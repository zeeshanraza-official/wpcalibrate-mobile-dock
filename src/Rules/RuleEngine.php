<?php
/**
 * Visibility Rule Engine.
 *
 * Deterministically resolves the winning NavigationConfig for any request.
 *
 * @package WPCalibrate\MobileDock\Rules
 */

namespace WPCalibrate\MobileDock\Rules;

use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Domain\VisibilityRule;

defined( 'ABSPATH' ) || exit;

class RuleEngine {

	public const SPECIFICITY_EXPLICIT_CONTENT = 400;
	public const SPECIFICITY_WOOCOMMERCE       = 300;
	public const SPECIFICITY_AUTHENTICATION   = 200;
	public const SPECIFICITY_ENTIRE_SITE      = 100;
	public const SPECIFICITY_DEFAULT_FALLBACK = 50;

	/**
	 * Resolves the single winning navigation configuration for the current view.
	 *
	 * @param NavigationConfig[] $configs Available configurations.
	 * @param array|null         $context Optional mock context for testing.
	 * @return NavigationConfig|null
	 */
	public function resolve( array $configs, ?array $context = null ): ?NavigationConfig {
		if ( empty( $configs ) ) {
			return null;
		}

		$ctx = $context ?? $this->get_current_context();

		// Check global excluded pages first.
		if ( ! empty( $ctx['page_id'] ) && in_array( $ctx['page_id'], $ctx['global_excluded_pages'] ?? [], true ) ) {
			return null;
		}

		$matches = [];

		foreach ( $configs as $config ) {
			if ( 'publish' !== $config->status ) {
				continue;
			}

			$match_score = $this->evaluate_config( $config, $ctx );
			if ( false !== $match_score ) {
				$matches[] = [
					'config'   => $config,
					'score'    => $match_score,
					'priority' => $config->priority,
				];
			}
		}

		if ( empty( $matches ) ) {
			return null;
		}

		// Sort by score DESC (highest specificity wins), then by priority ASC (lower number wins).
		usort(
			$matches,
			static function ( array $a, array $b ): int {
				if ( $a['score'] !== $b['score'] ) {
					return $b['score'] <=> $a['score'];
				}
				return $a['priority'] <=> $b['priority'];
			}
		);

		$winning_config = $matches[0]['config'];

		/**
		 * Filter the winning navigation configuration.
		 *
		 * @param NavigationConfig|null $winning_config
		 * @param array                 $ctx Current request context.
		 */
		return apply_filters( 'wpcalibrate_mobile_dock_resolved_navigation', $winning_config, $ctx );
	}

