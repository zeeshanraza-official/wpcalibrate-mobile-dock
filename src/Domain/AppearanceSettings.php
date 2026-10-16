<?php
/**
 * Appearance Settings Value Object.
 *
 * @package WPCalibrate\MobileDock\Domain
 */

namespace WPCalibrate\MobileDock\Domain;

defined( 'ABSPATH' ) || exit;

class AppearanceSettings {

	public function __construct(
		public string $preset = 'light',
		public string $bg_color = '#ffffff',
		public string $icon_color = '#4b5563',
		public string $active_icon_color = '#2563eb',
		public string $text_color = '#6b7280',
		public string $active_text_color = '#2563eb',
		public string $font_size = '11px',
		public string $icon_size = '22px',
		public string $height = '60px',
		public string $horizontal_spacing = '8px',
		public string $item_padding = '6px 4px',
		public string $border_width = '1px',
		public string $border_color = '#e5e7eb',
		public string $border_radius = '0px',
		public string $box_shadow = '0 -2px 10px rgba(0,0,0,0.06)',
		public bool $show_labels = true,
		public string $layout_mode = 'fixed', // 'fixed' or 'floating'
		public string $center_button_size = '52px',
		public string $center_button_offset = '-16px',
		public string $center_button_bg = '#2563eb',
		public string $center_button_icon_color = '#ffffff',
		public bool $override_global = false
	) {}

	public static function get_default_light(): array {
		return [
			'preset'                   => 'light',
			'bg_color'                 => '#ffffff',
			'icon_color'               => '#4b5563',
			'active_icon_color'        => '#2563eb',
			'text_color'               => '#6b7280',
			'active_text_color'        => '#2563eb',
			'font_size'                => '11px',
			'icon_size'                => '22px',
			'height'                   => '60px',
			'horizontal_spacing'       => '8px',
			'item_padding'             => '6px 4px',
			'border_width'             => '1px',
			'border_color'             => '#e5e7eb',
			'border_radius'            => '0px',
			'box_shadow'               => '0 -2px 10px rgba(0,0,0,0.06)',
			'show_labels'              => true,
			'layout_mode'              => 'fixed',
			'center_button_size'       => '52px',
			'center_button_offset'     => '-16px',
			'center_button_bg'         => '#2563eb',
			'center_button_icon_color' => '#ffffff',
			'override_global'          => false,
		];
	}

	public static function get_default_dark(): array {
		return [
			'preset'                   => 'dark',
			'bg_color'                 => '#18181b',
			'icon_color'               => '#a1a1aa',
			'active_icon_color'        => '#60a5fa',
			'text_color'               => '#9ca3af',
			'active_text_color'        => '#60a5fa',
			'font_size'                => '11px',
			'icon_size'                => '22px',
			'height'                   => '60px',
			'horizontal_spacing'       => '8px',
			'item_padding'             => '6px 4px',
			'border_width'             => '1px',
			'border_color'             => '#27272a',
			'border_radius'            => '0px',
			'box_shadow'               => '0 -2px 12px rgba(0,0,0,0.4)',
			'show_labels'              => true,
			'layout_mode'              => 'fixed',
			'center_button_size'       => '52px',
			'center_button_offset'     => '-16px',
			'center_button_bg'         => '#3b82f6',
			'center_button_icon_color' => '#ffffff',
			'override_global'          => false,
		];
	}

