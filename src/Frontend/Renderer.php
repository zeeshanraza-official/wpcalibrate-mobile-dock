<?php
/**
 * Frontend Navigation Renderer.
 *
 * Server-renders semantic, accessible mobile dock markup and injects
 * validated CSS custom properties.
 *
 * @package WPCalibrate\MobileDock\Frontend
 */

namespace WPCalibrate\MobileDock\Frontend;

use WPCalibrate\MobileDock\Domain\AppearanceSettings;
use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Icons\IconRegistry;
use WPCalibrate\MobileDock\Navigation\SettingsRegistry;
use WPCalibrate\MobileDock\Rules\RuleEngine;
use WPCalibrate\MobileDock\WooCommerce\CartBadgeService;

defined( 'ABSPATH' ) || exit;

class Renderer {

	public function __construct(
		private ActionResolver $action_resolver,
		private ActiveStateMatcher $active_matcher,
		private SearchController $search_controller,
		private CartBadgeService $cart_badge_service,
		private RuleEngine $rule_engine
	) {}

	/**
	 * Render the mobile navigation dock.
	 *
	 * @param NavigationConfig $config Matched configuration.
	 */
	public function render( NavigationConfig $config ): void {
		/**
		 * Action before mobile dock rendering.
		 *
		 * @param NavigationConfig $config
		 */
		do_action( 'wpcalibrate_mobile_dock_before_render', $config );

		$items = $config->get_enabled_items();
		if ( empty( $items ) ) {
			return;
		}

		// Filter items by item-level visibility rules.
		$visible_items = [];
		$has_search    = false;

		foreach ( $items as $item ) {
			if ( $this->rule_engine->is_item_visible( $item ) ) {
				$visible_items[] = $item;
				if ( NavigationItem::ACTION_SEARCH === $item->action_type ) {
					$has_search = true;
				}
			}
		}

		if ( empty( $visible_items ) ) {
			return;
		}

		// Merge appearance: per-dock appearance overrides if enabled, else global appearance.
		$global_settings     = SettingsRegistry::get_all();
		$effective_appearance = $config->appearance->override_global
			? $config->appearance
			: AppearanceSettings::from_array( $global_settings );

		$css_vars = $effective_appearance->to_css_variables();
		$var_strings = [];
		foreach ( $css_vars as $prop => $val ) {
			$var_strings[] = sprintf( '%s: %s;', esc_attr( $prop ), esc_attr( $val ) );
		}
		$inline_vars = implode( ' ', $var_strings );

		// Container classes
		$wrapper_classes = [
			'wpcal-mobile-dock',
			'wpcal-layout-' . sanitize_html_class( $effective_appearance->layout_mode ),
			'wpcal-preset-' . sanitize_html_class( $effective_appearance->preset ),
		];

		if ( ! $effective_appearance->show_labels ) {
			$wrapper_classes[] = 'wpcal-hide-labels';
		}

		/**
		 * Filter mobile dock wrapper classes.
		 *
		 * @param array            $wrapper_classes
		 * @param NavigationConfig $config
		 */
		$wrapper_classes = apply_filters( 'wpcalibrate_mobile_dock_wrapper_classes', $wrapper_classes, $config );
		$class_attr      = esc_attr( implode( ' ', array_unique( $wrapper_classes ) ) );

		// Render dynamic layout styles for breakpoint & body padding
		$this->render_layout_styles( $config, $effective_appearance );

		?>
		<nav
			id="wpcal-mobile-dock"
			class="<?php echo $class_attr; ?>"
			style="<?php echo esc_attr( $inline_vars ); ?>"
			aria-label="<?php esc_attr_e( 'Mobile Navigation', 'wpcalibrate-mobile-dock' ); ?>"
			role="navigation"
		>
			<div class="wpcal-mobile-dock__inner">
				<ul class="wpcal-mobile-dock__items" role="menubar">
					<?php foreach ( $visible_items as $item ) : ?>
						<?php $this->render_item( $item, $config ); ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</nav>
		<?php

		// Output search modal overlay if search action exists.
		if ( $has_search ) {
			echo $this->search_controller->render_modal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Action after mobile dock rendering.
		 *
		 * @param NavigationConfig $config
		 */
		do_action( 'wpcalibrate_mobile_dock_after_render', $config );
	}

	/**
	 * Render an individual dock item.
	 *
	 * @param NavigationItem   $item
	 * @param NavigationConfig $config
	 */
	private function render_item( NavigationItem $item, NavigationConfig $config ): void {
		$resolved  = $this->action_resolver->resolve( $item );
		$is_active = $this->active_matcher->is_active( $item );

		$item_classes = [
			'wpcal-item',
			'wpcal-action-' . sanitize_html_class( $item->action_type ),
		];

		if ( $item->is_center_raised ) {
			$item_classes[] = 'wpcal-item--center-raised';
		}

		if ( $is_active ) {
			$item_classes[] = 'wpcal-item--active';
		}

		if ( ! empty( $item->css_class ) ) {
			$item_classes[] = $item->css_class;
		}

		/**
		 * Filter mobile dock item classes.
		 *
		 * @param array            $item_classes
		 * @param NavigationItem   $item
		 * @param NavigationConfig $config
		 */
		$item_classes = apply_filters( 'wpcalibrate_mobile_dock_item_classes', $item_classes, $item, $config );
		$class_string = esc_attr( implode( ' ', array_unique( $item_classes ) ) );

		// Accessible label
		$accessible_label = ! empty( $item->accessibility_label ) ? $item->accessibility_label : $item->label;

		// Attribute string building
		$extra_attrs = [];
		foreach ( $resolved['attributes'] as $k => $v ) {
			$extra_attrs[] = sprintf( '%s="%s"', sanitize_key( $k ), esc_attr( $v ) );
		}

		if ( $is_active && ! $resolved['is_button'] ) {
			$extra_attrs[] = 'aria-current="page"';
		}

		$attr_string = ! empty( $extra_attrs ) ? ' ' . implode( ' ', $extra_attrs ) : '';

		// Icon SVG
		$icon_svg = IconRegistry::get_svg(
			$item->icon,
			[
				'class'       => 'wpcal-dock-icon',
				'aria-hidden' => 'true',
				'focusable'   => 'false',
			]
		);

		?>
		<li class="<?php echo $class_string; ?>" role="none">
			<?php if ( $resolved['is_button'] ) : ?>
				<button
					class="wpcal-item__link"
					aria-label="<?php echo esc_attr( $accessible_label ); ?>"
					<?php echo $attr_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				>
					<span class="wpcal-item__icon-wrap">
						<?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php if ( NavigationItem::ACTION_WC_CART === $item->action_type ) : ?>
							<?php echo $this->cart_badge_service->render_badge_wrapper(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</span>
					<?php if ( ! empty( $item->label ) ) : ?>
						<span class="wpcal-item__label"><?php echo esc_html( $item->label ); ?></span>
					<?php endif; ?>
				</button>
			<?php else : ?>
				<a
					href="<?php echo esc_url( $resolved['href'] ); ?>"
					class="wpcal-item__link"
					aria-label="<?php echo esc_attr( $accessible_label ); ?>"
					<?php echo $attr_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				>
					<span class="wpcal-item__icon-wrap">
						<?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php if ( NavigationItem::ACTION_WC_CART === $item->action_type ) : ?>
							<?php echo $this->cart_badge_service->render_badge_wrapper(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</span>
					<?php if ( ! empty( $item->label ) ) : ?>
						<span class="wpcal-item__label"><?php echo esc_html( $item->label ); ?></span>
					<?php endif; ?>
				</a>
			<?php endif; ?>
		</li>
		<?php
	}

	/**
	 * Output layout and responsive media query styles safely.
	 *
	 * @param NavigationConfig   $config
	 * @param AppearanceSettings $appearance
	 */
	private function render_layout_styles( NavigationConfig $config, AppearanceSettings $appearance ): void {
		$bp = $config->breakpoint;
		if ( $config->show_on_tablet && $config->tablet_breakpoint > $bp ) {
			$bp = $config->tablet_breakpoint;
		}

		$height = $appearance->height;
		?>
		<style id="wpcal-mobile-dock-layout">
			@media (max-width: <?php echo absint( $bp ); ?>px) {
				body.wpcal-has-mobile-dock {
					padding-bottom: calc(<?php echo esc_html( $height ); ?> + env(safe-area-inset-bottom, 0px)) !important;
				}
				#wpcal-mobile-dock {
					display: flex !important;
				}
			}
			@media (min-width: <?php echo absint( $bp + 1 ); ?>px) {
				#wpcal-mobile-dock {
					display: none !important;
				}
			}
		</style>
		<?php
	}
}
