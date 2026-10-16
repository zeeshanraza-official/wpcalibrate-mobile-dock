<?php
/**
 * Menu and Permissions Integration Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Integration
 */

namespace WPCalibrate\MobileDock\Tests\Integration;

use WPCalibrate\MobileDock\Admin\AdminMenu;
use WPCalibrate\MobileDock\Admin\AdminScreens;
use WPCalibrate\MobileDock\Licensing\UnconfiguredLicenseProvider;
use WPCalibrate\MobileDock\Navigation\NavigationRepository;
use WPMockState;

class MenuAndPermissionTest {

	public function test_unauthorized_user_permission_check(): void {
		WPMockState::reset();
		$screens = new AdminScreens( new NavigationRepository(), new UnconfiguredLicenseProvider() );

		// Capability is manage_options
		assert( $screens->get_capability() === 'manage_options', 'Default capability must be manage_options' );

		// User without manage_options
		WPMockState::$current_user_cap = 'read';
		assert( ! current_user_can( $screens->get_capability() ), 'Subscriber cannot manage options' );

		// Admin user
		WPMockState::$current_user_cap = 'manage_options';
		assert( current_user_can( $screens->get_capability() ), 'Administrator can manage options' );
	}

	public function test_nonce_verification_blocks_unauthorized_requests(): void {
		WPMockState::reset();

		$valid_nonce = wp_create_nonce( 'wpcal_save_settings_nonce' );
		assert( wp_verify_nonce( $valid_nonce, 'wpcal_save_settings_nonce' ), 'Valid nonce must pass' );
		assert( ! wp_verify_nonce( 'forged_fake_nonce', 'wpcal_save_settings_nonce' ), 'Fake nonce must be rejected' );
		assert( ! wp_verify_nonce( $valid_nonce, 'different_action' ), 'Nonce for different action must be rejected' );
	}

	public function test_menu_slug_isolation(): void {
		assert( AdminMenu::PARENT_SLUG === 'wpcalibrate', 'Parent slug must be shared wpcalibrate' );
		assert( AdminMenu::SUBMENU_SLUG === 'wpcalibrate-mobile-dock', 'Submenu slug must be unique to mobile dock' );
	}
}
