<?php
/**
 * Support Tab Template.
 *
 * @package WPCalibrate\MobileDock\Admin
 */

use WPCalibrate\MobileDock\Support\SupportService;

defined( 'ABSPATH' ) || exit;

$report = SupportService::get_system_report();
?>
<div class="wpcal-section-card">
	<div class="wpcal-card-header">
		<h2><?php esc_html_e( 'Help & Official Support', 'wpcalibrate-mobile-dock' ); ?></h2>
		<p class="wpcal-desc">
			<?php esc_html_e( 'Need assistance, found a bug, or have a feature request? Reach out to our dedicated support team.', 'wpcalibrate-mobile-dock' ); ?>
		</p>
	</div>

	<div class="wpcal-grid-2 wpcal-support-grid">
		<div class="wpcal-support-card">
			<div class="wpcal-support-icon">✉️</div>
			<h3><?php esc_html_e( 'Email Support', 'wpcalibrate-mobile-dock' ); ?></h3>
			<p><?php esc_html_e( 'Reach our developer support team directly:', 'wpcalibrate-mobile-dock' ); ?></p>
			<a href="mailto:<?php echo esc_attr( SupportService::EMAIL ); ?>" class="button button-secondary">
				<?php echo esc_html( SupportService::EMAIL ); ?>
			</a>
		</div>

		<div class="wpcal-support-card">
			<div class="wpcal-support-icon">💬</div>
			<h3><?php esc_html_e( 'WhatsApp / Phone', 'wpcalibrate-mobile-dock' ); ?></h3>
			<p><?php esc_html_e( 'Instant messaging and telephone assistance:', 'wpcalibrate-mobile-dock' ); ?></p>
			<a href="https://api.whatsapp.com/send?phone=447474795976" target="_blank" rel="noopener noreferrer" class="button button-secondary">
				<?php echo esc_html( SupportService::PHONE ); ?>
			</a>
		</div>

		<div class="wpcal-support-card">
			<div class="wpcal-support-icon">🌐</div>
			<h3><?php esc_html_e( 'Official Website', 'wpcalibrate-mobile-dock' ); ?></h3>
			<p><?php esc_html_e( 'Product updates, documentation, and tutorials:', 'wpcalibrate-mobile-dock' ); ?></p>
			<a href="<?php echo esc_url( SupportService::WEBSITE ); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary">
				<?php echo esc_html( SupportService::WEBSITE ); ?>
			</a>
		</div>

		<div class="wpcal-support-card">
			<div class="wpcal-support-icon">🛍️</div>
			<h3><?php esc_html_e( 'WPCalibrate Marketplace', 'wpcalibrate-mobile-dock' ); ?></h3>
			<p><?php esc_html_e( 'Browse our full ecosystem of high-performance plugins:', 'wpcalibrate-mobile-dock' ); ?></p>
			<a href="<?php echo esc_url( SupportService::MARKETPLACE ); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary">
				<?php echo esc_html( SupportService::MARKETPLACE ); ?>
			</a>
		</div>
	</div>

	<hr class="wpcal-divider" />

	<div class="wpcal-card-header">
		<h3><?php esc_html_e( 'System Status Report', 'wpcalibrate-mobile-dock' ); ?></h3>
		<p class="wpcal-desc"><?php esc_html_e( 'Include this information when requesting support to help us diagnose issues faster.', 'wpcalibrate-mobile-dock' ); ?></p>
	</div>

	<table class="widefat striped wpcal-status-table">
		<tbody>
			<?php foreach ( $report as $key => $val ) : ?>
				<tr>
					<th scope="row" style="width: 240px; font-weight: 600;"><?php echo esc_html( $key ); ?></th>
					<td><code><?php echo esc_html( $val ); ?></code></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
