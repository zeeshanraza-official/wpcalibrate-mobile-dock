<?php
/**
 * Asset Manager.
 *
 * Enqueues frontend and admin scripts and styles conditionally and defensively.
 *
 * @package WPCalibrate\MobileDock\Assets
 */

namespace WPCalibrate\MobileDock\Assets;

use WPCalibrate\MobileDock\Admin\AdminMenu;
use WPCalibrate\MobileDock\Admin\AdminScreens;
use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Navigation\SettingsRegistry;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

class AssetManager {

	public function __construct(
		private AdminScreens $admin_screens
	) {}

	public function init(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
	}

	/**
	 * Enqueue admin scripts and styles ONLY on this plugin's screen.
	 *
	 * @param string $hook_suffix
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		// Strict check: only on our submenu page
		if ( ! isset( $_GET['page'] ) || AdminMenu::SUBMENU_SLUG !== $_GET['page'] ) {
			return;
		}

		// WordPress Core Color Picker & Dashicons
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_script( 'wp-color-picker' );

		// Plugin Admin Assets
		wp_enqueue_style(
			'wpcalibrate-mobile-dock-admin',
			WPCALIBRATE_MOBILE_DOCK_URL . 'assets/admin/css/admin.css',
			[ 'wp-color-picker', 'dashicons' ],
			WPCALIBRATE_MOBILE_DOCK_VERSION
		);

		wp_enqueue_script(
			'wpcalibrate-mobile-dock-admin',
			WPCALIBRATE_MOBILE_DOCK_URL . 'assets/admin/js/admin.js',
			[ 'jquery', 'wp-color-picker' ],
			WPCALIBRATE_MOBILE_DOCK_VERSION,
			true
		);

		wp_localize_script(
			'wpcalibrate-mobile-dock-admin',
			'wpcalAdminData',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wpcal_admin_nonce' ),
				'strings' => [
					'confirmDelete' => __( 'Are you sure you want to remove this item?', 'wpcalibrate-mobile-dock' ),
				],
			]
		);
	}

	/**
	 * Enqueue frontend scripts and styles when an active dock is confirmed.
	 *
	 * @param NavigationConfig $config Active navigation config.
	 */
	public function enqueue_frontend_assets( NavigationConfig $config ): void {
		wp_enqueue_style(
			'wpcalibrate-mobile-dock',
			WPCALIBRATE_MOBILE_DOCK_URL . 'assets/frontend/css/mobile-dock.css',
			[],
			WPCALIBRATE_MOBILE_DOCK_VERSION
		);

		// Output Custom CSS if present
		$custom_css = SettingsRegistry::get( 'custom_css', '' );
		if ( ! empty( $custom_css ) ) {
			wp_add_inline_style( 'wpcalibrate-mobile-dock', $custom_css );
		}

		wp_enqueue_script(
			'wpcalibrate-mobile-dock',
			WPCALIBRATE_MOBILE_DOCK_URL . 'assets/frontend/js/mobile-dock.js',
			[],
			WPCALIBRATE_MOBILE_DOCK_VERSION,
			true
		);

		wp_localize_script(
			'wpcalibrate-mobile-dock',
			'wpcalMobileDockConfig',
			[
				'breakpoint'       => (int) $config->breakpoint,
				'showOnTablet'     => (bool) $config->show_on_tablet,
				'tabletBreakpoint' => (int) $config->tablet_breakpoint,
				'isWooActive'      => (bool) WooCommerceIntegration::is_active(),
			]
		);
	}
}
