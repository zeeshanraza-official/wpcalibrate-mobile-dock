<?php
/**
 * License Settings Tab Template.
 *
 * @package WPCalibrate\MobileDock\Admin
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wpcal-section-card">
	<div class="wpcal-card-header">
		<h2><?php esc_html_e( 'License Management', 'wpcalibrate-mobile-dock' ); ?></h2>
		<p class="wpcal-desc">
			<?php esc_html_e( 'Manage your product license and automatic updates.', 'wpcalibrate-mobile-dock' ); ?>
		</p>
	</div>

	<!-- Unconfigured Provider Notice -->
	<div class="notice notice-info inline wpcal-notice">
		<p>
			<strong><?php esc_html_e( 'Notice:', 'wpcalibrate-mobile-dock' ); ?></strong>
			<?php esc_html_e( 'Licensing integration has not been configured for this build. All core features included in version 1.0.0 are fully functional.', 'wpcalibrate-mobile-dock' ); ?>
		</p>
	</div>

	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e( 'License Key', 'wpcalibrate-mobile-dock' ); ?></th>
			<td>
				<input type="password" value="" placeholder="WPCAL-XXXX-XXXX-XXXX" class="regular-text code" disabled />
				<p class="description"><?php esc_html_e( 'Licensing server provider is not active for this build.', 'wpcalibrate-mobile-dock' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'License Status', 'wpcalibrate-mobile-dock' ); ?></th>
			<td>
				<span class="wpcal-badge wpcal-badge--muted"><?php esc_html_e( 'Unconfigured (Core Edition)', 'wpcalibrate-mobile-dock' ); ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Updates & Marketplace', 'wpcalibrate-mobile-dock' ); ?></th>
			<td>
				<a href="https://marketplace.wpcalibrate.com/" target="_blank" rel="noopener noreferrer" class="button button-secondary">
					<?php esc_html_e( 'Visit WPCalibrate Marketplace', 'wpcalibrate-mobile-dock' ); ?> &rarr;
				</a>
			</td>
		</tr>
	</table>
</div>
