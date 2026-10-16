<?php
/**
 * Global Visibility Tab Template.
 *
 * @var array $settings Plugin settings array.
 * @package WPCalibrate\MobileDock\Admin
 */

use WPCalibrate\MobileDock\Navigation\SettingsRegistry;

defined( 'ABSPATH' ) || exit;

$settings       = isset( $settings ) && is_array( $settings ) ? $settings : SettingsRegistry::get_all();
$all_pages      = get_pages( [ 'number' => 150 ] );
$excluded_pages = (array) ( $settings['global_excluded_pages'] ?? [] );
?>
<div class="wpcal-section-card">
	<div class="wpcal-card-header">
		<h2><?php esc_html_e( 'Global Visibility Defaults', 'wpcalibrate-mobile-dock' ); ?></h2>
		<p class="wpcal-desc">
			<?php esc_html_e( 'Set default breakpoints and site-wide exclusion rules.', 'wpcalibrate-mobile-dock' ); ?>
		</p>
	</div>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock' ) ); ?>">
		<?php wp_nonce_field( 'wpcal_save_settings_nonce', '_wpcal_nonce' ); ?>
		<input type="hidden" name="wpcal_action" value="save_settings" />
		<input type="hidden" name="current_tab" value="visibility" />

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Default Mobile Breakpoint', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="number" name="settings[default_breakpoint]" value="<?php echo esc_attr( $settings['default_breakpoint'] ); ?>" min="320" max="1920" class="small-text" /> px
					<p class="description"><?php esc_html_e( 'Navigation displays only when screen width is at or below this value (default: 768px).', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Tablet Visibility', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="settings[show_on_tablet]" value="1" id="wpcal-global-tablet-toggle" <?php checked( $settings['show_on_tablet'] ); ?> />
						<?php esc_html_e( 'Allow docks to display on tablet devices by default', 'wpcalibrate-mobile-dock' ); ?>
					</label>
				</td>
			</tr>
			<tr id="wpcal-global-tablet-bp-wrap" style="<?php echo $settings['show_on_tablet'] ? '' : 'display:none;'; ?>">
				<th scope="row"><?php esc_html_e( 'Tablet Maximum Breakpoint', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="number" name="settings[tablet_breakpoint]" value="<?php echo esc_attr( $settings['tablet_breakpoint'] ); ?>" min="480" max="2560" class="small-text" /> px
					<p class="description"><?php esc_html_e( 'Maximum width for tablet display (default: 1024px).', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Global Excluded Pages', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<select name="settings[global_excluded_pages][]" multiple class="wpcal-select2" style="width: 100%; max-width: 480px; height: 160px;">
						<?php foreach ( $all_pages as $p ) : ?>
							<option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( in_array( $p->ID, $excluded_pages, true ) ); ?>>
								<?php echo esc_html( $p->post_title ); ?> (ID: <?php echo esc_html( $p->ID ); ?>)
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Hold Ctrl/Cmd to select multiple pages where no mobile dock will ever render.', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<button type="submit" class="button button-primary button-large">
				<?php esc_html_e( 'Save Visibility Defaults', 'wpcalibrate-mobile-dock' ); ?>
			</button>
		</p>
	</form>
</div>