	/**
	 * Determines if a navigation item is visible for the current context.
	 *
	 * @param NavigationItem $item
	 * @param array|null     $context
	 * @return bool
	 */
	public function is_item_visible( NavigationItem $item, ?array $context = null ): bool {
		if ( ! $item->enabled ) {
			return false;
		}

		$ctx = $context ?? $this->get_current_context();

		// User authentication check.
		if ( 'logged_in' === $item->user_visibility && ! $ctx['is_logged_in'] ) {
			return false;
		}
		if ( 'logged_out' === $item->user_visibility && $ctx['is_logged_in'] ) {
			return false;
		}

		// WooCommerce items check.
		$is_wc_action = str_starts_with( $item->action_type, 'wc_' );
		if ( $is_wc_action && empty( $ctx['is_wc_active'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Evaluate a single configuration against the context.
	 *
	 * @param NavigationConfig $config
	 * @param array            $ctx
	 * @return int|false Specificity score or false if not matching.
	 */
	public function evaluate_config( NavigationConfig $config, array $ctx ): int|false {
		$rules = $config->visibility_rules;

		// If no rules defined:
		if ( empty( $rules ) ) {
			return $config->is_default ? self::SPECIFICITY_DEFAULT_FALLBACK : self::SPECIFICITY_ENTIRE_SITE;
		}

		$has_include_rule = false;
		$matched_include  = false;
		$highest_score    = 0;

		foreach ( $rules as $rule ) {
			if ( VisibilityRule::OPERATOR_INCLUDE === $rule->operator ) {
				$has_include_rule = true;
			}

			// Check user auth requirement for this rule.
			if ( VisibilityRule::AUTH_LOGGED_IN === $rule->user_auth && ! $ctx['is_logged_in'] ) {
				continue;
			}
			if ( VisibilityRule::AUTH_LOGGED_OUT === $rule->user_auth && $ctx['is_logged_in'] ) {
				continue;
			}

			$condition_matches = $this->evaluate_condition( $rule, $ctx );

			if ( VisibilityRule::OPERATOR_EXCLUDE === $rule->operator ) {
				// Exclusions take absolute precedence.
				if ( $condition_matches ) {
					return false;
				}
			} else {
				if ( $condition_matches ) {
					$matched_include = true;
					$score           = $this->calculate_specificity( $rule );
					if ( $score > $highest_score ) {
						$highest_score = $score;
					}
				}
			}
		}

		// If there were explicit include rules, at least one MUST match.
		if ( $has_include_rule && ! $matched_include ) {
			return false;
		}

		if ( $matched_include ) {
			return $highest_score;
		}

		// If only exclude rules were defined and none matched, applies as entire site.
		return $config->is_default ? self::SPECIFICITY_DEFAULT_FALLBACK : self::SPECIFICITY_ENTIRE_SITE;
	}

	/**
	 * Evaluate a single condition against current context.
	 */
	private function evaluate_condition( VisibilityRule $rule, array $ctx ): bool {
		switch ( $rule->condition_type ) {
			case VisibilityRule::TYPE_ENTIRE_SITE:
				return true;

			case VisibilityRule::TYPE_FRONT_PAGE:
				return ! empty( $ctx['is_front_page'] );

			case VisibilityRule::TYPE_BLOG_PAGE:
				return ! empty( $ctx['is_blog_page'] );

			case VisibilityRule::TYPE_PAGES:
				if ( empty( $ctx['is_page'] ) || empty( $ctx['page_id'] ) ) {
					return false;
				}
				return empty( $rule->target_ids ) || in_array( $ctx['page_id'], $rule->target_ids, true );

			case VisibilityRule::TYPE_POSTS:
				if ( empty( $ctx['is_single'] ) || empty( $ctx['post_id'] ) ) {
					return false;
				}
				return empty( $rule->target_ids ) || in_array( $ctx['post_id'], $rule->target_ids, true );

			case VisibilityRule::TYPE_POST_TYPES:
				return ! empty( $ctx['post_type'] ) && in_array( $ctx['post_type'], $rule->target_ids, true );

			case VisibilityRule::TYPE_TAXONOMIES:
				if ( empty( $ctx['term_ids'] ) ) {
					return false;
				}
				return ! empty( array_intersect( $ctx['term_ids'], $rule->target_ids ) );

			case VisibilityRule::TYPE_WC_SHOP:
				return ! empty( $ctx['is_wc_shop'] );

			case VisibilityRule::TYPE_WC_PRODUCTS:
				if ( empty( $ctx['is_wc_product'] ) ) {
					return false;
				}
				return empty( $rule->target_ids ) || in_array( $ctx['post_id'], $rule->target_ids, true );

			case VisibilityRule::TYPE_WC_CATEGORIES:
				if ( empty( $ctx['is_wc_category'] ) ) {
					return false;
				}
				return empty( $rule->target_ids ) || in_array( $ctx['wc_category_id'], $rule->target_ids, true );

			case VisibilityRule::TYPE_WC_CART:
				return ! empty( $ctx['is_wc_cart'] );

			case VisibilityRule::TYPE_WC_CHECKOUT:
				return ! empty( $ctx['is_wc_checkout'] );

			case VisibilityRule::TYPE_WC_ACCOUNT:
				return ! empty( $ctx['is_wc_account'] );

			default:
				return false;
		}
	}

	/**
	 * Calculate specificity score for an include rule.
	 */
	private function calculate_specificity( VisibilityRule $rule ): int {
		// Explicit single targets (specific page/post/category IDs) have highest score
		if ( in_array( $rule->condition_type, [ VisibilityRule::TYPE_PAGES, VisibilityRule::TYPE_POSTS, VisibilityRule::TYPE_WC_PRODUCTS, VisibilityRule::TYPE_WC_CATEGORIES, VisibilityRule::TYPE_TAXONOMIES ], true ) && ! empty( $rule->target_ids ) ) {
			return self::SPECIFICITY_EXPLICIT_CONTENT;
		}

		// WooCommerce sections
		if ( str_starts_with( $rule->condition_type, 'wc_' ) ) {
			return self::SPECIFICITY_WOOCOMMERCE;
		}

		// Front page / blog page
		if ( in_array( $rule->condition_type, [ VisibilityRule::TYPE_FRONT_PAGE, VisibilityRule::TYPE_BLOG_PAGE ], true ) ) {
			return self::SPECIFICITY_EXPLICIT_CONTENT - 50;
		}

		// Authentication specific
		if ( VisibilityRule::AUTH_ALL !== $rule->user_auth ) {
			return self::SPECIFICITY_AUTHENTICATION;
		}

		return self::SPECIFICITY_ENTIRE_SITE;
	}

	/**
	 * Build current WordPress request context.
	 *
	 * @return array
	 */
	public function get_current_context(): array {
		$context = [
			'is_logged_in'          => is_user_logged_in(),
			'is_front_page'         => is_front_page(),
			'is_blog_page'          => is_home() && ! is_front_page(),
			'is_page'               => is_page(),
			'is_single'             => is_single(),
			'page_id'               => is_page() ? get_the_ID() : 0,
			'post_id'               => is_singular() ? get_the_ID() : 0,
			'post_type'             => get_post_type() ?: '',
			'term_ids'              => is_tax() || is_category() || is_tag() ? [ get_queried_object_id() ] : [],
			'is_wc_active'          => class_exists( 'WooCommerce' ),
			'is_wc_shop'            => function_exists( 'is_shop' ) && is_shop(),
			'is_wc_product'         => function_exists( 'is_product' ) && is_product(),
			'is_wc_category'        => function_exists( 'is_product_category' ) && is_product_category(),
			'wc_category_id'        => function_exists( 'is_product_category' ) && is_product_category() ? get_queried_object_id() : 0,
			'is_wc_cart'            => function_exists( 'is_cart' ) && is_cart(),
			'is_wc_checkout'        => function_exists( 'is_checkout' ) && is_checkout(),
			'is_wc_account'         => function_exists( 'is_account_page' ) && is_account_page(),
			'global_excluded_pages' => (array) \WPCalibrate\MobileDock\Navigation\SettingsRegistry::get( 'global_excluded_pages', [] ),
		];

		return apply_filters( 'wpcalibrate_mobile_dock_context', $context );
	}
}
