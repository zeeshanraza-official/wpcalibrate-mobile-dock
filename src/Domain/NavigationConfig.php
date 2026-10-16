<?php
/**
 * Navigation Configuration Aggregate Root / Value Object.
 *
 * @package WPCalibrate\MobileDock\Domain
 */

namespace WPCalibrate\MobileDock\Domain;

defined( 'ABSPATH' ) || exit;

class NavigationConfig {

	/**
	 * @param int $id Post ID (0 for new)
	 * @param string $title Navigation dock title
	 * @param string $status 'publish' (active) or 'draft' (inactive)
	 * @param bool $is_default Whether this is the default dock
	 * @param int $priority Priority ordering for conflict resolution (lower number = higher precedence)
	 * @param NavigationItem[] $items Items collection
	 * @param AppearanceSettings $appearance Appearance overrides
	 * @param VisibilityRule[] $visibility_rules Visibility rules collection
	 * @param int $breakpoint Breakpoint in px (default 768)
	 * @param bool $show_on_tablet Allow tablet display
	 * @param int $tablet_breakpoint Tablet max breakpoint in px (default 1024)
	 */
	public function __construct(
		public int $id = 0,
		public string $title = 'Mobile Dock',
		public string $status = 'publish',
		public bool $is_default = false,
		public int $priority = 10,
		public array $items = [],
		public AppearanceSettings $appearance = new AppearanceSettings(),
		public array $visibility_rules = [],
		public int $breakpoint = 768,
		public bool $show_on_tablet = false,
		public int $tablet_breakpoint = 1024
	) {
		$this->enforce_center_button_uniqueness();
	}

	/**
	 * Enforce that at most ONE item has is_center_raised = true.
	 */
	public function enforce_center_button_uniqueness(): void {
		$found_center = false;
		foreach ( $this->items as $item ) {
			if ( $item->is_center_raised ) {
				if ( $found_center ) {
					// Second one found - unset it
					$item->is_center_raised = false;
				} else {
					$found_center = true;
				}
			}
		}
	}

	/**
	 * Returns only enabled items.
	 *
	 * @return NavigationItem[]
	 */
	public function get_enabled_items(): array {
		return array_values(
			array_filter(
				$this->items,
				static fn( NavigationItem $item ) => $item->enabled
			)
		);
	}

	public static function from_post( \WP_Post $post, array $meta = [] ): self {
		$title      = $post->post_title;
		$status     = $post->post_status;
		$id         = $post->ID;
		$is_default = ! empty( $meta['is_default'] );
		$priority   = isset( $meta['priority'] ) ? (int) $meta['priority'] : 10;
		$breakpoint = isset( $meta['breakpoint'] ) ? absint( $meta['breakpoint'] ) : 768;
		$on_tablet  = ! empty( $meta['show_on_tablet'] );
		$tab_bp     = isset( $meta['tablet_breakpoint'] ) ? absint( $meta['tablet_breakpoint'] ) : 1024;

		$items = [];
		if ( ! empty( $meta['items'] ) && is_array( $meta['items'] ) ) {
			foreach ( $meta['items'] as $item_data ) {
				if ( is_array( $item_data ) ) {
					$items[] = NavigationItem::from_array( $item_data );
				}
			}
		}

		$appearance = isset( $meta['appearance'] ) && is_array( $meta['appearance'] )
			? AppearanceSettings::from_array( $meta['appearance'] )
			: new AppearanceSettings();

		$rules = [];
		if ( ! empty( $meta['visibility_rules'] ) && is_array( $meta['visibility_rules'] ) ) {
			foreach ( $meta['visibility_rules'] as $rule_data ) {
				if ( is_array( $rule_data ) ) {
					$rules[] = VisibilityRule::from_array( $rule_data );
				}
			}
		}

		return new self(
			$id,
			$title,
			$status,
			$is_default,
			$priority,
			$items,
			$appearance,
			$rules,
			$breakpoint,
			$on_tablet,
			$tab_bp
		);
	}

	public function to_meta_array(): array {
		$items_arr = array_map(
			static fn( NavigationItem $i ) => $i->to_array(),
			$this->items
		);

		$rules_arr = array_map(
			static fn( VisibilityRule $r ) => $r->to_array(),
			$this->visibility_rules
		);

		return [
			'is_default'        => $this->is_default,
			'priority'          => $this->priority,
			'breakpoint'        => $this->breakpoint,
			'show_on_tablet'    => $this->show_on_tablet,
			'tablet_breakpoint' => $this->tablet_breakpoint,
			'items'             => $items_arr,
			'appearance'        => $this->appearance->to_array(),
			'visibility_rules'  => $rules_arr,
		];
	}
}
