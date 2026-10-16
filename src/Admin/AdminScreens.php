<?php
/**
 * Admin Screens Controller.
 *
 * Handles tabs, form submissions, nonces, permissions, and template rendering.
 *
 * @package WPCalibrate\MobileDock\Admin
 */

namespace WPCalibrate\MobileDock\Admin;

use WPCalibrate\MobileDock\Domain\AppearanceSettings;
use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Domain\VisibilityRule;
use WPCalibrate\MobileDock\Icons\IconRegistry;
use WPCalibrate\MobileDock\Licensing\LicenseProviderInterface;
use WPCalibrate\MobileDock\Navigation\NavigationRepository;
use WPCalibrate\MobileDock\Navigation\SettingsRegistry;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

class AdminScreens {

	private string $hook_suffix = '';
	private array $notices = [];

	public function __construct(
		private NavigationRepository $repository,
		private LicenseProviderInterface $license_provider
	) {}

	public function set_hook_suffix( string $suffix ): void {
		$this->hook_suffix = $suffix;
	}

	public function get_hook_suffix(): string {
		return $this->hook_suffix;
	}

	public function get_capability(): string {
		return apply_filters( 'wpcalibrate_mobile_dock_capability', 'manage_options' );
	}

	public function init(): void {
		add_action( 'admin_init', [ $this, 'handle_actions' ] );
	}

	/**
	 * Handle state-changing POST and GET actions.
	 */
	public function handle_actions(): void {
		// Only handle when on our plugin page
		if ( ! isset( $_GET['page'] ) || AdminMenu::SUBMENU_SLUG !== $_GET['page'] ) {
			return;
		}

		if ( ! current_user_can( $this->get_capability() ) ) {
			return;
		}

		// Handle POST submissions
		if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
			$action = sanitize_key( $_POST['wpcal_action'] ?? '' );

			if ( 'save_settings' === $action ) {
				$this->handle_save_settings();
			} elseif ( 'reset_settings' === $action ) {
				$this->handle_reset_settings();
			} elseif ( 'save_dock' === $action ) {
				$this->handle_save_dock();
			}
		}

