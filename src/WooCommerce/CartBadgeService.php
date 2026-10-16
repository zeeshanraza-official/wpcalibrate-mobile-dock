<?php
/**
 * WooCommerce Cart Badge Service.
 *
 * Provides live cart quantity badge rendering and AJAX fragment support.
 *
 * @package WPCalibrate\MobileDock\WooCommerce
 */

namespace WPCalibrate\MobileDock\WooCommerce;

use WPCalibrate\MobileDock\Navigation\SettingsRegistry;

defined( 'ABSPATH' ) || exit;

class CartBadgeService {

	/**
	 * Register WooCommerce cart fragment hook.
	 */
	public function init(): void {
		if ( WooCommerceIntegration::is_active() ) {
			add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'add_cart_fragment' ] );
		}
	}

	/**
	 * Hook into woocommerce_add_to_cart_fragments to return updated badge markup.
	 *
	 * @param array $fragments
	 * @return array
	 */
	public function add_cart_fragment( array $fragments ): array {
		$fragments['span.wpcal-dock-badge-wrapper'] = $this->render_badge_wrapper();
		return $fragments;
	}

	/**
	 * Renders the wrapper and inner badge for server and fragment rendering.
	 *
	 * @return string
	 */
	public function render_badge_wrapper(): string {
		$enabled = (bool) SettingsRegistry::get( 'enable_cart_badge', true );
		if ( ! $enabled ) {
			return '<span class="wpcal-dock-badge-wrapper" style="display:none;"></span>';
		}

		$count = WooCommerceIntegration::get_cart_count();
		$display_style = ( $count <= 0 ) ? 'display:none;' : '';

		$max_badge = (int) SettingsRegistry::get( 'cart_badge_max', 99 );
		$display_text = ( $count > $max_badge ) ? $max_badge . '+' : (string) $count;

		$sr_text = sprintf(
			/* translators: %d: number of items in cart */
			_n( 'Cart, %d item', 'Cart, %d items', $count, 'wpcalibrate-mobile-dock' ),
			$count
		);

		ob_start();
		?>
		<span class="wpcal-dock-badge-wrapper" style="<?php echo esc_attr( $display_style ); ?>" data-cart-count="<?php echo esc_attr( $count ); ?>">
			<span class="screen-reader-text wpcal-cart-sr-label"><?php echo esc_html( $sr_text ); ?></span>
			<span class="wpcal-dock-badge" aria-hidden="true"><?php echo esc_html( $display_text ); ?></span>
		</span>
		<?php
		return trim( ob_get_clean() );
	}
}
