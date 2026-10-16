<?php
/**
 * WooCommerce Integration Tab Template.
 *
 * @var array $settings Plugin settings array.
 * @package WPCalibrate\MobileDock\Admin
 */

use WPCalibrate\MobileDock\Navigation\SettingsRegistry;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

$settings     = isset( $settings ) && is_array( $settings ) ? $settings : SettingsRegistry::get_all();
$is_wc_active = WooCommerceIntegration::is_active();
?>
<div class="wpcal-section-card">
	<div class="wpcal-card-header">
		<h2><?php esc_html_e( 'WooCommerce Integration', 'wpcalibrate-mobile-dock' ); ?></h2>
		<p class="wpcal-desc">
			<?php esc_html_e( 'WooCommerce-aware navigation actions and live cart count badge settings.', 'wpcalibrate-mobile-dock' ); ?>
		</p>
	</div>

	<div class="wpcal-status-box <?php echo $is_wc_active ? 'wpcal-status-box--active' : 'wpcal-status-box--inactive'; ?>">
		<span class="dashicons <?php echo $is_wc_active ? 'dashicons-yes-alt' : 'dashicons-info'; ?>"></span>
		<div>
			<strong><?php esc_html_e( 'Integration Status', 'wpcalibrate-mobile-dock' ); ?>:</strong>
			<?php if ( $is_wc_active ) : ?>
				<span class="wpcal-text-success"><?php esc_html_e( 'Active & Ready. WooCommerce endpoints and cart fragments are enabled.', 'wpcalibrate-mobile-dock' ); ?></span>
			<?php else : ?>
				<span class="wpcal-text-muted"><?php esc_html_e( 'WooCommerce is not active. This plugin continues to work normally for all WordPress pages and blog posts.', 'wpcalibrate-mobile-dock' ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock' ) ); ?>">
		<?php wp_nonce_field( 'wpcal_save_settings_nonce', '_wpcal_nonce' ); ?>
		<input type="hidden" name="wpcal_action" value="save_settings" />
		<input type="hidden" name="current_tab" value="woocommerce" />

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Live Cart Badge', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="settings[enable_cart_badge]" value="1" <?php checked( $settings['enable_cart_badge'] ); ?> <?php disabled( ! $is_wc_active ); ?> />
						<?php esc_html_e( 'Display a live item count badge on WooCommerce Cart navigation items', 'wpcalibrate-mobile-dock' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Automatically updates via WooCommerce AJAX cart fragments whenever items are added or removed.', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Badge Background Color', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[cart_badge_bg]" value="<?php echo esc_attr( $settings['cart_badge_bg'] ); ?>" class="wpcal-color-picker" <?php disabled( ! $is_wc_active ); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Badge Text Color', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[cart_badge_text]" value="<?php echo esc_attr( $settings['cart_badge_text'] ); ?>" class="wpcal-color-picker" <?php disabled( ! $is_wc_active ); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Max Badge Number', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="number" name="settings[cart_badge_max]" value="<?php echo esc_attr( $settings['cart_badge_max'] ); ?>" min="9" max="999" class="small-text" <?php disabled( ! $is_wc_active ); ?> />
					<p class="description"><?php esc_html_e( 'Displays as "99+" if cart contents exceed this limit.', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<button type="submit" class="button button-primary button-large" <?php disabled( ! $is_wc_active ); ?>>
				<?php esc_html_e( 'Save WooCommerce Settings', 'wpcalibrate-mobile-dock' ); ?>
			</button>
		</p>
	</form>
</div>
