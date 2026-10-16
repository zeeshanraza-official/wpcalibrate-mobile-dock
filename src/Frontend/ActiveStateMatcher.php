<?php
/**
 * Active State Matcher for Mobile Dock items.
 *
 * Provides accurate active-state detection without false positive substring matching.
 *
 * @package WPCalibrate\MobileDock\Frontend
 */

namespace WPCalibrate\MobileDock\Frontend;

use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

class ActiveStateMatcher {

	/**
	 * Checks if a given item matches current request.
	 *
	 * @param NavigationItem $item
	 * @param array|null     $context Optional context for unit testing.
	 * @return bool
	 */
	public function is_active( NavigationItem $item, ?array $context = null ): bool {
		$ctx = $context ?? $this->get_request_context();

		switch ( $item->action_type ) {
			case NavigationItem::ACTION_HOME:
				return ! empty( $ctx['is_front_page'] );

			case NavigationItem::ACTION_BLOG:
				return ! empty( $ctx['is_blog_page'] );

			case NavigationItem::ACTION_PAGE:
				$target_id = absint( $item->target );
				return ! empty( $ctx['is_page'] ) && (int) $ctx['page_id'] === $target_id;

			case NavigationItem::ACTION_POST:
				$target_id = absint( $item->target );
				return ! empty( $ctx['is_single'] ) && (int) $ctx['post_id'] === $target_id;

			case NavigationItem::ACTION_WC_SHOP:
				return ! empty( $ctx['is_wc_shop'] );

			case NavigationItem::ACTION_WC_CATEGORY:
				$target_id = absint( $item->target );
				return ! empty( $ctx['is_wc_category'] ) && (int) $ctx['wc_category_id'] === $target_id;

			case NavigationItem::ACTION_WC_CART:
				return ! empty( $ctx['is_wc_cart'] );

			case NavigationItem::ACTION_WC_ACCOUNT:
				return ! empty( $ctx['is_wc_account'] );

			case NavigationItem::ACTION_CUSTOM_URL:
				return $this->match_custom_url( $item->target, $ctx['current_url'] ?? '' );

			default:
				return false;
		}
	}

	/**
	 * Compare custom internal URL strictly against current URL.
	 */
	public function match_custom_url( string $target_url, string $current_url ): bool {
		if ( empty( $target_url ) || empty( $current_url ) ) {
			return false;
		}

		$target_path  = $this->normalize_url_path( $target_url );
		$current_path = $this->normalize_url_path( $current_url );

		// Do not activate external URLs
		$target_host  = wp_parse_url( $target_url, PHP_URL_HOST );
		$current_host = wp_parse_url( $current_url, PHP_URL_HOST );

		if ( $target_host && $current_host && strtolower( $target_host ) !== strtolower( $current_host ) ) {
			return false;
		}

		return ( '' !== $target_path && $target_path === $current_path );
	}

	private function normalize_url_path( string $url ): string {
		$parsed = wp_parse_url( $url );
		$path   = $parsed['path'] ?? '/';
		return untrailingslashit( strtolower( $path ) );
	}

	private function get_request_context(): array {
		global $wp;

		$current_url = home_url( add_query_arg( [], $wp->request ?? '' ) );

		return [
			'is_front_page'  => is_front_page(),
			'is_blog_page'   => is_home() && ! is_front_page(),
			'is_page'        => is_page(),
			'page_id'        => is_page() ? get_the_ID() : 0,
			'is_single'      => is_single(),
			'post_id'        => is_singular() ? get_the_ID() : 0,
			'is_wc_shop'     => function_exists( 'is_shop' ) && is_shop(),
			'is_wc_category' => function_exists( 'is_product_category' ) && is_product_category(),
			'wc_category_id' => function_exists( 'is_product_category' ) && is_product_category() ? get_queried_object_id() : 0,
			'is_wc_cart'     => function_exists( 'is_cart' ) && is_cart(),
			'is_wc_account'  => function_exists( 'is_account_page' ) && is_account_page(),
			'current_url'    => $current_url,
		];
	}
}
