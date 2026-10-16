<?php
/**
 * WooCommerce Integration Integration Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Integration
 */

namespace WPCalibrate\MobileDock\Tests\Integration;

use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Rules\RuleEngine;
use WPCalibrate\MobileDock\WooCommerce\CartBadgeService;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;
use WPMockState;

class WooCommerceIntegrationTest {

	public function test_woocommerce_active_behavior(): void {
		WPMockState::reset();
		WPMockState::$wc_active = true;
		WPMockState::$cart_count = 3;

		// Mock WooCommerce class existence
		if ( ! class_exists( 'WooCommerce' ) ) {
			eval( 'class WooCommerce {}' );
		}

		assert( WooCommerceIntegration::is_active() === true, 'WooCommerce must be detected as active' );
		assert( WooCommerceIntegration::get_cart_count() === 3, 'Cart count must return 3' );
		assert( WooCommerceIntegration::get_cart_url() === 'https://example.com/cart/', 'Cart URL must resolve' );
		assert( WooCommerceIntegration::get_shop_url() === 'https://example.com/shop/', 'Shop URL must resolve' );

		// Cart Badge markup
		$badge_service = new CartBadgeService();
		$badge_html = $badge_service->render_badge_wrapper();
		assert( str_contains( $badge_html, 'data-cart-count="3"' ), 'Badge wrapper must include cart count data attribute' );
		assert( str_contains( $badge_html, '3' ), 'Badge must display count 3' );
		assert( str_contains( $badge_html, 'screen-reader-text' ), 'Badge must include screen-reader accessible label' );

		// AJAX Cart Fragments filter
		$fragments = $badge_service->add_cart_fragment( [] );
		assert( isset( $fragments['span.wpcal-dock-badge-wrapper'] ), 'Cart fragments must provide span.wpcal-dock-badge-wrapper' );
	}

	public function test_woocommerce_inactive_graceful_fallback(): void {
		WPMockState::reset();
		WPMockState::$wc_active = false;

		$engine = new RuleEngine();
		$wc_cart_item = new NavigationItem(
			id: 'cart',
			label: 'Cart',
			action_type: NavigationItem::ACTION_WC_CART
		);

		// With WooCommerce inactive in context:
		$is_visible = $engine->is_item_visible( $wc_cart_item, [ 'is_logged_in' => false, 'is_wc_active' => false ] );
		assert( $is_visible === false, 'WooCommerce items must NOT be visible when WooCommerce is inactive' );
	}
}
