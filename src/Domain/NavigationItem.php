<?php
/**
 * Navigation Item Value Object.
 *
 * @package WPCalibrate\MobileDock\Domain
 */

namespace WPCalibrate\MobileDock\Domain;

defined( 'ABSPATH' ) || exit;

class NavigationItem {

	public const ACTION_HOME        = 'home';
	public const ACTION_CUSTOM_URL  = 'custom_url';
	public const ACTION_PAGE        = 'page';
	public const ACTION_POST        = 'post';
	public const ACTION_BLOG        = 'blog';
	public const ACTION_SEARCH      = 'search';
	public const ACTION_BACK        = 'back';
	public const ACTION_SCROLL_TOP  = 'scroll_top';
	public const ACTION_PHONE       = 'phone';
	public const ACTION_WHATSAPP    = 'whatsapp';
	public const ACTION_EMAIL       = 'email';
	public const ACTION_WC_SHOP     = 'wc_shop';
	public const ACTION_WC_CATEGORY = 'wc_category';
	public const ACTION_WC_CART     = 'wc_cart';
	public const ACTION_WC_ACCOUNT  = 'wc_account';

	public static function get_supported_action_types(): array {
		$types = [
			self::ACTION_HOME        => __( 'Home', 'wpcalibrate-mobile-dock' ),
			self::ACTION_CUSTOM_URL  => __( 'Custom URL', 'wpcalibrate-mobile-dock' ),
			self::ACTION_PAGE        => __( 'WordPress Page', 'wpcalibrate-mobile-dock' ),
			self::ACTION_POST        => __( 'WordPress Post', 'wpcalibrate-mobile-dock' ),
			self::ACTION_BLOG        => __( 'Blog / Posts Index', 'wpcalibrate-mobile-dock' ),
			self::ACTION_SEARCH      => __( 'Search', 'wpcalibrate-mobile-dock' ),
			self::ACTION_BACK        => __( 'Browser Back', 'wpcalibrate-mobile-dock' ),
			self::ACTION_SCROLL_TOP  => __( 'Scroll to Top', 'wpcalibrate-mobile-dock' ),
			self::ACTION_PHONE       => __( 'Phone Call', 'wpcalibrate-mobile-dock' ),
			self::ACTION_WHATSAPP    => __( 'WhatsApp', 'wpcalibrate-mobile-dock' ),
			self::ACTION_EMAIL       => __( 'Email', 'wpcalibrate-mobile-dock' ),
			self::ACTION_WC_SHOP     => __( 'WooCommerce Shop', 'wpcalibrate-mobile-dock' ),
			self::ACTION_WC_CATEGORY => __( 'WooCommerce Category', 'wpcalibrate-mobile-dock' ),
			self::ACTION_WC_CART     => __( 'WooCommerce Cart', 'wpcalibrate-mobile-dock' ),
			self::ACTION_WC_ACCOUNT  => __( 'WooCommerce My Account', 'wpcalibrate-mobile-dock' ),
		];

		/**
		 * Filter supported mobile dock action types.
		 *
		 * @param array $types Array of key => label pairs.
		 */
		return apply_filters( 'wpcalibrate_mobile_dock_action_types', $types );
	}

	public function __construct(
		public string $id,
		public bool $enabled = true,
		public string $label = '',
		public string $icon = 'home',
		public string $action_type = self::ACTION_HOME,
		public string $target = '',
		public bool $open_new_tab = false,
		public string $rel = '',
		public string $css_class = '',
		public string $accessibility_label = '',
		public bool $is_center_raised = false,
		public string $user_visibility = 'all', // all, logged_in, logged_out
		public string $active_matching = 'auto',
		public array $extra = []
	) {
		// Clean and validate CSS class (only valid alphanumeric, dashes, underscores, spaces)
		$this->css_class = self::sanitize_css_classes( $this->css_class );
	}

	public static function sanitize_css_classes( string $classes ): string {
		$parts = preg_split( '/\s+/', trim( $classes ) );
		$valid = [];
		foreach ( $parts as $part ) {
			// CSS class name identifier check
			if ( preg_match( '/^[a-zA-Z_][a-zA-Z0-9_-]*$/', $part ) ) {
				$valid[] = sanitize_html_class( $part );
			}
		}
		return implode( ' ', array_unique( $valid ) );
	}

	public static function from_array( array $data ): self {
		$id                  = sanitize_key( $data['id'] ?? uniqid( 'item_', true ) );
		$enabled             = isset( $data['enabled'] ) ? (bool) $data['enabled'] : true;
		$label               = sanitize_text_field( $data['label'] ?? '' );
		$icon                = sanitize_key( $data['icon'] ?? 'home' );
		$action_type         = sanitize_key( $data['action_type'] ?? self::ACTION_HOME );
		$target              = sanitize_text_field( $data['target'] ?? '' );
		$open_new_tab        = ! empty( $data['open_new_tab'] );
		$rel                 = sanitize_text_field( $data['rel'] ?? '' );
		$css_class           = self::sanitize_css_classes( $data['css_class'] ?? '' );
		$accessibility_label = sanitize_text_field( $data['accessibility_label'] ?? '' );
		$is_center_raised    = ! empty( $data['is_center_raised'] );
		$raw_user_vis        = $data['user_visibility'] ?? 'all';
		$user_visibility     = in_array( $raw_user_vis, [ 'all', 'logged_in', 'logged_out' ], true )
			? (string) $raw_user_vis
			: 'all';
		$active_matching     = sanitize_key( $data['active_matching'] ?? 'auto' );
		$extra               = is_array( $data['extra'] ?? null ) ? $data['extra'] : [];

		return new self(
			$id,
			$enabled,
			$label,
			$icon,
			$action_type,
			$target,
			$open_new_tab,
			$rel,
			$css_class,
			$accessibility_label,
			$is_center_raised,
			$user_visibility,
			$active_matching,
			$extra
		);
	}

	public function to_array(): array {
		return [
			'id'                  => $this->id,
			'enabled'             => $this->enabled,
			'label'               => $this->label,
			'icon'                => $this->icon,
			'action_type'         => $this->action_type,
			'target'              => $this->target,
			'open_new_tab'        => $this->open_new_tab,
			'rel'                 => $this->rel,
			'css_class'           => $this->css_class,
			'accessibility_label' => $this->accessibility_label,
			'is_center_raised'    => $this->is_center_raised,
			'user_visibility'     => $this->user_visibility,
			'active_matching'     => $this->active_matching,
			'extra'               => $this->extra,
		];
	}
}
