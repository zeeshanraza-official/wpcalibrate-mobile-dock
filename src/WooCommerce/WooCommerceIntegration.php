<?php
/**
 * WooCommerce Integration Service.
 *
 * Handles safe, optional WooCommerce detection, endpoint resolution,
 * and HPOS / blocks compatibility declarations.
 *
 * @package WPCalibrate\MobileDock\WooCommerce
 */

namespace WPCalibrate\MobileDock\WooCommerce;

defined( 'ABSPATH' ) || exit;

class WooCommerceIntegration {

	/**
	 * Check if WooCommerce is active and fully loaded.
	 *
	 * @return bool
	 */
	public static function is_active(): bool {
		return class_exists( 'WooCommerce' ) && function_exists( 'WC' );
	}

	/**
	 * Declare HPOS and Blocks compatibility safely.
	 */
	public static function declare_compatibility(): void {
		if ( ! class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_FILE' ) ) {
			return;
		}

		// HPOS (Custom Order Tables) compatibility.
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			WPCALIBRATE_MOBILE_DOCK_FILE,
			true
		);

		// Cart and Checkout Blocks compatibility.
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'cart_checkout_blocks',
			WPCALIBRATE_MOBILE_DOCK_FILE,
			true
		);
	}

	/**
	 * Get WooCommerce Shop URL safely.
	 *
	 * @return string
	 */
	public static function get_shop_url(): string {
		if ( ! self::is_active() || ! function_exists( 'wc_get_page_permalink' ) ) {
			return home_url( '/' );
		}
		$url = wc_get_page_permalink( 'shop' );
		return $url ? $url : home_url( '/' );
	}

	/**
	 * Get WooCommerce Cart URL safely.
	 *
	 * @return string
	 */
	public static function get_cart_url(): string {
		if ( ! self::is_active() || ! function_exists( 'wc_get_cart_url' ) ) {
			return home_url( '/' );
		}
		return wc_get_cart_url();
	}

	/**
	 * Get WooCommerce My Account URL safely.
	 *
	 * @return string
	 */
	public static function get_account_url(): string {
		if ( ! self::is_active() || ! function_exists( 'wc_get_page_permalink' ) ) {
			return home_url( '/' );
		}
		$url = wc_get_page_permalink( 'myaccount' );
		return $url ? $url : wp_login_url();
	}

	/**
	 * Get WooCommerce Category URL safely.
	 *
	 * @param int $term_id
	 * @return string
	 */
	public static function get_category_url( int $term_id ): string {
		if ( ! self::is_active() || $term_id <= 0 ) {
			return home_url( '/' );
		}
		$link = get_term_link( $term_id, 'product_cat' );
		return is_wp_error( $link ) ? home_url( '/' ) : $link;
	}

	/**
	 * Get current cart item count safely.
	 *
	 * @return int
	 */
	public static function get_cart_count(): int {
		if ( ! self::is_active() ) {
			return 0;
		}

		$wc = WC();
		if ( ! $wc || ! isset( $wc->cart ) || ! is_object( $wc->cart ) ) {
			return 0;
		}

		return (int) $wc->cart->get_cart_contents_count();
	}

	/**
	 * Get product categories for admin select options.
	 *
	 * @return array<int, string>
	 */
	public static function get_categories_list(): array {
		if ( ! self::is_active() || ! taxonomy_exists( 'product_cat' ) ) {
			return [];
		}

		$terms = get_terms(
			[
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			]
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return [];
		}

		$list = [];
		foreach ( $terms as $term ) {
			$list[ $term->term_id ] = $term->name;
		}
		return $list;
	}
}
