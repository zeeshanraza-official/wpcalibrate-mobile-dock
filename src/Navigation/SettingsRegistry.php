<?php
/**
 * Settings Registry and Schema Validation.
 *
 * @package WPCalibrate\MobileDock\Navigation
 */

namespace WPCalibrate\MobileDock\Navigation;

use WPCalibrate\MobileDock\Domain\AppearanceSettings;

defined( 'ABSPATH' ) || exit;

class SettingsRegistry {

	public const OPTION_NAME = 'wpcalibrate_mobile_dock_settings';

	public static function get_defaults(): array {
		$appearance_defaults = AppearanceSettings::get_default_light();

		return array_merge(
			$appearance_defaults,
			[
				// Visibility settings
				'default_breakpoint'        => 768,
				'show_on_tablet'            => false,
				'tablet_breakpoint'         => 1024,
				'global_excluded_pages'     => [],

				// WooCommerce settings
				'enable_cart_badge'         => true,
				'cart_badge_bg'             => '#ef4444',
				'cart_badge_text'           => '#ffffff',
				'cart_badge_max'            => 99,

				// Advanced settings
				'custom_css'                => '',
				'delete_data_on_uninstall'  => false,
				'enable_logging'            => false,
			]
		);
	}

	public static function get_all(): array {
		$stored   = get_option( self::OPTION_NAME, [] );
		$defaults = self::get_defaults();

		if ( ! is_array( $stored ) ) {
			return $defaults;
		}

		return wp_parse_args( $stored, $defaults );
	}

	public static function get( string $key, mixed $default = null ): mixed {
		$all = self::get_all();
		return $all[ $key ] ?? $default;
	}

	public static function update( array $input ): bool {
		$sanitized = self::sanitize( $input );
		return update_option( self::OPTION_NAME, $sanitized );
	}

	public static function reset(): bool {
		return update_option( self::OPTION_NAME, self::get_defaults() );
	}

