<?php
/**
 * Admin Screen Header Template.
 *
 * @package WPCalibrate\MobileDock\Admin
 *
 * @var string $current_tab Current active admin tab slug.
 */

defined( 'ABSPATH' ) || exit;

$current_tab    = isset( $current_tab ) ? (string) $current_tab : 'overview';
$brand_icon_url = WPCALIBRATE_MOBILE_DOCK_URL . 'assets/branding/wpcalibrate-icon.svg';
$msg            = sanitize_key( $_GET['message'] ?? '' );
?>
<div class="wrap wpcal-admin-wrap">
	<header class="wpcal-admin-header">
		<div class="wpcal-admin-header__brand">
			<div class="wpcal-brand-logo-container">
				<img src="<?php echo esc_url( $brand_icon_url ); ?>" alt="WPCalibrate" class="wpcal-brand-logo" width="36" height="36" />
			</div>
			<div class="wpcal-admin-header__title-wrap">
				<div class="wpcal-title-row">
					<h1 class="wpcal-admin-header__title"><?php esc_html_e( 'WPCalibrate Mobile Dock', 'wpcalibrate-mobile-dock' ); ?></h1>
					<span class="wpcal-version-badge">v<?php echo esc_html( WPCALIBRATE_MOBILE_DOCK_VERSION ); ?></span>
				</div>
				<p class="wpcal-admin-header__tagline"><?php esc_html_e( 'Fast, Accessible, App-Style Bottom Navigation for WordPress & WooCommerce', 'wpcalibrate-mobile-dock' ); ?></p>
			</div>
		</div>
		<div class="wpcal-admin-header__actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=navigations&view=new' ) ); ?>" class="button button-primary wpcal-btn-header-cta">
				<span class="dashicons dashicons-plus-alt2"></span>
				<?php esc_html_e( 'Add New Dock', 'wpcalibrate-mobile-dock' ); ?>
			</a>
		</div>
	</header>

	<?php if ( ! empty( $msg ) ) : ?>
		<div class="notice notice-success is-dismissible wpcal-notice">
			<p>
				<?php
				if ( 'saved' === $msg || 'dock_saved' === $msg ) {
					esc_html_e( 'Settings updated successfully.', 'wpcalibrate-mobile-dock' );
				} elseif ( 'reset' === $msg ) {
					esc_html_e( 'Settings have been reset to default values.', 'wpcalibrate-mobile-dock' );
				} elseif ( 'dock_deleted' === $msg ) {
					esc_html_e( 'Navigation dock deleted.', 'wpcalibrate-mobile-dock' );
				} elseif ( 'dock_duplicated' === $msg ) {
					esc_html_e( 'Navigation dock duplicated successfully.', 'wpcalibrate-mobile-dock' );
				} elseif ( 'dock_set_default' === $msg ) {
					esc_html_e( 'Default navigation dock updated.', 'wpcalibrate-mobile-dock' );
				}
				?>
			</p>
		</div>
	<?php endif; ?>

	<nav class="nav-tab-wrapper wpcal-nav-tabs" aria-label="<?php esc_attr_e( 'Mobile Dock Navigation Tabs', 'wpcalibrate-mobile-dock' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=overview' ) ); ?>" class="nav-tab <?php echo 'overview' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-info-outline"></span>
			<span class="wpcal-tab-title"><?php esc_html_e( 'Overview', 'wpcalibrate-mobile-dock' ); ?></span>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=navigations' ) ); ?>" class="nav-tab <?php echo 'navigations' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-menu"></span>
			<span class="wpcal-tab-title"><?php esc_html_e( 'Navigations', 'wpcalibrate-mobile-dock' ); ?></span>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=appearance' ) ); ?>" class="nav-tab <?php echo 'appearance' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-art"></span>
			<span class="wpcal-tab-title"><?php esc_html_e( 'Appearance', 'wpcalibrate-mobile-dock' ); ?></span>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=visibility' ) ); ?>" class="nav-tab <?php echo 'visibility' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-visibility"></span>
			<span class="wpcal-tab-title"><?php esc_html_e( 'Visibility', 'wpcalibrate-mobile-dock' ); ?></span>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=woocommerce' ) ); ?>" class="nav-tab <?php echo 'woocommerce' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-cart"></span>
			<span class="wpcal-tab-title"><?php esc_html_e( 'WooCommerce', 'wpcalibrate-mobile-dock' ); ?></span>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=advanced' ) ); ?>" class="nav-tab <?php echo 'advanced' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-admin-generic"></span>
			<span class="wpcal-tab-title"><?php esc_html_e( 'Advanced', 'wpcalibrate-mobile-dock' ); ?></span>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=license' ) ); ?>" class="nav-tab <?php echo 'license' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-admin-network"></span>
			<span class="wpcal-tab-title"><?php esc_html_e( 'License', 'wpcalibrate-mobile-dock' ); ?></span>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=support' ) ); ?>" class="nav-tab <?php echo 'support' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-sos"></span>
			<span class="wpcal-tab-title"><?php esc_html_e( 'Support', 'wpcalibrate-mobile-dock' ); ?></span>
		</a>
	</nav>

	<div class="wpcal-tab-content">
