<?php
/**
 * Overview & Quick Start Tab Template.
 *
 * @package WPCalibrate\MobileDock\Admin
 *
 * @var array $settings Global plugin settings array.
 */

use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Navigation\NavigationRepository;
use WPCalibrate\MobileDock\Navigation\SettingsRegistry;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;
use WPCalibrate\MobileDock\Support\SupportService;

defined( 'ABSPATH' ) || exit;

$settings      = isset( $settings ) && is_array( $settings ) ? $settings : SettingsRegistry::get_all();
$repo          = new NavigationRepository();
$docks         = $repo->get_all( true );
$dock_count    = count( $docks );
$is_wc_active  = WooCommerceIntegration::is_active();
$brand_svg_url = WPCALIBRATE_MOBILE_DOCK_URL . 'assets/branding/wpcalibrate-icon.svg';
?>
<div class="wpcal-overview-wrap">
	<!-- Hero Welcome Banner -->
	<div class="wpcal-hero-banner">
		<div class="wpcal-hero-banner__content">
			<div class="wpcal-hero-banner__badge">
				<span class="wpcal-hero-pulse"></span>
				<?php esc_html_e( 'App-Style Mobile Navigation System', 'wpcalibrate-mobile-dock' ); ?>
			</div>
			<h2 class="wpcal-hero-banner__title">
				<?php esc_html_e( 'Deliver a Native App Experience on Mobile WordPress', 'wpcalibrate-mobile-dock' ); ?>
			</h2>
			<p class="wpcal-hero-banner__desc">
				<?php esc_html_e( 'WPCalibrate Mobile Dock places high-priority navigation actions right at your mobile visitors’ fingertips. Built for speed, full WCAG 2.2 AA accessibility, notch safe-area support, and seamless WooCommerce integration.', 'wpcalibrate-mobile-dock' ); ?>
			</p>
			<div class="wpcal-hero-banner__actions">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=navigations&view=new' ) ); ?>" class="button button-primary button-hero wpcal-btn-primary">
					<span class="dashicons dashicons-plus-alt2"></span>
					<?php esc_html_e( 'Create New Dock', 'wpcalibrate-mobile-dock' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=appearance' ) ); ?>" class="button button-secondary button-hero wpcal-btn-secondary">
					<span class="dashicons dashicons-art"></span>
					<?php esc_html_e( 'Customize Appearance', 'wpcalibrate-mobile-dock' ); ?>
				</a>
			</div>
		</div>

		<div class="wpcal-hero-banner__stats">
			<div class="wpcal-stat-pill">
				<div class="wpcal-stat-pill__icon">📱</div>
				<div class="wpcal-stat-pill__data">
					<span class="wpcal-stat-pill__value"><?php echo esc_html( (string) $dock_count ); ?></span>
					<span class="wpcal-stat-pill__label"><?php esc_html_e( 'Configured Docks', 'wpcalibrate-mobile-dock' ); ?></span>
				</div>
			</div>

			<div class="wpcal-stat-pill">
				<div class="wpcal-stat-pill__icon">🛒</div>
				<div class="wpcal-stat-pill__data">
					<span class="wpcal-stat-pill__value">
						<?php echo $is_wc_active ? esc_html__( 'Active', 'wpcalibrate-mobile-dock' ) : esc_html__( 'Standby', 'wpcalibrate-mobile-dock' ); ?>
					</span>
					<span class="wpcal-stat-pill__label"><?php esc_html_e( 'WooCommerce Cart Badge', 'wpcalibrate-mobile-dock' ); ?></span>
				</div>
			</div>

			<div class="wpcal-stat-pill">
				<div class="wpcal-stat-pill__icon">⚡</div>
				<div class="wpcal-stat-pill__data">
					<span class="wpcal-stat-pill__value">26</span>
					<span class="wpcal-stat-pill__label"><?php esc_html_e( 'Bundled SVG Icons', 'wpcalibrate-mobile-dock' ); ?></span>
				</div>
			</div>

			<div class="wpcal-stat-pill">
				<div class="wpcal-stat-pill__icon">♿</div>
				<div class="wpcal-stat-pill__data">
					<span class="wpcal-stat-pill__value">AA</span>
					<span class="wpcal-stat-pill__label"><?php esc_html_e( 'WCAG 2.2 Compliant', 'wpcalibrate-mobile-dock' ); ?></span>
				</div>
			</div>
		</div>
	</div>

	<!-- 3-Step Guided Workflow -->
	<div class="wpcal-section-header">
		<h3><?php esc_html_e( 'How It Works: 3-Step Setup Guide', 'wpcalibrate-mobile-dock' ); ?></h3>
		<p class="wpcal-desc"><?php esc_html_e( 'Get your mobile dock live and optimized in under five minutes.', 'wpcalibrate-mobile-dock' ); ?></p>
	</div>

	<div class="wpcal-grid-3 wpcal-step-grid">
		<!-- Step 1 -->
		<div class="wpcal-card wpcal-step-card">
			<div class="wpcal-step-number">01</div>
			<div class="wpcal-step-icon">🎨</div>
			<h4 class="wpcal-step-title"><?php esc_html_e( 'Build Your Dock Items', 'wpcalibrate-mobile-dock' ); ?></h4>
			<p class="wpcal-step-desc">
				<?php esc_html_e( 'Choose from 26 local SVG icons and assign high-impact actions like Home, Shop, Cart, Search Overlay, Phone, WhatsApp, or Custom Links. Elevate one key action with the Center Raised button style.', 'wpcalibrate-mobile-dock' ); ?>
			</p>
			<div class="wpcal-step-footer">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=navigations' ) ); ?>" class="wpcal-link-action">
					<?php esc_html_e( 'Configure Items', 'wpcalibrate-mobile-dock' ); ?> &rarr;
				</a>
			</div>
		</div>

		<!-- Step 2 -->
		<div class="wpcal-card wpcal-step-card">
			<div class="wpcal-step-number">02</div>
			<div class="wpcal-step-icon">🎯</div>
			<h4 class="wpcal-step-title"><?php esc_html_e( 'Target with Smart Rules', 'wpcalibrate-mobile-dock' ); ?></h4>
			<p class="wpcal-step-desc">
				<?php esc_html_e( 'Show different docks for different audiences or sections. Target the Entire Site, Front Page, Blog, specific Pages/Posts, WooCommerce Catalog, or restrict docks to Logged-in/Logged-out users.', 'wpcalibrate-mobile-dock' ); ?>
			</p>
			<div class="wpcal-step-footer">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=visibility' ) ); ?>" class="wpcal-link-action">
					<?php esc_html_e( 'Manage Visibility', 'wpcalibrate-mobile-dock' ); ?> &rarr;
				</a>
			</div>
		</div>

		<!-- Step 3 -->
		<div class="wpcal-card wpcal-step-card">
			<div class="wpcal-step-number">03</div>
			<div class="wpcal-step-icon">✨</div>
			<h4 class="wpcal-step-title"><?php esc_html_e( 'Style & Launch', 'wpcalibrate-mobile-dock' ); ?></h4>
			<p class="wpcal-step-desc">
				<?php esc_html_e( 'Match your brand typography and colors. Choose between Edge-to-Edge fixed bar or Floating Pill layout modes. Safe-area insets dynamically handle iPhone notch and home indicator spacing.', 'wpcalibrate-mobile-dock' ); ?>
			</p>
			<div class="wpcal-step-footer">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=appearance' ) ); ?>" class="wpcal-link-action">
					<?php esc_html_e( 'Adjust Appearance', 'wpcalibrate-mobile-dock' ); ?> &rarr;
				</a>
			</div>
		</div>
	</div>

	<!-- Purpose, Method & Technical Architecture -->
	<div class="wpcal-grid-2 wpcal-details-grid">
		<!-- Purpose & Method -->
		<div class="wpcal-card">
			<div class="wpcal-card-header">
				<h4><?php esc_html_e( 'Purpose & Core Methodology', 'wpcalibrate-mobile-dock' ); ?></h4>
			</div>
			<div class="wpcal-card-body">
				<div class="wpcal-feature-item">
					<div class="wpcal-feature-bullet">✓</div>
					<div class="wpcal-feature-text">
						<strong><?php esc_html_e( 'Thumb-Zone Optimized Navigation', 'wpcalibrate-mobile-dock' ); ?>:</strong>
						<?php esc_html_e( 'Desktop hamburger menus require awkward hand repositioning on mobile devices. A bottom dock keeps essential conversion actions in the natural one-handed thumb reach zone.', 'wpcalibrate-mobile-dock' ); ?>
					</div>
				</div>

				<div class="wpcal-feature-item">
					<div class="wpcal-feature-bullet">✓</div>
					<div class="wpcal-feature-text">
						<strong><?php esc_html_e( 'Deterministic Specificity Rule Engine', 'wpcalibrate-mobile-dock' ); ?>:</strong>
						<?php esc_html_e( 'Never guess which dock will display. The engine computes mathematical specificity scores (Exact Content: 400 > WooCommerce: 300 > Authentication: 200 > Global: 100), with dock priority tie-breakers.', 'wpcalibrate-mobile-dock' ); ?>
					</div>
				</div>

				<div class="wpcal-feature-item">
					<div class="wpcal-feature-bullet">✓</div>
					<div class="wpcal-feature-text">
						<strong><?php esc_html_e( 'Zero Layout Shift & Dynamic Spacing', 'wpcalibrate-mobile-dock' ); ?>:</strong>
						<?php esc_html_e( 'The plugin dynamically calculates the dock height plus safe-area insets, applying exact padding to the document body so sticky footers or copyright text are never obscured.', 'wpcalibrate-mobile-dock' ); ?>
					</div>
				</div>

				<div class="wpcal-feature-item">
					<div class="wpcal-feature-bullet">✓</div>
					<div class="wpcal-feature-text">
						<strong><?php esc_html_e( 'Bundled SVG Icons with Zero External Requests', 'wpcalibrate-mobile-dock' ); ?>:</strong>
						<?php esc_html_e( 'No FontAwesome, no Dashicons enqueued on the frontend, and zero external font CDN calls. 26 curated SVG icons render inline for maximum speed and Core Web Vitals performance.', 'wpcalibrate-mobile-dock' ); ?>
					</div>
				</div>
			</div>
		</div>

		<!-- System Diagnostics & Fast Shortcuts -->
		<div class="wpcal-card">
			<div class="wpcal-card-header">
				<h4><?php esc_html_e( 'System & Environment Snapshot', 'wpcalibrate-mobile-dock' ); ?></h4>
			</div>
			<div class="wpcal-card-body">
				<table class="wpcal-diag-table">
					<tbody>
						<tr>
							<td><strong><?php esc_html_e( 'Plugin Version', 'wpcalibrate-mobile-dock' ); ?></strong></td>
							<td><code>v<?php echo esc_html( WPCALIBRATE_MOBILE_DOCK_VERSION ); ?></code></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'PHP Environment', 'wpcalibrate-mobile-dock' ); ?></strong></td>
							<td><code>PHP <?php echo esc_html( PHP_VERSION ); ?></code> (8.2+ verified)</td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'WordPress Core', 'wpcalibrate-mobile-dock' ); ?></strong></td>
							<td><code>v<?php echo esc_html( get_bloginfo( 'version' ) ); ?></code></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'WooCommerce Integration', 'wpcalibrate-mobile-dock' ); ?></strong></td>
							<td>
								<?php if ( $is_wc_active ) : ?>
									<span class="wpcal-badge wpcal-badge--success"><?php esc_html_e( 'Active & Connected', 'wpcalibrate-mobile-dock' ); ?></span>
								<?php else : ?>
									<span class="wpcal-badge wpcal-badge--muted"><?php esc_html_e( 'Not Detected (Optional)', 'wpcalibrate-mobile-dock' ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Mobile Breakpoint', 'wpcalibrate-mobile-dock' ); ?></strong></td>
							<td><code><?php echo esc_html( (string) ( $settings['mobile_breakpoint'] ?? 768 ) ); ?>px</code></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Tablet Support', 'wpcalibrate-mobile-dock' ); ?></strong></td>
							<td>
								<?php echo ! empty( $settings['show_on_tablet'] ) ? esc_html__( 'Enabled (up to ' . ( $settings['tablet_breakpoint'] ?? 1024 ) . 'px)', 'wpcalibrate-mobile-dock' ) : esc_html__( 'Mobile Only', 'wpcalibrate-mobile-dock' ); ?>
							</td>
						</tr>
					</tbody>
				</table>

				<div class="wpcal-support-box">
					<p class="wpcal-support-box__text">
						<strong><?php esc_html_e( 'Need assistance or custom tweaks?', 'wpcalibrate-mobile-dock' ); ?></strong><br>
						<?php esc_html_e( 'Our development team provides rapid support for all WPCalibrate products.', 'wpcalibrate-mobile-dock' ); ?>
					</p>
					<div class="wpcal-support-box__buttons">
						<a href="mailto:<?php echo esc_attr( SupportService::EMAIL ); ?>" class="button button-small">
							✉️ <?php esc_html_e( 'Email Support', 'wpcalibrate-mobile-dock' ); ?>
						</a>
						<a href="https://api.whatsapp.com/send?phone=447474795976" target="_blank" rel="noopener noreferrer" class="button button-small">
							💬 <?php esc_html_e( 'WhatsApp', 'wpcalibrate-mobile-dock' ); ?>
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=support' ) ); ?>" class="button button-small">
							📋 <?php esc_html_e( 'Full System Report', 'wpcalibrate-mobile-dock' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
