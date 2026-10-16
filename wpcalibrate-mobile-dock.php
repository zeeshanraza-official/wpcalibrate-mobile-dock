<?php
/**
 * Plugin Name: WPCalibrate Mobile Dock
 * Plugin URI: https://wpcalibrate.com
 * Description: Fast, accessible, app-style bottom navigation system for mobile WordPress websites, with optional WooCommerce-aware navigation actions.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.2
 * Tested up to: 7.1.3
 * WC requires at least: 8.0
 * WC tested up to: 11.2.1
 * Author: WPCalibrate
 * Author URI: https://wpcalibrate.com
 * Text Domain: wpcalibrate-mobile-dock
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/zeeshanraza-official/wpcalibrate-mobile-dock
 * Primary Branch: main
 *
 * @package WPCalibrate\MobileDock
 */

defined( 'ABSPATH' ) || exit;

// Core plugin constants.
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_VERSION' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_VERSION', '1.0.0' );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_DB_VERSION' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_DB_VERSION', 100 );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_FILE' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_FILE', __FILE__ );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_PATH' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_URL' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_BASENAME' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_MIN_PHP' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_MIN_PHP', '8.2' );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_MIN_WP' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_MIN_WP', '6.0' );
}

// Environmental compatibility checks before boot.
if ( version_compare( PHP_VERSION, WPCALIBRATE_MOBILE_DOCK_MIN_PHP, '<' ) ) {
	add_action(
		'admin_notices',
		static function () {
			?>
			<div class="notice notice-error">
				<p>
					<?php
					printf(
						/* translators: 1: Plugin name, 2: Required PHP version, 3: Current PHP version */
						esc_html__( '%1$s requires PHP version %2$s or higher. Your server is running PHP %3$s. Please upgrade PHP to use this plugin.', 'wpcalibrate-mobile-dock' ),
						'<strong>' . esc_html__( 'WPCalibrate Mobile Dock', 'wpcalibrate-mobile-dock' ) . '</strong>',
						esc_html( WPCALIBRATE_MOBILE_DOCK_MIN_PHP ),
						esc_html( PHP_VERSION )
					);
					?>
				</p>
			</div>
			<?php
		}
	);
	return;
}

global $wp_version;
if ( isset( $wp_version ) && version_compare( $wp_version, WPCALIBRATE_MOBILE_DOCK_MIN_WP, '<' ) ) {
	add_action(
		'admin_notices',
		static function () use ( $wp_version ) {
			?>
			<div class="notice notice-error">
				<p>
					<?php
					printf(
						/* translators: 1: Plugin name, 2: Required WordPress version, 3: Current WordPress version */
						esc_html__( '%1$s requires WordPress version %2$s or higher. Your site is running WordPress %3$s. Please update WordPress to use this plugin.', 'wpcalibrate-mobile-dock' ),
						'<strong>' . esc_html__( 'WPCalibrate Mobile Dock', 'wpcalibrate-mobile-dock' ) . '</strong>',
						esc_html( WPCALIBRATE_MOBILE_DOCK_MIN_WP ),
						esc_html( $wp_version )
					);
					?>
				</p>
			</div>
			<?php
		}
	);
	return;
}

// Register PSR-4 style autoloader for WPCalibrate\MobileDock namespace.
spl_autoload_register(
	static function ( string $class ): void {
		$prefix   = 'WPCalibrate\\MobileDock\\';
		$base_dir = WPCALIBRATE_MOBILE_DOCK_PATH . 'src/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);

// Register lifecycle hooks.
register_activation_hook( __FILE__, [ 'WPCalibrate\\MobileDock\\Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'WPCalibrate\\MobileDock\\Plugin', 'deactivate' ] );

// Initialize plugin bootstrap.
\WPCalibrate\MobileDock\Bootstrap::init();
