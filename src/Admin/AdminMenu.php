<?php
/**
 * Admin Menu Management.
 *
 * Implements defensive, shared WPCalibrate parent dashboard registration
 * and isolated Mobile Dock submenu.
 *
 * @package WPCalibrate\MobileDock\Admin
 */

namespace WPCalibrate\MobileDock\Admin;

defined( 'ABSPATH' ) || exit;

class AdminMenu {

	public const PARENT_SLUG  = 'wpcalibrate';
	public const SUBMENU_SLUG = 'wpcalibrate-mobile-dock';

	public function __construct( private AdminScreens $admin_screens ) {}

	public function init(): void {
		add_action( 'admin_menu', [ $this, 'register_menu' ], 9 );
		add_action( 'admin_head', [ $this, 'print_admin_menu_styles' ] );
	}

	public function get_capability(): string {
		/**
		 * Filter required administrative capability for Mobile Dock.
		 *
		 * @param string $capability Default 'manage_options'.
		 */
		return apply_filters( 'wpcalibrate_mobile_dock_capability', 'manage_options' );
	}

	/**
	 * Get scalable SVG data URI for WordPress dashboard menu icon.
	 */
	public function get_menu_icon(): string {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20" fill="none"><path d="M 13.5 2 L 6.5 2 C 4 2 2 4 2 6.5 L 2 13.5 C 2 16 4 18 6.5 18 L 13.5 18 C 16 18 18 16 18 13.5 L 18 12.5 C 18 12.2 17.8 12 17.5 12 L 15.5 12 C 15.2 12 15 12.2 15 12.5 L 15 13.2 C 15 14.2 14.2 15 13.2 15 L 6.8 15 C 5.8 15 5 14.2 5 13.2 L 5 6.8 C 5 5.8 5.8 5 6.8 5 L 13.2 5 C 14.2 5 15 5.8 15 6.8 L 15 7.5 C 15 7.8 15.2 8 15.5 8 L 17.5 8 C 17.8 8 18 7.8 18 7.5 L 18 6.5 C 18 4 16 2 13.5 2 Z" fill="#a7aaad"/><rect x="15.8" y="9.2" width="2.4" height="1.6" rx="0.5" fill="#00a896"/><text x="5.5" y="12.5" font-family="-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif" font-size="6.8" font-weight="900" fill="#a7aaad" letter-spacing="-0.8">WP</text></svg>';
		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	/**
	 * Print global admin menu icon sizing and hover styles.
	 */
	public function print_admin_menu_styles(): void {
		?>
		<style>
		#adminmenu .toplevel_page_wpcalibrate .wp-menu-image {
			display: flex;
			align-items: center;
			justify-content: center;
		}
		#adminmenu .toplevel_page_wpcalibrate .wp-menu-image img,
		#adminmenu .toplevel_page_wpcalibrate .wp-menu-image svg {
			width: 20px !important;
			height: 20px !important;
			max-width: 20px !important;
			max-height: 20px !important;
			padding: 0 !important;
			object-fit: contain;
		}
		#adminmenu .toplevel_page_wpcalibrate:hover .wp-menu-image.svg,
		#adminmenu .toplevel_page_wpcalibrate.wp-has-current-submenu .wp-menu-image.svg {
			opacity: 1 !important;
		}
		</style>
		<?php
	}

	public function register_menu(): void {
		$capability = $this->get_capability();
		if ( ! current_user_can( $capability ) ) {
			return;
		}

		global $admin_page_hooks;

		// 1. Check if the shared WPCalibrate parent menu is already registered.
		$parent_exists = isset( $admin_page_hooks[ self::PARENT_SLUG ] );

		if ( ! $parent_exists ) {
			add_menu_page(
				__( 'WPCalibrate', 'wpcalibrate-mobile-dock' ),
				__( 'WPCalibrate', 'wpcalibrate-mobile-dock' ),
				$capability,
				self::PARENT_SLUG,
				[ $this, 'render_parent_placeholder' ],
				$this->get_menu_icon(),
				59
			);
		}

		// 2. Add Mobile Dock submenu under WPCalibrate parent.
		$hook_suffix = add_submenu_page(
			self::PARENT_SLUG,
			__( 'Mobile Dock', 'wpcalibrate-mobile-dock' ),
			__( 'Mobile Dock', 'wpcalibrate-mobile-dock' ),
			$capability,
			self::SUBMENU_SLUG,
			[ $this->admin_screens, 'render' ]
		);

		// Store hook suffix for asset enqueue filtering.
		$this->admin_screens->set_hook_suffix( $hook_suffix );
	}

	/**
	 * Default fallback dashboard callback if parent was registered by this plugin.
	 */
	public function render_parent_placeholder(): void {
		$url = admin_url( 'admin.php?page=' . self::SUBMENU_SLUG );
		wp_safe_redirect( $url );
		exit;
	}
}
