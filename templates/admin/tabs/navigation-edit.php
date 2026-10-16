<?php
/**
 * Navigation Edit Tab Template.
 *
 * Visual Navigation Item Builder and Dock Settings.
 *
 * @var \WPCalibrate\MobileDock\Domain\NavigationConfig $config Navigation configuration object.
 * @package WPCalibrate\MobileDock\Admin
 */

use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Domain\VisibilityRule;
use WPCalibrate\MobileDock\Icons\IconRegistry;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

$config       = isset( $config ) && $config instanceof NavigationConfig ? $config : new NavigationConfig();
$action_types = NavigationItem::get_supported_action_types();
$is_wc_active = WooCommerceIntegration::is_active();
$icons        = IconRegistry::get_icons();
$all_pages    = get_pages( [ 'number' => 150 ] );
$wc_cats      = $is_wc_active ? WooCommerceIntegration::get_categories_list() : [];
?>
<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock' ) ); ?>" id="wpcal-dock-form">
	<?php wp_nonce_field( 'wpcal_save_dock_nonce', '_wpcal_nonce' ); ?>
	<input type="hidden" name="wpcal_action" value="save_dock" />
	<input type="hidden" name="dock_id" value="<?php echo esc_attr( $config->id ); ?>" />

	<div class="wpcal-editor-layout">
		<!-- Main Builder Column -->
		<div class="wpcal-editor-main">
			<!-- Items Section -->
			<div class="wpcal-section-card">
				<div class="wpcal-card-header wpcal-flex-between">
					<div>
						<h2><?php esc_html_e( 'Navigation Items', 'wpcalibrate-mobile-dock' ); ?></h2>
						<p class="wpcal-desc"><?php esc_html_e( 'Add, configure, and reorder items in this bottom navigation. Recommended: 2 to 7 items.', 'wpcalibrate-mobile-dock' ); ?></p>
					</div>
					<button type="button" class="button button-primary" id="wpcal-add-item-btn">
						+ <?php esc_html_e( 'Add Item', 'wpcalibrate-mobile-dock' ); ?>
					</button>
				</div>

				<div id="wpcal-items-warning" class="notice notice-warning inline" style="<?php echo ( count( $config->items ) > 7 || ( count( $config->items ) > 0 && count( $config->items ) < 2 ) ) ? '' : 'display:none;'; ?>">
					<p id="wpcal-warning-text">
						<?php
						if ( count( $config->items ) > 7 ) {
							esc_html_e( 'Warning: Having more than 7 items may crowd the mobile screen on narrow devices.', 'wpcalibrate-mobile-dock' );
						} elseif ( count( $config->items ) > 0 && count( $config->items ) < 2 ) {
							esc_html_e( 'Notice: Navigation docks typically feel best with at least 2 items.', 'wpcalibrate-mobile-dock' );
						}
						?>
					</p>
				</div>

				<div id="wpcal-items-container" class="wpcal-items-list">
					<?php if ( empty( $config->items ) ) : ?>
						<p class="wpcal-no-items-notice"><?php esc_html_e( 'No items yet. Click "Add Item" to get started.', 'wpcalibrate-mobile-dock' ); ?></p>
					<?php else : ?>
						<?php foreach ( $config->items as $idx => $item ) : ?>
							<?php include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/item-row.php'; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>

			<!-- Visibility Rules Section -->
			<div class="wpcal-section-card">
				<div class="wpcal-card-header wpcal-flex-between">
					<div>
						<h2><?php esc_html_e( 'Visibility Rules', 'wpcalibrate-mobile-dock' ); ?></h2>
						<p class="wpcal-desc"><?php esc_html_e( 'Determine where this dock appears. Exclusions always override inclusions.', 'wpcalibrate-mobile-dock' ); ?></p>
					</div>
					<button type="button" class="button button-secondary" id="wpcal-add-rule-btn">
						+ <?php esc_html_e( 'Add Rule', 'wpcalibrate-mobile-dock' ); ?>
					</button>
				</div>

				<div id="wpcal-rules-container" class="wpcal-rules-list">
					<?php if ( empty( $config->visibility_rules ) ) : ?>
						<p class="wpcal-no-rules-notice"><?php esc_html_e( 'No visibility rules specified. This dock applies to the entire site by default.', 'wpcalibrate-mobile-dock' ); ?></p>
					<?php else : ?>
						<?php foreach ( $config->visibility_rules as $r_idx => $rule ) : ?>
							<?php include WPCALIBRATE_MOBILE_DOCK_PATH . 'templates/admin/tabs/rule-row.php'; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>

			<!-- Per-Dock Appearance Overrides Section -->
			<div class="wpcal-section-card">
				<div class="wpcal-card-header">
					<h2><?php esc_html_e( 'Appearance Overrides', 'wpcalibrate-mobile-dock' ); ?></h2>
					<p class="wpcal-desc"><?php esc_html_e( 'Optionally override global appearance tokens specifically for this navigation dock.', 'wpcalibrate-mobile-dock' ); ?></p>
				</div>

				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Override Global Appearance', 'wpcalibrate-mobile-dock' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="appearance[override_global]" value="1" id="wpcal-override-appearance-toggle" <?php checked( $config->appearance->override_global ); ?> />
								<?php esc_html_e( 'Enable custom design settings for this dock', 'wpcalibrate-mobile-dock' ); ?>
							</label>
						</td>
					</tr>
				</table>

				<div id="wpcal-appearance-fields" style="<?php echo $config->appearance->override_global ? '' : 'display:none;'; ?>">
					<table class="form-table">
						<tr>
							<th scope="row"><?php esc_html_e( 'Design Preset', 'wpcalibrate-mobile-dock' ); ?></th>
							<td>
								<select name="appearance[preset]" id="wpcal-dock-preset-select">
									<option value="light" <?php selected( $config->appearance->preset, 'light' ); ?>><?php esc_html_e( 'Light Preset', 'wpcalibrate-mobile-dock' ); ?></option>
									<option value="dark" <?php selected( $config->appearance->preset, 'dark' ); ?>><?php esc_html_e( 'Dark Preset', 'wpcalibrate-mobile-dock' ); ?></option>
									<option value="custom" <?php selected( $config->appearance->preset, 'custom' ); ?>><?php esc_html_e( 'Custom Colors', 'wpcalibrate-mobile-dock' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Background Color', 'wpcalibrate-mobile-dock' ); ?></th>
							<td>
								<input type="text" name="appearance[bg_color]" value="<?php echo esc_attr( $config->appearance->bg_color ); ?>" class="wpcal-color-picker" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Icon Color', 'wpcalibrate-mobile-dock' ); ?></th>
							<td>
								<input type="text" name="appearance[icon_color]" value="<?php echo esc_attr( $config->appearance->icon_color ); ?>" class="wpcal-color-picker" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Active Icon Color', 'wpcalibrate-mobile-dock' ); ?></th>
							<td>
								<input type="text" name="appearance[active_icon_color]" value="<?php echo esc_attr( $config->appearance->active_icon_color ); ?>" class="wpcal-color-picker" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Text Color', 'wpcalibrate-mobile-dock' ); ?></th>
							<td>
								<input type="text" name="appearance[text_color]" value="<?php echo esc_attr( $config->appearance->text_color ); ?>" class="wpcal-color-picker" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Active Text Color', 'wpcalibrate-mobile-dock' ); ?></th>
							<td>
								<input type="text" name="appearance[active_text_color]" value="<?php echo esc_attr( $config->appearance->active_text_color ); ?>" class="wpcal-color-picker" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Navigation Height', 'wpcalibrate-mobile-dock' ); ?></th>
							<td>
								<input type="text" name="appearance[height]" value="<?php echo esc_attr( $config->appearance->height ); ?>" class="regular-text" style="max-width: 120px;" />
								<p class="description"><?php esc_html_e( 'e.g., 60px', 'wpcalibrate-mobile-dock' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Layout Mode', 'wpcalibrate-mobile-dock' ); ?></th>
							<td>
								<select name="appearance[layout_mode]">
									<option value="fixed" <?php selected( $config->appearance->layout_mode, 'fixed' ); ?>><?php esc_html_e( 'Fixed Bottom (Edge to Edge)', 'wpcalibrate-mobile-dock' ); ?></option>
									<option value="floating" <?php selected( $config->appearance->layout_mode, 'floating' ); ?>><?php esc_html_e( 'Floating Pill', 'wpcalibrate-mobile-dock' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Show Item Labels', 'wpcalibrate-mobile-dock' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="appearance[show_labels]" value="1" <?php checked( $config->appearance->show_labels ); ?> />
									<?php esc_html_e( 'Display text labels beneath icons', 'wpcalibrate-mobile-dock' ); ?>
								</label>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>

		<!-- Sidebar Settings Column -->
		<div class="wpcal-editor-sidebar">
			<div class="wpcal-sidebar-card">
				<h3><?php esc_html_e( 'Publish Settings', 'wpcalibrate-mobile-dock' ); ?></h3>

				<div class="wpcal-field-group">
					<label for="dock_title"><strong><?php esc_html_e( 'Dock Title', 'wpcalibrate-mobile-dock' ); ?></strong></label>
					<input type="text" id="dock_title" name="dock_title" value="<?php echo esc_attr( $config->title ); ?>" class="widefat" required />
				</div>

				<div class="wpcal-field-group">
					<label for="dock_status"><strong><?php esc_html_e( 'Status', 'wpcalibrate-mobile-dock' ); ?></strong></label>
					<select id="dock_status" name="dock_status" class="widefat">
						<option value="publish" <?php selected( $config->status, 'publish' ); ?>><?php esc_html_e( 'Active', 'wpcalibrate-mobile-dock' ); ?></option>
						<option value="draft" <?php selected( $config->status, 'draft' ); ?>><?php esc_html_e( 'Draft (Disabled)', 'wpcalibrate-mobile-dock' ); ?></option>
					</select>
				</div>

				<div class="wpcal-field-group">
					<label>
						<input type="checkbox" name="dock_is_default" value="1" <?php checked( $config->is_default ); ?> />
						<strong><?php esc_html_e( 'Set as Default Dock', 'wpcalibrate-mobile-dock' ); ?></strong>
					</label>
					<p class="description"><?php esc_html_e( 'Default dock will display when no higher-specificity dock matches.', 'wpcalibrate-mobile-dock' ); ?></p>
				</div>

				<div class="wpcal-field-group">
					<label for="dock_priority"><strong><?php esc_html_e( 'Priority Order', 'wpcalibrate-mobile-dock' ); ?></strong></label>
					<input type="number" id="dock_priority" name="dock_priority" value="<?php echo esc_attr( $config->priority ); ?>" min="1" max="999" class="small-text" />
					<p class="description"><?php esc_html_e( 'Lower numbers take higher precedence in tie-breakers (e.g. 1 beats 10).', 'wpcalibrate-mobile-dock' ); ?></p>
				</div>

				<hr />

				<h3><?php esc_html_e( 'Breakpoint Controls', 'wpcalibrate-mobile-dock' ); ?></h3>

				<div class="wpcal-field-group">
					<label for="dock_breakpoint"><strong><?php esc_html_e( 'Mobile Breakpoint (px)', 'wpcalibrate-mobile-dock' ); ?></strong></label>
					<input type="number" id="dock_breakpoint" name="dock_breakpoint" value="<?php echo esc_attr( $config->breakpoint ); ?>" min="320" max="1920" class="small-text" />
					<p class="description"><?php esc_html_e( 'Display dock only on viewports at or below this width (default: 768px).', 'wpcalibrate-mobile-dock' ); ?></p>
				</div>

				<div class="wpcal-field-group">
					<label>
						<input type="checkbox" name="dock_show_on_tablet" value="1" id="wpcal-tablet-toggle" <?php checked( $config->show_on_tablet ); ?> />
						<strong><?php esc_html_e( 'Enable on Tablets', 'wpcalibrate-mobile-dock' ); ?></strong>
					</label>
				</div>

				<div class="wpcal-field-group" id="wpcal-tablet-bp-wrap" style="<?php echo $config->show_on_tablet ? '' : 'display:none;'; ?>">
					<label for="dock_tablet_breakpoint"><strong><?php esc_html_e( 'Tablet Max Breakpoint (px)', 'wpcalibrate-mobile-dock' ); ?></strong></label>
					<input type="number" id="dock_tablet_breakpoint" name="dock_tablet_breakpoint" value="<?php echo esc_attr( $config->tablet_breakpoint ); ?>" min="480" max="2560" class="small-text" />
				</div>

				<hr />

				<div class="wpcal-card-actions">
					<button type="submit" class="button button-primary button-large wpcal-btn-block">
						<?php esc_html_e( 'Save Navigation Dock', 'wpcalibrate-mobile-dock' ); ?>
					</button>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcalibrate-mobile-dock&tab=navigations' ) ); ?>" class="button button-secondary wpcal-btn-block wpcal-mt-2">
						<?php esc_html_e( 'Cancel', 'wpcalibrate-mobile-dock' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Modal for Icon Picker -->
<div id="wpcal-icon-picker-modal" class="wpcal-modal" hidden>
	<div class="wpcal-modal__backdrop" id="wpcal-close-icon-modal"></div>
	<div class="wpcal-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="wpcal-icon-modal-title">
		<div class="wpcal-modal__header">
			<h3 id="wpcal-icon-modal-title"><?php esc_html_e( 'Select an Icon', 'wpcalibrate-mobile-dock' ); ?></h3>
			<button type="button" class="wpcal-modal__close" id="wpcal-close-icon-btn" aria-label="<?php esc_attr_e( 'Close', 'wpcalibrate-mobile-dock' ); ?>">&times;</button>
		</div>
		<div class="wpcal-modal__search">
			<input type="text" id="wpcal-icon-search-input" placeholder="<?php esc_attr_e( 'Search bundled icons...', 'wpcalibrate-mobile-dock' ); ?>" class="widefat" />
		</div>
		<div class="wpcal-modal__body">
			<div class="wpcal-icon-grid" id="wpcal-icon-grid">
				<?php foreach ( $icons as $icon_id => $icon_meta ) : ?>
					<button type="button" class="wpcal-icon-option" data-icon-id="<?php echo esc_attr( $icon_id ); ?>" data-icon-label="<?php echo esc_attr( $icon_meta['label'] ); ?>" title="<?php echo esc_attr( $icon_meta['label'] ); ?>">
						<span class="wpcal-icon-preview">
							<?php echo IconRegistry::get_svg( $icon_id, [ 'width' => '24', 'height' => '24' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<span class="wpcal-icon-name"><?php echo esc_html( $icon_meta['label'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