	public static function sanitize_color( string $color, string $fallback = '#ffffff' ): string {
		$trimmed = trim( $color );
		// Hex color #123 or #123456 or #12345678
		if ( preg_match( '/^#([A-Fa-f0-9]{3,4}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $trimmed ) ) {
			return $trimmed;
		}
		// rgb or rgba
		if ( preg_match( '/^rgba?\(\s*([0-9]{1,3}\s*,\s*){2}[0-9]{1,3}\s*(,\s*(0|1|0?\.[0-9]+)\s*)?\)$/i', $trimmed ) ) {
			return $trimmed;
		}
		// hsl or hsla
		if ( preg_match( '/^hsla?\(\s*[0-9]{1,3}\s*,\s*[0-9]{1,3}%\s*,\s*[0-9]{1,3}%\s*(,\s*(0|1|0?\.[0-9]+)\s*)?\)$/i', $trimmed ) ) {
			return $trimmed;
		}
		// transparent
		if ( 'transparent' === strtolower( $trimmed ) ) {
			return 'transparent';
		}
		return $fallback;
	}

	public static function sanitize_dimension( string $value, string $fallback = '0px' ): string {
		$trimmed = trim( $value );
		if ( preg_match( '/^-?[0-9]+(\.[0-9]+)?(px|em|rem|%|vh|vw)$/i', $trimmed ) ) {
			return $trimmed;
		}
		if ( '0' === $trimmed ) {
			return '0px';
		}
		return $fallback;
	}

	public static function sanitize_box_shadow( string $value ): string {
		$trimmed = trim( $value );
		if ( 'none' === strtolower( $trimmed ) || empty( $trimmed ) ) {
			return 'none';
		}
		// Allow safe box-shadow patterns: numbers, units, commas, spaces, rgba, hex
		if ( preg_match( '/^[0-9a-zA-Z_\s,\.\-\(\)#%]+$/', $trimmed ) && ! preg_match( '/[;<>{}]/', $trimmed ) ) {
			return $trimmed;
		}
		return '0 -2px 10px rgba(0,0,0,0.06)';
	}

	public static function from_array( array $data ): self {
		$defaults = self::get_default_light();

		$preset          = in_array( $data['preset'] ?? 'light', [ 'light', 'dark', 'custom' ], true ) ? (string) $data['preset'] : 'light';
		$bg_color        = self::sanitize_color( $data['bg_color'] ?? $defaults['bg_color'], $defaults['bg_color'] );
		$icon_color      = self::sanitize_color( $data['icon_color'] ?? $defaults['icon_color'], $defaults['icon_color'] );
		$act_icon_color  = self::sanitize_color( $data['active_icon_color'] ?? $defaults['active_icon_color'], $defaults['active_icon_color'] );
		$text_color      = self::sanitize_color( $data['text_color'] ?? $defaults['text_color'], $defaults['text_color'] );
		$act_text_color  = self::sanitize_color( $data['active_text_color'] ?? $defaults['active_text_color'], $defaults['active_text_color'] );
		$font_size       = self::sanitize_dimension( $data['font_size'] ?? $defaults['font_size'], $defaults['font_size'] );
		$icon_size       = self::sanitize_dimension( $data['icon_size'] ?? $defaults['icon_size'], $defaults['icon_size'] );
		$height          = self::sanitize_dimension( $data['height'] ?? $defaults['height'], $defaults['height'] );
		$horiz_spacing   = self::sanitize_dimension( $data['horizontal_spacing'] ?? $defaults['horizontal_spacing'], $defaults['horizontal_spacing'] );
		$border_width    = self::sanitize_dimension( $data['border_width'] ?? $defaults['border_width'], $defaults['border_width'] );
		$border_color    = self::sanitize_color( $data['border_color'] ?? $defaults['border_color'], $defaults['border_color'] );
		$border_radius   = self::sanitize_dimension( $data['border_radius'] ?? $defaults['border_radius'], $defaults['border_radius'] );
		$box_shadow      = self::sanitize_box_shadow( $data['box_shadow'] ?? $defaults['box_shadow'] );
		$show_labels     = isset( $data['show_labels'] ) ? (bool) $data['show_labels'] : true;
		$layout_mode     = in_array( $data['layout_mode'] ?? 'fixed', [ 'fixed', 'floating' ], true ) ? (string) $data['layout_mode'] : 'fixed';

		$btn_size        = self::sanitize_dimension( $data['center_button_size'] ?? $defaults['center_button_size'], $defaults['center_button_size'] );
		$btn_offset      = self::sanitize_dimension( $data['center_button_offset'] ?? $defaults['center_button_offset'], $defaults['center_button_offset'] );
		$btn_bg          = self::sanitize_color( $data['center_button_bg'] ?? $defaults['center_button_bg'], $defaults['center_button_bg'] );
		$btn_icon_col    = self::sanitize_color( $data['center_button_icon_color'] ?? $defaults['center_button_icon_color'], $defaults['center_button_icon_color'] );
		$override        = ! empty( $data['override_global'] );

		// Item padding sanitized
		$item_padding    = sanitize_text_field( $data['item_padding'] ?? $defaults['item_padding'] );
		if ( ! preg_match( '/^[0-9pxemrem%\s]+$/', $item_padding ) ) {
			$item_padding = $defaults['item_padding'];
		}

		return new self(
			$preset,
			$bg_color,
			$icon_color,
			$act_icon_color,
			$text_color,
			$act_text_color,
			$font_size,
			$icon_size,
			$height,
			$horiz_spacing,
			$item_padding,
			$border_width,
			$border_color,
			$border_radius,
			$box_shadow,
			$show_labels,
			$layout_mode,
			$btn_size,
			$btn_offset,
			$btn_bg,
			$btn_icon_col,
			$override
		);
	}

	public function to_array(): array {
		return [
			'preset'                   => $this->preset,
			'bg_color'                 => $this->bg_color,
			'icon_color'               => $this->icon_color,
			'active_icon_color'        => $this->active_icon_color,
			'text_color'               => $this->text_color,
			'active_text_color'        => $this->active_text_color,
			'font_size'                => $this->font_size,
			'icon_size'                => $this->icon_size,
			'height'                   => $this->height,
			'horizontal_spacing'       => $this->horizontal_spacing,
			'item_padding'             => $this->item_padding,
			'border_width'             => $this->border_width,
			'border_color'             => $this->border_color,
			'border_radius'            => $this->border_radius,
			'box_shadow'               => $this->box_shadow,
			'show_labels'              => $this->show_labels,
			'layout_mode'              => $this->layout_mode,
			'center_button_size'       => $this->center_button_size,
			'center_button_offset'     => $this->center_button_offset,
			'center_button_bg'         => $this->center_button_bg,
			'center_button_icon_color' => $this->center_button_icon_color,
			'override_global'          => $this->override_global,
		];
	}

	/**
	 * Generates CSS Custom Properties array.
	 *
	 * @return array
	 */
	public function to_css_variables(): array {
		return [
			'--wpcal-dock-bg'             => $this->bg_color,
			'--wpcal-dock-icon-color'     => $this->icon_color,
			'--wpcal-dock-active-icon'    => $this->active_icon_color,
			'--wpcal-dock-text-color'     => $this->text_color,
			'--wpcal-dock-active-text'    => $this->active_text_color,
			'--wpcal-dock-font-size'      => $this->font_size,
			'--wpcal-dock-icon-size'      => $this->icon_size,
			'--wpcal-dock-height'         => $this->height,
			'--wpcal-dock-horiz-gap'      => $this->horizontal_spacing,
			'--wpcal-dock-item-padding'   => $this->item_padding,
			'--wpcal-dock-border-width'   => $this->border_width,
			'--wpcal-dock-border-color'   => $this->border_color,
			'--wpcal-dock-border-radius'  => $this->border_radius,
			'--wpcal-dock-box-shadow'     => $this->box_shadow,
			'--wpcal-center-btn-size'     => $this->center_button_size,
			'--wpcal-center-btn-offset'   => $this->center_button_offset,
			'--wpcal-center-btn-bg'       => $this->center_button_bg,
			'--wpcal-center-btn-icon-col' => $this->center_button_icon_color,
		];
	}
}
