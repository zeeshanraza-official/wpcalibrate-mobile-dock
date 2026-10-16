<?php
/**
 * Migration Manager Unit Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Unit
 */

namespace WPCalibrate\MobileDock\Tests\Unit;

use WPCalibrate\MobileDock\Migration\MigrationManager;
use WPCalibrate\MobileDock\Navigation\NavigationRepository;
use WPMockState;

class MigrationManagerTest {

	public function test_initial_migration_creates_starter_dock(): void {
		WPMockState::reset();

		$repo = new NavigationRepository();
		$mig  = new MigrationManager( $repo );

		assert( get_option( MigrationManager::OPTION_DB_VERSION, 0 ) === 0, 'Initial DB version should be 0' );
		assert( empty( $repo->get_all( true ) ), 'Initial docks should be empty' );

		$mig->maybe_migrate();

		assert( get_option( MigrationManager::OPTION_DB_VERSION, 0 ) === 100, 'DB version must be set to 100' );
		$docks = $repo->get_all( true );
		assert( count( $docks ) === 1, 'Starter dock must be created on initial migration' );
		assert( $docks[0]->is_default === true, 'Starter dock must be marked as default' );

		// Idempotency: run again
		$mig->maybe_migrate();
		$docks_after = $repo->get_all( true );
		assert( count( $docks_after ) === 1, 'Re-running migration must not create duplicate starter docks' );
	}
}
