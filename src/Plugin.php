<?php
/**
 * Main Plugin Coordinator.
 *
 * @package WPCalibrate\MobileDock
 */

namespace WPCalibrate\MobileDock;

use WPCalibrate\MobileDock\Admin\AdminMenu;
use WPCalibrate\MobileDock\Admin\AdminScreens;
use WPCalibrate\MobileDock\Assets\AssetManager;
use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Frontend\ActionResolver;
use WPCalibrate\MobileDock\Frontend\ActiveStateMatcher;
use WPCalibrate\MobileDock\Frontend\Renderer;
use WPCalibrate\MobileDock\Frontend\SearchController;
use WPCalibrate\MobileDock\Licensing\LicenseProviderInterface;
use WPCalibrate\MobileDock\Licensing\UnconfiguredLicenseProvider;
use WPCalibrate\MobileDock\Migration\MigrationManager;
use WPCalibrate\MobileDock\Navigation\NavigationRepository;
use WPCalibrate\MobileDock\Navigation\SettingsRegistry;
use WPCalibrate\MobileDock\Rules\RuleEngine;
use WPCalibrate\MobileDock\WooCommerce\CartBadgeService;

defined( 'ABSPATH' ) || exit;

class Plugin {

	private static ?self $instance = null;

	private NavigationRepository $repository;
	private RuleEngine $rule_engine;
	private ActionResolver $action_resolver;
	private ActiveStateMatcher $active_matcher;
	private SearchController $search_controller;
	private CartBadgeService $cart_badge_service;
	private Renderer $renderer;
	private LicenseProviderInterface $license_provider;
	private AdminScreens $admin_screens;
	private AdminMenu $admin_menu;
	private AssetManager $asset_manager;
	private MigrationManager $migration_manager;

	private ?NavigationConfig $active_dock = null;

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->repository         = new NavigationRepository();
		$this->rule_engine        = new RuleEngine();
		$this->action_resolver    = new ActionResolver();
		$this->active_matcher     = new ActiveStateMatcher();
		$this->search_controller  = new SearchController();
		$this->cart_badge_service = new CartBadgeService();
		$this->license_provider   = new UnconfiguredLicenseProvider();
		$this->migration_manager  = new MigrationManager( $this->repository );

		$this->renderer = new Renderer(
			$this->action_resolver,
			$this->active_matcher,
			$this->search_controller,
			$this->cart_badge_service,
			$this->rule_engine
		);

		$this->admin_screens = new AdminScreens(
			$this->repository,
			$this->license_provider
		);

		$this->admin_menu = new AdminMenu( $this->admin_screens );

		$this->asset_manager = new AssetManager( $this->admin_screens );
	}

	public function init(): void {
		add_action( 'init', [ $this, 'on_init' ] );
		add_action( 'template_redirect', [ $this, 'on_template_redirect' ] );

		if ( is_admin() ) {
			$this->admin_screens->init();
			$this->admin_menu->init();
			$this->asset_manager->init();
		}

		$this->cart_badge_service->init();
	}

	public function on_init(): void {
		// Load localization textdomain.
		load_plugin_textdomain(
			'wpcalibrate-mobile-dock',
			false,
			dirname( WPCALIBRATE_MOBILE_DOCK_BASENAME ) . '/languages'
		);

		// Register private post type.
		NavigationRepository::register_post_type();

		// Check and run migrations if needed.
		$this->migration_manager->maybe_migrate();
	}

	/**
	 * Resolves active navigation dock on template_redirect.
	 */
	public function on_template_redirect(): void {
		if ( is_admin() || wp_doing_ajax() || wp_is_json_request() ) {
			return;
		}

		$published_configs = $this->repository->get_all( false );
		$this->active_dock = $this->rule_engine->resolve( $published_configs );

		if ( $this->active_dock ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend' ] );
			add_action( 'wp_footer', [ $this, 'render_dock' ] );
		}
	}

	public function enqueue_frontend(): void {
		if ( $this->active_dock ) {
			$this->asset_manager->enqueue_frontend_assets( $this->active_dock );
		}
	}

	public function render_dock(): void {
		if ( $this->active_dock ) {
			$this->renderer->render( $this->active_dock );
		}
	}

	/**
	 * Fired on plugin activation.
	 */
	public static function activate(): void {
		NavigationRepository::register_post_type();

		$repo = new NavigationRepository();
		$mig  = new MigrationManager( $repo );
		$mig->maybe_migrate();

		// Store initial version and db version
		update_option( 'wpcalibrate_mobile_dock_version', WPCALIBRATE_MOBILE_DOCK_VERSION );
		update_option( 'wpcalibrate_mobile_dock_db_version', WPCALIBRATE_MOBILE_DOCK_DB_VERSION );
	}

	/**
	 * Fired on plugin deactivation.
	 */
	public static function deactivate(): void {
		$repo = new NavigationRepository();
		$repo->clear_cache();
	}

	public function get_repository(): NavigationRepository {
		return $this->repository;
	}

	public function get_rule_engine(): RuleEngine {
		return $this->rule_engine;
	}

	public function get_renderer(): Renderer {
		return $this->renderer;
	}
}
