<?php
/**
 * Visibility Rule Value Object.
 *
 * @package WPCalibrate\MobileDock\Domain
 */

namespace WPCalibrate\MobileDock\Domain;

defined( 'ABSPATH' ) || exit;

class VisibilityRule {

	public const TYPE_ENTIRE_SITE    = 'entire_site';
	public const TYPE_FRONT_PAGE     = 'front_page';
	public const TYPE_BLOG_PAGE      = 'blog_page';
	public const TYPE_PAGES          = 'pages';
	public const TYPE_POSTS          = 'posts';
	public const TYPE_POST_TYPES     = 'post_types';
	public const TYPE_TAXONOMIES     = 'taxonomies';
	public const TYPE_WC_SHOP        = 'wc_shop';
	public const TYPE_WC_PRODUCTS    = 'wc_products';
	public const TYPE_WC_CATEGORIES  = 'wc_categories';
	public const TYPE_WC_CART        = 'wc_cart';
	public const TYPE_WC_CHECKOUT    = 'wc_checkout';
	public const TYPE_WC_ACCOUNT     = 'wc_account';

	public const OPERATOR_INCLUDE = 'include';
	public const OPERATOR_EXCLUDE = 'exclude';

	public const AUTH_ALL        = 'all';
	public const AUTH_LOGGED_IN  = 'logged_in';
	public const AUTH_LOGGED_OUT = 'logged_out';

	public function __construct(
		public string $condition_type = self::TYPE_ENTIRE_SITE,
		public string $operator = self::OPERATOR_INCLUDE,
		public array $target_ids = [],
		public string $user_auth = self::AUTH_ALL
	) {}

	public static function from_array( array $data ): self {
		$type = sanitize_key( $data['condition_type'] ?? self::TYPE_ENTIRE_SITE );
		$operator = in_array( $data['operator'] ?? self::OPERATOR_INCLUDE, [ self::OPERATOR_INCLUDE, self::OPERATOR_EXCLUDE ], true )
			? (string) $data['operator']
			: self::OPERATOR_INCLUDE;

		$raw_ids    = is_array( $data['target_ids'] ?? null ) ? $data['target_ids'] : [];
		$target_ids = [];
		foreach ( $raw_ids as $id ) {
			if ( is_numeric( $id ) ) {
				$target_ids[] = absint( $id );
			} elseif ( is_string( $id ) ) {
				$target_ids[] = sanitize_key( $id );
			}
		}

		$user_auth = in_array( $data['user_auth'] ?? self::AUTH_ALL, [ self::AUTH_ALL, self::AUTH_LOGGED_IN, self::AUTH_LOGGED_OUT ], true )
			? (string) $data['user_auth']
			: self::AUTH_ALL;

		return new self( $type, $operator, $target_ids, $user_auth );
	}

	public function to_array(): array {
		return [
			'condition_type' => $this->condition_type,
			'operator'       => $this->operator,
			'target_ids'     => $this->target_ids,
			'user_auth'      => $this->user_auth,
		];
	}
}