	public static function sanitize( array $input ): array {
		$current  = self::get_all();
		$defaults = self::get_defaults();
		$clean    = [];

		// Appearance tokens
		$raw_preset                        = $input['preset'] ?? 'light';
		$clean['preset']                   = in_array( $raw_preset, [ 'light', 'dark', 'custom' ], true ) ? $raw_preset : 'light';
		$clean['bg_color']                 = AppearanceSettings::sanitize_color( $input['bg_color'] ?? $defaults['bg_color'], $defaults['bg_color'] );
		$clean['icon_color']               = AppearanceSettings::sanitize_color( $input['icon_color'] ?? $defaults['icon_color'], $defaults['icon_color'] );
		$clean['active_icon_color']        = AppearanceSettings::sanitize_color( $input['active_icon_color'] ?? $defaults['active_icon_color'], $defaults['active_icon_color'] );
		$clean['text_color']               = AppearanceSettings::sanitize_color( $input['text_color'] ?? $defaults['text_color'], $defaults['text_color'] );
		$clean['active_text_color']        = AppearanceSettings::sanitize_color( $input['active_text_color'] ?? $defaults['active_text_color'], $defaults['active_text_color'] );
		$clean['font_size']                = AppearanceSettings::sanitize_dimension( $input['font_size'] ?? $defaults['font_size'], $defaults['font_size'] );
		$clean['icon_size']                = AppearanceSettings::sanitize_dimension( $input['icon_size'] ?? $defaults['icon_size'], $defaults['icon_size'] );
		$clean['height']                   = AppearanceSettings::sanitize_dimension( $input['height'] ?? $defaults['height'], $defaults['height'] );
		$clean['horizontal_spacing']       = AppearanceSettings::sanitize_dimension( $input['horizontal_spacing'] ?? $defaults['horizontal_spacing'], $defaults['horizontal_spacing'] );
		$clean['border_width']             = AppearanceSettings::sanitize_dimension( $input['border_width'] ?? $defaults['border_width'], $defaults['border_width'] );
		$clean['border_color']             = AppearanceSettings::sanitize_color( $input['border_color'] ?? $defaults['border_color'], $defaults['border_color'] );
		$clean['border_radius']            = AppearanceSettings::sanitize_dimension( $input['border_radius'] ?? $defaults['border_radius'], $defaults['border_radius'] );
		$clean['box_shadow']               = AppearanceSettings::sanitize_box_shadow( $input['box_shadow'] ?? $defaults['box_shadow'] );
		$clean['show_labels']              = ! empty( $input['show_labels'] );
		$raw_layout                        = $input['layout_mode'] ?? 'fixed';
		$clean['layout_mode']              = in_array( $raw_layout, [ 'fixed', 'floating' ], true ) ? $raw_layout : 'fixed';

		$clean['center_button_size']       = AppearanceSettings::sanitize_dimension( $input['center_button_size'] ?? $defaults['center_button_size'], $defaults['center_button_size'] );
		$clean['center_button_offset']     = AppearanceSettings::sanitize_dimension( $input['center_button_offset'] ?? $defaults['center_button_offset'], $defaults['center_button_offset'] );
		$clean['center_button_bg']         = AppearanceSettings::sanitize_color( $input['center_button_bg'] ?? $defaults['center_button_bg'], $defaults['center_button_bg'] );
		$clean['center_button_icon_color'] = AppearanceSettings::sanitize_color( $input['center_button_icon_color'] ?? $defaults['center_button_icon_color'], $defaults['center_button_icon_color'] );
		$clean['override_global']          = false;

		$item_padding = sanitize_text_field( $input['item_padding'] ?? $defaults['item_padding'] );
		$clean['item_padding'] = preg_match( '/^[0-9pxemrem%\s]+$/', $item_padding ) ? $item_padding : $defaults['item_padding'];

		// Visibility tokens
		$bp = isset( $input['default_breakpoint'] ) ? absint( $input['default_breakpoint'] ) : 768;
		$clean['default_breakpoint'] = ( $bp >= 320 && $bp <= 1920 ) ? $bp : 768;

		$clean['show_on_tablet'] = ! empty( $input['show_on_tablet'] );

		$tbp = isset( $input['tablet_breakpoint'] ) ? absint( $input['tablet_breakpoint'] ) : 1024;
		$clean['tablet_breakpoint'] = ( $tbp >= 480 && $tbp <= 2560 ) ? $tbp : 1024;

		$raw_pages = is_array( $input['global_excluded_pages'] ?? null ) ? $input['global_excluded_pages'] : [];
		$clean['global_excluded_pages'] = array_values( array_filter( array_map( 'absint', $raw_pages ) ) );

		// WooCommerce tokens
		$clean['enable_cart_badge'] = ! empty( $input['enable_cart_badge'] );
		$clean['cart_badge_bg']     = AppearanceSettings::sanitize_color( $input['cart_badge_bg'] ?? $defaults['cart_badge_bg'], $defaults['cart_badge_bg'] );
		$clean['cart_badge_text']   = AppearanceSettings::sanitize_color( $input['cart_badge_text'] ?? $defaults['cart_badge_text'], $defaults['cart_badge_text'] );
		$max_badge                  = isset( $input['cart_badge_max'] ) ? absint( $input['cart_badge_max'] ) : 99;
		$clean['cart_badge_max']    = ( $max_badge > 0 && $max_badge <= 999 ) ? $max_badge : 99;

		// Advanced tokens
		$clean['delete_data_on_uninstall'] = ! empty( $input['delete_data_on_uninstall'] );
		$clean['enable_logging']           = ! empty( $input['enable_logging'] );

		// Sanitize Custom CSS strictly: strip tags, no <script>, no <?php
		$raw_css = $input['custom_css'] ?? '';
		$clean['custom_css'] = self::sanitize_custom_css( $raw_css );

		return $clean;
	}

	public static function sanitize_custom_css( string $css ): string {
		// Strip all tags completely.
		$css = wp_strip_all_tags( $css );
		// Disallow dangerous keywords / expression / behavior / javascript: / vbscript:
		$css = preg_replace( '/expression|behavior|javascript:|vbscript:/i', '', $css );
		return trim( $css );
	}
}
