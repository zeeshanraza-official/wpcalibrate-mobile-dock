<?php
/**
 * Navigations List Tab Template.
 *
 * @var \WPCalibrate\MobileDock\Domain\NavigationConfig[] $docks List of navigation configurations.
 * @package WPCalibrate\MobileDock\Admin
 */

defined( 'ABSPATH' ) || exit;

$docks = isset( $docks ) && is_array( $docks ) ? $docks : [];
?>
<div class="wpcal-section-card">
	<div class="wpcal-nav-bar-header">
		<div class="wpcal-card-header" style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">
			<h2><?php esc_html_e( 'Mobile Navigation Configurations', 'wpcalibrate-mobile-dock' ); ?></h2>
			<p class="wpcal-desc">
				<?php esc_html_e( 'Manage your bottom navigation docks. If multiple docks match a page, the winning dock is determined by specificity rules and priority.', 'wpcalibrate-mobile-dock' ); ?>
			</p>
		</div>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=navigations&view=new' ) ); ?>" class="button button-primary wpcal-btn-primary" style="margin-left: auto;">
			<span class="dashicons dashicons-plus-alt2"></span>
			<?php esc_html_e( 'Create New Dock', 'wpcalibrate-mobile-dock' ); ?>
		</a>
	</div>

	<?php if ( empty( $docks ) ) : ?>
		<div class="wpcal-empty-state">
			<div class="wpcal-empty-state__icon">📱</div>
			<h3><?php esc_html_e( 'No mobile navigation docks created yet', 'wpcalibrate-mobile-dock' ); ?></h3>
			<p><?php esc_html_e( 'Create your first app-style bottom navigation dock to give mobile visitors a streamlined experience.', 'wpcalibrate-mobile-dock' ); ?></p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=navigations&view=new' ) ); ?>" class="button button-primary button-hero wpcal-btn-primary">
				<?php esc_html_e( 'Create Mobile Dock', 'wpcalibrate-mobile-dock' ); ?>
			</a>
		</div>
	<?php else : ?>
		<table class="wp-list-table widefat fixed striped wpcal-table" style="margin-top: 18px;">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-primary"><?php esc_html_e( 'Title', 'wpcalibrate-mobile-dock' ); ?></th>
					<th scope="col" style="width: 110px;"><?php esc_html_e( 'Status', 'wpcalibrate-mobile-dock' ); ?></th>
					<th scope="col" style="width: 90px; text-align: center;"><?php esc_html_e( 'Items', 'wpcalibrate-mobile-dock' ); ?></th>
					<th scope="col" style="width: 90px; text-align: center;"><?php esc_html_e( 'Priority', 'wpcalibrate-mobile-dock' ); ?></th>
					<th scope="col" style="width: 110px; text-align: center;"><?php esc_html_e( 'Default', 'wpcalibrate-mobile-dock' ); ?></th>
					<th scope="col" style="width: 220px; text-align: right;"><?php esc_html_e( 'Actions', 'wpcalibrate-mobile-dock' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $docks as $dock ) : ?>
					<?php
					$edit_url = admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=navigations&view=edit&dock_id=' . $dock->id );
					$dup_url  = wp_nonce_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&action=duplicate&dock_id=' . $dock->id ), 'wpcal_duplicate_dock_' . $dock->id );
					$del_url  = wp_nonce_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&action=delete&dock_id=' . $dock->id ), 'wpcal_delete_dock_' . $dock->id );
					$def_url  = wp_nonce_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&action=set_default&dock_id=' . $dock->id ), 'wpcal_set_default_' . $dock->id );
					?>
					<tr>
						<td class="column-primary">
							<strong><a href="<?php echo esc_url( $edit_url ); ?>" class="wpcal-table-title-link"><?php echo esc_html( $dock->title ); ?></a></strong>
						</td>
						<td>
							<?php if ( 'publish' === $dock->status ) : ?>
								<span class="wpcal-badge wpcal-badge--active"><span class="wpcal-pulse-dot"></span><?php esc_html_e( 'Active', 'wpcalibrate-mobile-dock' ); ?></span>
							<?php else : ?>
								<span class="wpcal-badge wpcal-badge--draft"><?php esc_html_e( 'Draft', 'wpcalibrate-mobile-dock' ); ?></span>
							<?php endif; ?>
						</td>
						<td style="text-align: center;"><span class="wpcal-count-pill"><?php echo count( $dock->items ); ?></span></td>
						<td style="text-align: center;"><code><?php echo (int) $dock->priority; ?></code></td>
						<td style="text-align: center;">
							<?php if ( $dock->is_default ) : ?>
								<span class="wpcal-badge wpcal-badge--default" title="<?php esc_attr_e( 'Default fallback dock', 'wpcalibrate-mobile-dock' ); ?>">
									★ <?php esc_html_e( 'Default', 'wpcalibrate-mobile-dock' ); ?>
								</span>
							<?php else : ?>
								<a href="<?php echo esc_url( $def_url ); ?>" class="button button-small" title="<?php esc_attr_e( 'Set as default fallback dock', 'wpcalibrate-mobile-dock' ); ?>">
									<?php esc_html_e( 'Set Default', 'wpcalibrate-mobile-dock' ); ?>
								</a>
							<?php endif; ?>
						</td>
						<td style="text-align: right;">
							<a href="<?php echo esc_url( $edit_url ); ?>" class="button button-small button-primary" style="background: #0284c7; border-color: #0284c7;">
								<?php esc_html_e( 'Edit', 'wpcalibrate-mobile-dock' ); ?>
							</a>
							<a href="<?php echo esc_url( $dup_url ); ?>" class="button button-small">
								<?php esc_html_e( 'Duplicate', 'wpcalibrate-mobile-dock' ); ?>
							</a>
							<a href="<?php echo esc_url( $del_url ); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete this navigation dock?', 'wpcalibrate-mobile-dock' ) ); ?>');">
								<?php esc_html_e( 'Delete', 'wpcalibrate-mobile-dock' ); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
