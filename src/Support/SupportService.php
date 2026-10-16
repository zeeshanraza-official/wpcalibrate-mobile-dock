<?php
/**
 * Support and Diagnostics Service.
 *
 * @package WPCalibrate\MobileDock\Support
 */

namespace WPCalibrate\MobileDock\Support;

use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

class SupportService {

	public const EMAIL       = 'support@wpcalibrate.com';
	public const WEBSITE     = 'https://wpcalibrate.com';
	public const MARKETPLACE = 'https://marketplace.wpcalibrate.com/';
	public const PHONE       = '+447474795976';

	public static function get_system_report(): array {
		global $wp_version;

		$theme = wp_get_theme();

		return [
			'Plugin Version'      => WPCALIBRATE_MOBILE_DOCK_VERSION,
			'PHP Version'         => PHP_VERSION,
			'WordPress Version'   => $wp_version ?? 'Unknown',
			'WooCommerce Active'  => WooCommerceIntegration::is_active() ? 'Yes' : 'No',
			'Active Theme'        => $theme->get( 'Name' ) . ' (' . $theme->get( 'Version' ) . ')',
			'Multisite'           => is_multisite() ? 'Yes' : 'No',
			'Server Software'     => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
			'Memory Limit'        => ini_get( 'memory_limit' ),
		];
	}
}
