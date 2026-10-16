<?php
/**
 * Global Appearance Tab Template.
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
		<h2><?php esc_html_e( 'Global Appearance Defaults', 'wpcalibrate-mobile-dock' ); ?></h2>
		<p class="wpcal-desc">
			<?php esc_html_e( 'Configure the default styling for all bottom navigation docks on your site. Individual docks can override these settings.', 'wpcalibrate-mobile-dock' ); ?>
		</p>
	</div>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock' ) ); ?>">
		<?php wp_nonce_field( 'wpcal_save_settings_nonce', '_wpcal_nonce' ); ?>
		<input type="hidden" name="wpcal_action" value="save_settings" />
		<input type="hidden" name="current_tab" value="appearance" />

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Design Preset', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<select name="settings[preset]" id="wpcal-global-preset-select">
						<option value="light" <?php selected( $settings['preset'], 'light' ); ?>><?php esc_html_e( 'Light Preset (Clean White)', 'wpcalibrate-mobile-dock' ); ?></option>
						<option value="dark" <?php selected( $settings['preset'], 'dark' ); ?>><?php esc_html_e( 'Dark Preset (Modern Charcoal)', 'wpcalibrate-mobile-dock' ); ?></option>
						<option value="custom" <?php selected( $settings['preset'], 'custom' ); ?>><?php esc_html_e( 'Custom Palette', 'wpcalibrate-mobile-dock' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Choosing a preset fills recommended color tokens. Any custom values entered below take precedence.', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Layout Mode', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<select name="settings[layout_mode]">
						<option value="fixed" <?php selected( $settings['layout_mode'], 'fixed' ); ?>><?php esc_html_e( 'Fixed Full Width (Edge to edge)', 'wpcalibrate-mobile-dock' ); ?></option>
						<option value="floating" <?php selected( $settings['layout_mode'], 'floating' ); ?>><?php esc_html_e( 'Floating Rounded Pill', 'wpcalibrate-mobile-dock' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Background Color', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[bg_color]" value="<?php echo esc_attr( $settings['bg_color'] ); ?>" class="wpcal-color-picker" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Icon Color', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[icon_color]" value="<?php echo esc_attr( $settings['icon_color'] ); ?>" class="wpcal-color-picker" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Active Item Icon Color', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[active_icon_color]" value="<?php echo esc_attr( $settings['active_icon_color'] ); ?>" class="wpcal-color-picker" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Label Text Color', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[text_color]" value="<?php echo esc_attr( $settings['text_color'] ); ?>" class="wpcal-color-picker" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Active Label Text Color', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[active_text_color]" value="<?php echo esc_attr( $settings['active_text_color'] ); ?>" class="wpcal-color-picker" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Display Labels', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="settings[show_labels]" value="1" <?php checked( $settings['show_labels'] ); ?> />
						<?php esc_html_e( 'Show text labels below icons', 'wpcalibrate-mobile-dock' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Navigation Height', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[height]" value="<?php echo esc_attr( $settings['height'] ); ?>" class="regular-text" style="max-width: 120px;" />
					<p class="description"><?php esc_html_e( 'Default: 60px', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Icon Size', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[icon_size]" value="<?php echo esc_attr( $settings['icon_size'] ); ?>" class="regular-text" style="max-width: 120px;" />
					<p class="description"><?php esc_html_e( 'Default: 22px', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Font Size', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[font_size]" value="<?php echo esc_attr( $settings['font_size'] ); ?>" class="regular-text" style="max-width: 120px;" />
					<p class="description"><?php esc_html_e( 'Default: 11px', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Border Width & Color', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[border_width]" value="<?php echo esc_attr( $settings['border_width'] ); ?>" class="regular-text" style="max-width: 80px;" placeholder="1px" />
					<input type="text" name="settings[border_color]" value="<?php echo esc_attr( $settings['border_color'] ); ?>" class="wpcal-color-picker" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Box Shadow', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[box_shadow]" value="<?php echo esc_attr( $settings['box_shadow'] ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'CSS box-shadow syntax (e.g. 0 -2px 10px rgba(0,0,0,0.06))', 'wpcalibrate-mobile-dock' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Center Button Background', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[center_button_bg]" value="<?php echo esc_attr( $settings['center_button_bg'] ); ?>" class="wpcal-color-picker" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Center Button Icon Color', 'wpcalibrate-mobile-dock' ); ?></th>
				<td>
					<input type="text" name="settings[center_button_icon_color]" value="<?php echo esc_attr( $settings['center_button_icon_color'] ); ?>" class="wpcal-color-picker" />
				</td>
			</tr>
		</table>

		<p class="submit">
			<button type="submit" class="button button-primary button-large">
				<?php esc_html_e( 'Save Appearance Defaults', 'wpcalibrate-mobile-dock' ); ?>
			</button>
		</p>
	</form>
</div>
