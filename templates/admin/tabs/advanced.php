<?php
/**
 * Advanced Settings Tab Template.
 *
 * @var array $settings Plugin settings array.
 * @package WPCalibrate\MobileDock\Admin
 */

use WPCalibrate\MobileDock\Navigation\SettingsRegistry;

defined( 'ABSPATH' ) || exit;

$settings = isset( $settings ) && is_array( $settings ) ? $settings : SettingsRegistry::get_all();
?>
<div class="wpcal-section-card">
	<div class="wpcal-card-header">
		<h2><?php esc_html_e( 'Advanced Settings', 'wpcalibrate-mobile-dock' ); ?></h2>
		<p class="wpcal-desc">
			<?php esc_html_e( 'Developer customizations, data retention policies, and maintenance utilities.', 'wpcalibrate-mobile-dock' ); ?>
		</p>
	</div>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock' ) ); ?>">
		<?php wp_nonce_field( 'wpcal_save_settings_nonce', '_wpcal_nonce' ); ?>
		<input type="hidden" name="wpcal_action" value="save_settings" />
		<input type="hidden" name="current_tab" value="advanced" />

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Custom CSS', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<textarea name="settings[custom_css]" rows="8" class="large-text code" placeholder="#wpcal-mobile-dock { /* custom CSS rules */ }"><?php echo esc_textarea( $settings['custom_css'] ); ?></textarea>
					<p class="description">
						<?php esc_html_e( 'Add custom CSS scoped to the mobile dock. Example:', 'wpcalibrate-mobile-dock' ); ?><br />
						<code>#wpcal-mobile-dock .wpcal-item__label { font-weight: 600; }</code><br />
						<em><?php esc_html_e( 'Script tags, HTML tags, and dangerous executable syntax will be stripped on save.', 'wpcalibrate-mobile-dock' ); ?></em>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Data Retention on Uninstall', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="settings[delete_data_on_uninstall]" value="1" <?php checked( $settings['delete_data_on_uninstall'] ); ?> />
						<strong><?php esc_html_e( 'Delete all plugin data when uninstalled', 'wpcalibrate-mobile-dock' ); ?></strong>
					</label>
					<p class="description">
						<?php esc_html_e( 'Default: OFF. When unchecked, plugin settings and navigation docks are preserved if the plugin is uninstalled. When checked, uninstalling this plugin completely cleans up its options, metadata, and custom post configurations.', 'wpcalibrate-mobile-dock' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Debug Logging', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="settings[enable_logging]" value="1" <?php checked( $settings['enable_logging'] ); ?> />
						<?php esc_html_e( 'Enable diagnostic logging to error_log (only when WP_DEBUG is enabled)', 'wpcalibrate-mobile-dock' ); ?>
					</label>
				</td>
			</tr>
		</table>

		<p class="submit">
			<button type="submit" class="button button-primary button-large">
				<?php esc_html_e( 'Save Advanced Settings', 'wpcalibrate-mobile-dock' ); ?>
			</button>
		</p>
	</form>

	<hr class="wpcal-divider" />

	<!-- Reset Settings Section -->
	<div class="wpcal-danger-zone">
		<h3><?php esc_html_e( 'Reset Plugin Settings', 'wpcalibrate-mobile-dock' ); ?></h3>
		<p class="description">
			<?php esc_html_e( 'Reset all global appearance, visibility, and WooCommerce options back to their original factory defaults. Existing navigation docks will not be deleted.', 'wpcalibrate-mobile-dock' ); ?>
		</p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure you want to reset all plugin settings to factory defaults? This action cannot be undone.', 'wpcalibrate-mobile-dock' ) ); ?>');">
			<?php wp_nonce_field( 'wpcal_reset_settings_nonce', '_wpcal_nonce' ); ?>
			<input type="hidden" name="wpcal_action" value="reset_settings" />
			<button type="submit" class="button button-secondary button-link-delete">
				<?php esc_html_e( 'Reset All Settings to Defaults', 'wpcalibrate-mobile-dock' ); ?>
			</button>
		</form>
	</div>
</div>
