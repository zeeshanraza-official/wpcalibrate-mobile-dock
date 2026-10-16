<?php
/**
 * Icon Registry for bundled SVG icons.
 *
 * @package WPCalibrate\MobileDock\Icons
 */

namespace WPCalibrate\MobileDock\Icons;

defined( 'ABSPATH' ) || exit;

class IconRegistry {

	/**
	 * In-memory cache of loaded SVGs.
	 *
	 * @var array<string, string>
	 */
	private static array $cache = [];

	/**
	 * Returns the curated list of approved icon definitions.
	 *
	 * @return array<string, array{label: string, category: string}>
	 */
	public static function get_icons(): array {
		$icons = [
			'home'       => [ 'label' => __( 'Home', 'wpcalibrate-mobile-dock' ), 'category' => 'general' ],
			'search'     => [ 'label' => __( 'Search', 'wpcalibrate-mobile-dock' ), 'category' => 'action' ],
			'cart'       => [ 'label' => __( 'Cart', 'wpcalibrate-mobile-dock' ), 'category' => 'commerce' ],
			'bag'        => [ 'label' => __( 'Shopping Bag', 'wpcalibrate-mobile-dock' ), 'category' => 'commerce' ],
			'store'      => [ 'label' => __( 'Shop / Store', 'wpcalibrate-mobile-dock' ), 'category' => 'commerce' ],
			'tag'        => [ 'label' => __( 'Price Tag', 'wpcalibrate-mobile-dock' ), 'category' => 'commerce' ],
			'user'       => [ 'label' => __( 'User / Account', 'wpcalibrate-mobile-dock' ), 'category' => 'general' ],
			'heart'      => [ 'label' => __( 'Wishlist / Heart', 'wpcalibrate-mobile-dock' ), 'category' => 'general' ],
			'menu'       => [ 'label' => __( 'Menu / Hamburger', 'wpcalibrate-mobile-dock' ), 'category' => 'navigation' ],
			'arrow-left' => [ 'label' => __( 'Arrow Back', 'wpcalibrate-mobile-dock' ), 'category' => 'navigation' ],
			'arrow-up'   => [ 'label' => __( 'Scroll to Top', 'wpcalibrate-mobile-dock' ), 'category' => 'navigation' ],
			'phone'      => [ 'label' => __( 'Phone', 'wpcalibrate-mobile-dock' ), 'category' => 'contact' ],
			'whatsapp'   => [ 'label' => __( 'WhatsApp', 'wpcalibrate-mobile-dock' ), 'category' => 'contact' ],
			'email'      => [ 'label' => __( 'Email', 'wpcalibrate-mobile-dock' ), 'category' => 'contact' ],
			'chat'       => [ 'label' => __( 'Chat', 'wpcalibrate-mobile-dock' ), 'category' => 'contact' ],
			'bell'       => [ 'label' => __( 'Bell / Notifications', 'wpcalibrate-mobile-dock' ), 'category' => 'general' ],
			'grid'       => [ 'label' => __( 'Grid / Catalog', 'wpcalibrate-mobile-dock' ), 'category' => 'navigation' ],
			'compass'    => [ 'label' => __( 'Explore / Compass', 'wpcalibrate-mobile-dock' ), 'category' => 'navigation' ],
			'bookmark'   => [ 'label' => __( 'Bookmark', 'wpcalibrate-mobile-dock' ), 'category' => 'general' ],
			'settings'   => [ 'label' => __( 'Settings', 'wpcalibrate-mobile-dock' ), 'category' => 'general' ],
			'filter'     => [ 'label' => __( 'Filter', 'wpcalibrate-mobile-dock' ), 'category' => 'action' ],
			'share'      => [ 'label' => __( 'Share', 'wpcalibrate-mobile-dock' ), 'category' => 'action' ],
			'info'       => [ 'label' => __( 'Info', 'wpcalibrate-mobile-dock' ), 'category' => 'general' ],
			'help'       => [ 'label' => __( 'Help', 'wpcalibrate-mobile-dock' ), 'category' => 'general' ],
			'calendar'   => [ 'label' => __( 'Calendar', 'wpcalibrate-mobile-dock' ), 'category' => 'general' ],
			'map-pin'    => [ 'label' => __( 'Map Pin', 'wpcalibrate-mobile-dock' ), 'category' => 'contact' ],
		];

		/**
		 * Filter the allowed mobile dock icon list.
		 *
		 * @param array $icons Approved icons.
		 */
		return apply_filters( 'wpcalibrate_mobile_dock_icons', $icons );
	}

	/**
	 * Check if an icon identifier is valid.
	 *
	 * @param string $icon_id Icon key.
	 * @return bool
	 */
	public static function is_valid( string $icon_id ): bool {
		$icons = self::get_icons();
		return isset( $icons[ $icon_id ] );
	}

	/**
	 * Get the sanitized SVG markup for an approved icon.
	 *
	 * @param string $icon_id Icon key.
	 * @param array  $attributes Custom HTML attributes (e.g. class, width, height, aria-hidden).
	 * @return string Safe SVG markup, or empty string if not allowed.
	 */
	public static function get_svg( string $icon_id, array $attributes = [] ): string {
		$icon_id = sanitize_key( $icon_id );
		if ( ! self::is_valid( $icon_id ) ) {
			$icon_id = 'home'; // Fallback to safe default.
		}

		if ( ! isset( self::$cache[ $icon_id ] ) ) {
			$path = defined( 'WPCALIBRATE_MOBILE_DOCK_PATH' )
				? WPCALIBRATE_MOBILE_DOCK_PATH . 'assets/icons/' . $icon_id . '.svg'
				: dirname( __DIR__, 2 ) . '/assets/icons/' . $icon_id . '.svg';

			if ( ! file_exists( $path ) ) {
				return '';
			}

			$raw_content = file_get_contents( $path );
			if ( false === $raw_content ) {
				return '';
			}

			// Clean any XML headers or unwanted whitespace.
			$raw_content = trim( preg_replace( '/<\?xml.*?\?>/i', '', $raw_content ) );
			self::$cache[ $icon_id ] = $raw_content;
		}

		$svg = self::$cache[ $icon_id ];

		if ( ! empty( $attributes ) ) {
			$attr_strings = [];
			foreach ( $attributes as $key => $val ) {
				$clean_key = sanitize_key( $key );
				$clean_val = esc_attr( $val );
				$attr_strings[] = sprintf( '%s="%s"', $clean_key, $clean_val );
			}
			$attr_str = implode( ' ', $attr_strings );
			$svg = preg_replace( '/^<svg\b/i', '<svg ' . $attr_str, $svg );
		}

		return $svg;
	}
}
