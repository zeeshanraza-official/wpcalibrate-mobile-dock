<?php
/**
 * Lifecycle and Data Retention Integration Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Integration
 */

namespace WPCalibrate\MobileDock\Tests\Integration;

use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Navigation\NavigationRepository;
use WPCalibrate\MobileDock\Navigation\SettingsRegistry;
use WPCalibrate\MobileDock\Plugin;
use WPMockState;

class LifecycleAndRetentionTest {

	public function test_activation_and_reactivation_preserves_data(): void {
		WPMockState::reset();
		$repo = new NavigationRepository();

		// Initial activation
		Plugin::activate();
		$docks = $repo->get_all( true );
		assert( count( $docks ) === 1, 'Starter dock created on activation' );

		// User creates a custom dock
		$custom_dock = new NavigationConfig(
			id: 0,
			title: 'User Custom Dock',
			items: [ new NavigationItem( id: 'c1', label: 'Custom' ) ]
		);
		$repo->save( $custom_dock );
		assert( count( $repo->get_all( true ) ) === 2, 'Two docks exist' );

		// Deactivate plugin
		Plugin::deactivate();
		assert( count( $repo->get_all( true ) ) === 2, 'Deactivation must preserve docks' );

		// Reactivate plugin
		Plugin::activate();
		assert( count( $repo->get_all( true ) ) === 2, 'Reactivation must NOT overwrite existing docks' );
	}

	public function test_uninstall_retention_policy(): void {
		WPMockState::reset();
		$repo = new NavigationRepository();

		Plugin::activate();
		assert( count( $repo->get_all( true ) ) >= 1, 'Dock exists' );

		// Case 1: Retention policy OFF (default)
		SettingsRegistry::update( [ 'delete_data_on_uninstall' => false ] );

		// Simulate uninstall.php execution
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', true );
		}
		require WPCALIBRATE_MOBILE_DOCK_PATH . 'uninstall.php';

		assert( count( $repo->get_all( true ) ) >= 1, 'Uninstall with retention OFF must leave docks intact' );
		assert( ! empty( get_option( SettingsRegistry::OPTION_NAME ) ), 'Uninstall with retention OFF must leave options intact' );

		// Case 2: Retention policy ON
		SettingsRegistry::update( [ 'delete_data_on_uninstall' => true ] );
		require WPCALIBRATE_MOBILE_DOCK_PATH . 'uninstall.php';

		assert( count( $repo->get_all( true ) ) === 0, 'Uninstall with retention ON must delete all plugin docks' );
		assert( empty( get_option( SettingsRegistry::OPTION_NAME ) ), 'Uninstall with retention ON must delete plugin options' );
	}
}
