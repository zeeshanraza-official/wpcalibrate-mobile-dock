<?php
/**
 * Plugin Bootstrap Initializer.
 *
 * @package WPCalibrate\MobileDock
 */

namespace WPCalibrate\MobileDock;

use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

class Bootstrap {

	/**
	 * Bootstraps the plugin.
	 */
	public static function init(): void {
		// Declare WooCommerce HPOS and Blocks compatibility safely.
		add_action( 'before_woocommerce_init', [ WooCommerceIntegration::class, 'declare_compatibility' ] );

		// Initialize core plugin singleton.
		Plugin::get_instance()->init();
	}
}
