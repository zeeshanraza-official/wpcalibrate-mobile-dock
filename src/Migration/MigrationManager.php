<?php
/**
 * Migration & Version Manager.
 *
 * Handles database schema versions and idempotent upgrade routines.
 *
 * @package WPCalibrate\MobileDock\Migration
 */

namespace WPCalibrate\MobileDock\Migration;

use WPCalibrate\MobileDock\Domain\AppearanceSettings;
use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Domain\VisibilityRule;
use WPCalibrate\MobileDock\Navigation\NavigationRepository;
use WPCalibrate\MobileDock\Navigation\SettingsRegistry;

defined( 'ABSPATH' ) || exit;

class MigrationManager {

	public const OPTION_DB_VERSION = 'wpcalibrate_mobile_dock_db_version';

	public function __construct( private NavigationRepository $repository ) {}

	/**
	 * Run migrations if needed.
	 */
	public function maybe_migrate(): void {
		$current_db_version = (int) get_option( self::OPTION_DB_VERSION, 0 );
		$target_db_version  = (int) WPCALIBRATE_MOBILE_DOCK_DB_VERSION;

		if ( $current_db_version >= $target_db_version ) {
			return;
		}

		// Initial install / version 100
		if ( $current_db_version < 100 ) {
			$this->migrate_to_100();
		}

		update_option( self::OPTION_DB_VERSION, $target_db_version );
	}

	/**
	 * Initial schema setup (v100).
	 */
	private function migrate_to_100(): void {
		// Set default settings if not already present.
		$existing_settings = get_option( SettingsRegistry::OPTION_NAME, null );
		if ( null === $existing_settings ) {
			update_option( SettingsRegistry::OPTION_NAME, SettingsRegistry::get_defaults() );
		}

		// Create default starter dock if no docks exist.
		$existing_docks = $this->repository->get_all( true );
		if ( empty( $existing_docks ) ) {
			$this->create_starter_dock();
		}
	}

	/**
	 * Create default starter navigation dock safely without assuming WooCommerce.
	 */
	public function create_starter_dock(): int {
		$items = [
			new NavigationItem(
				id: 'item_home',
				enabled: true,
				label: __( 'Home', 'wpcalibrate-mobile-dock' ),
				icon: 'home',
				action_type: NavigationItem::ACTION_HOME
			),
			new NavigationItem(
				id: 'item_search',
				enabled: true,
				label: __( 'Search', 'wpcalibrate-mobile-dock' ),
				icon: 'search',
				action_type: NavigationItem::ACTION_SEARCH,
				is_center_raised: true
			),
			new NavigationItem(
				id: 'item_blog',
				enabled: true,
				label: __( 'Blog', 'wpcalibrate-mobile-dock' ),
				icon: 'compass',
				action_type: NavigationItem::ACTION_BLOG
			),
			new NavigationItem(
				id: 'item_contact',
				enabled: true,
				label: __( 'Call', 'wpcalibrate-mobile-dock' ),
				icon: 'phone',
				action_type: NavigationItem::ACTION_PHONE,
				target: '+447474795976'
			),
		];

		$starter_dock = new NavigationConfig(
			id: 0,
			title: __( 'Default Mobile Dock', 'wpcalibrate-mobile-dock' ),
			status: 'publish',
			is_default: true,
			priority: 1,
			items: $items,
			appearance: new AppearanceSettings(),
			visibility_rules: [
				new VisibilityRule(
					condition_type: VisibilityRule::TYPE_ENTIRE_SITE,
					operator: VisibilityRule::OPERATOR_INCLUDE
				),
			]
		);

		return $this->repository->save( $starter_dock );
	}
}