		// Handle GET actions (delete, duplicate, set default)
		if ( isset( $_GET['action'] ) ) {
			$action  = sanitize_key( $_GET['action'] );
			$dock_id = absint( $_GET['dock_id'] ?? 0 );

			if ( 'delete' === $action && $dock_id > 0 ) {
				$this->handle_delete_dock( $dock_id );
			} elseif ( 'duplicate' === $action && $dock_id > 0 ) {
				$this->handle_duplicate_dock( $dock_id );
			} elseif ( 'set_default' === $action && $dock_id > 0 ) {
				$this->handle_set_default_dock( $dock_id );
			}
		}
	}

	private function handle_save_settings(): void {
		check_admin_referer( 'wpcal_save_settings_nonce', '_wpcal_nonce' );

		$tab = sanitize_key( $_POST['current_tab'] ?? 'appearance' );
		$input = $_POST['settings'] ?? [];

		if ( ! is_array( $input ) ) {
			$input = [];
		}

		// Fetch existing settings and merge
		$existing = SettingsRegistry::get_all();
		$merged   = array_merge( $existing, $input );

		// Checkbox unchecked handling
		if ( 'appearance' === $tab ) {
			$merged['show_labels'] = ! empty( $input['show_labels'] );
		} elseif ( 'visibility' === $tab ) {
			$merged['show_on_tablet'] = ! empty( $input['show_on_tablet'] );
			$merged['global_excluded_pages'] = is_array( $input['global_excluded_pages'] ?? null )
				? array_map( 'absint', $input['global_excluded_pages'] )
				: [];
		} elseif ( 'woocommerce' === $tab ) {
			$merged['enable_cart_badge'] = ! empty( $input['enable_cart_badge'] );
		} elseif ( 'advanced' === $tab ) {
			$merged['delete_data_on_uninstall'] = ! empty( $input['delete_data_on_uninstall'] );
			$merged['enable_logging']          = ! empty( $input['enable_logging'] );
		}

		SettingsRegistry::update( $merged );

		wp_safe_redirect(
			add_query_arg(
				[
					'page'    => AdminMenu::SUBMENU_SLUG,
					'tab'     => $tab,
					'message' => 'saved',
				],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	private function handle_reset_settings(): void {
		check_admin_referer( 'wpcal_reset_settings_nonce', '_wpcal_nonce' );

		SettingsRegistry::reset();

		wp_safe_redirect(
			add_query_arg(
				[
					'page'    => AdminMenu::SUBMENU_SLUG,
					'tab'     => 'advanced',
					'message' => 'reset',
				],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	private function handle_save_dock(): void {
		check_admin_referer( 'wpcal_save_dock_nonce', '_wpcal_nonce' );

		$dock_id    = absint( $_POST['dock_id'] ?? 0 );
		$title      = sanitize_text_field( $_POST['dock_title'] ?? __( 'Mobile Dock', 'wpcalibrate-mobile-dock' ) );
		$status     = in_array( $_POST['dock_status'] ?? 'publish', [ 'publish', 'draft' ], true ) ? $_POST['dock_status'] : 'publish';
		$is_default = ! empty( $_POST['dock_is_default'] );
		$priority   = isset( $_POST['dock_priority'] ) ? (int) $_POST['dock_priority'] : 10;
		$breakpoint = isset( $_POST['dock_breakpoint'] ) ? absint( $_POST['dock_breakpoint'] ) : 768;
		$show_tablet= ! empty( $_POST['dock_show_on_tablet'] );
		$tablet_bp  = isset( $_POST['dock_tablet_breakpoint'] ) ? absint( $_POST['dock_tablet_breakpoint'] ) : 1024;

		// Process items
		$raw_items = $_POST['items'] ?? [];
		$items     = [];
		if ( is_array( $raw_items ) ) {
			foreach ( $raw_items as $item_data ) {
				if ( is_array( $item_data ) ) {
					$items[] = NavigationItem::from_array( $item_data );
				}
			}
		}

		// Process visibility rules
		$raw_rules = $_POST['visibility_rules'] ?? [];
		$rules     = [];
		if ( is_array( $raw_rules ) ) {
			foreach ( $raw_rules as $rule_data ) {
				if ( is_array( $rule_data ) ) {
					$rules[] = VisibilityRule::from_array( $rule_data );
				}
			}
		}

		// Process appearance overrides
		$raw_appearance = $_POST['appearance'] ?? [];
		$appearance     = is_array( $raw_appearance ) ? AppearanceSettings::from_array( $raw_appearance ) : new AppearanceSettings();

		$config = new NavigationConfig(
			id: $dock_id,
			title: $title,
			status: $status,
			is_default: $is_default,
			priority: $priority,
			items: $items,
			appearance: $appearance,
			visibility_rules: $rules,
			breakpoint: $breakpoint,
			show_on_tablet: $show_tablet,
			tablet_breakpoint: $tablet_bp
		);

		$saved_id = $this->repository->save( $config );

		wp_safe_redirect(
			add_query_arg(
				[
					'page'    => AdminMenu::SUBMENU_SLUG,
					'tab'     => 'navigations',
					'view'    => 'edit',
					'dock_id' => $saved_id,
					'message' => 'dock_saved',
				],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	private function handle_delete_dock( int $dock_id ): void {
		check_admin_referer( 'wpcal_delete_dock_' . $dock_id );

		$this->repository->delete( $dock_id );

		wp_safe_redirect(
			add_query_arg(
				[
					'page'    => AdminMenu::SUBMENU_SLUG,
					'tab'     => 'navigations',
					'message' => 'dock_deleted',
				],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	private function handle_duplicate_dock( int $dock_id ): void {
		check_admin_referer( 'wpcal_duplicate_dock_' . $dock_id );

		$new_id = $this->repository->duplicate( $dock_id );

		wp_safe_redirect(
			add_query_arg(
				[
					'page'    => AdminMenu::SUBMENU_SLUG,
					'tab'     => 'navigations',
					'view'    => 'edit',
					'dock_id' => $new_id,
					'message' => 'dock_duplicated',
				],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	private function handle_set_default_dock( int $dock_id ): void {
		check_admin_referer( 'wpcal_set_default_' . $dock_id );

		$this->repository->set_default( $dock_id );

		wp_safe_redirect(
			add_query_arg(
				[
					'page'    => AdminMenu::SUBMENU_SLUG,
					'tab'     => 'navigations',
					'message' => 'dock_set_default',
				],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Render the administrative screen.
	 */
	public function render(): void {
		if ( ! current_user_can( $this->get_capability() ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wpcalibrate-mobile-dock' ) );
		}

		$current_tab = sanitize_key( $_GET['tab'] ?? 'overview' );
		$valid_tabs  = [ 'overview', 'navigations', 'appearance', 'visibility', 'woocommerce', 'advanced', 'license', 'support' ];
		if ( ! in_array( $current_tab, $valid_tabs, true ) ) {
			$current_tab = 'overview';
		}

		$settings = SettingsRegistry::get_all();

		include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/header.php';

		switch ( $current_tab ) {
			case 'overview':
				include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/overview.php';
				break;

			case 'navigations':
				$view = sanitize_key( $_GET['view'] ?? 'list' );
				if ( 'edit' === $view || 'new' === $view ) {
					$dock_id = absint( $_GET['dock_id'] ?? 0 );
					$config  = $dock_id > 0 ? $this->repository->get( $dock_id ) : null;
					if ( ! $config ) {
						$config = new NavigationConfig(
							id: 0,
							title: __( 'New Mobile Dock', 'wpcalibrate-mobile-dock' ),
							status: 'publish',
							is_default: false,
							priority: 10,
							items: [],
							appearance: new AppearanceSettings(),
							visibility_rules: []
						);
					}
					include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/navigation-edit.php';
				} else {
					$docks = $this->repository->get_all( true );
					include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/navigations.php';
				}
				break;

			case 'appearance':
				include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/appearance.php';
				break;

			case 'visibility':
				include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/visibility.php';
				break;

			case 'woocommerce':
				include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/woocommerce.php';
				break;

			case 'advanced':
				include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/advanced.php';
				break;

			case 'license':
				$provider = $this->license_provider;
				include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/license.php';
				break;

			case 'support':
				include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/support.php';
				break;
		}

		include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/footer.php';
	}
}
