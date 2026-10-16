<?php
/**
 * Navigation Item Row Sub-Template.
 *
 * @var \WPCalibrate\MobileDock\Domain\NavigationItem|null $item Navigation item.
 * @var int|string $idx Row index.
 * @var array $action_types Action types.
 * @var bool $is_wc_active WooCommerce active flag.
 * @var array $all_pages Pages list.
 * @var array $wc_cats Product categories list.
 * @package WPCalibrate\MobileDock\Admin
 */

use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Icons\IconRegistry;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

// Fallback values if rendered as new item template or in isolation
$item_id             = isset( $item ) && $item instanceof NavigationItem ? $item->id : 'item_' . uniqid();
$item_enabled        = isset( $item ) && $item instanceof NavigationItem ? $item->enabled : true;
$item_label          = isset( $item ) && $item instanceof NavigationItem ? $item->label : '';
$item_icon           = isset( $item ) && $item instanceof NavigationItem ? $item->icon : 'home';
$item_action         = isset( $item ) && $item instanceof NavigationItem ? $item->action_type : NavigationItem::ACTION_HOME;
$item_target         = isset( $item ) && $item instanceof NavigationItem ? $item->target : '';
$item_new_tab        = isset( $item ) && $item instanceof NavigationItem ? $item->open_new_tab : false;
$item_rel            = isset( $item ) && $item instanceof NavigationItem ? $item->rel : '';
$item_css            = isset( $item ) && $item instanceof NavigationItem ? $item->css_class : '';
$item_aria           = isset( $item ) && $item instanceof NavigationItem ? $item->accessibility_label : '';
$item_center         = isset( $item ) && $item instanceof NavigationItem ? $item->is_center_raised : false;
$item_visibility     = isset( $item ) && $item instanceof NavigationItem ? $item->user_visibility : 'all';
$item_extra          = isset( $item ) && $item instanceof NavigationItem ? $item->extra : [];

$row_index    = isset( $idx ) ? $idx : '{{INDEX}}';
$action_types = isset( $action_types ) && is_array( $action_types ) ? $action_types : NavigationItem::get_supported_action_types();
$is_wc_active = isset( $is_wc_active ) ? (bool) $is_wc_active : WooCommerceIntegration::is_active();
$all_pages    = isset( $all_pages ) && is_array( $all_pages ) ? $all_pages : get_pages( [ 'number' => 150 ] );
$wc_cats      = isset( $wc_cats ) && is_array( $wc_cats ) ? $wc_cats : ( $is_wc_active ? WooCommerceIntegration::get_categories_list() : [] );
?>
<div class="wpcal-item-card" data-item-index="<?php echo esc_attr( $row_index ); ?>">
	<input type="hidden" name="items[<?php echo esc_attr( $row_index ); ?>][id]" value="<?php echo esc_attr( $item_id ); ?>" />

	<div class="wpcal-item-card__header">
		<div class="wpcal-item-card__drag" title="<?php esc_attr_e( 'Drag to reorder', 'wpcalibrate-mobile-dock' ); ?>">
			<span class="dashicons dashicons-menu"></span>
		</div>
		<div class="wpcal-item-card__enable">
			<label title="<?php esc_attr_e( 'Enable or disable this item', 'wpcalibrate-mobile-dock' ); ?>">
				<input type="checkbox" name="items[<?php echo esc_attr( $row_index ); ?>][enabled]" value="1" <?php checked( $item_enabled ); ?> />
			</label>
		</div>
		<div class="wpcal-item-card__icon-picker">
			<button type="button" class="button wpcal-icon-select-btn" data-target-input="wpcal-icon-input-<?php echo esc_attr( $row_index ); ?>" title="<?php esc_attr_e( 'Click to change icon', 'wpcalibrate-mobile-dock' ); ?>">
				<span class="wpcal-icon-btn-preview" id="wpcal-icon-preview-<?php echo esc_attr( $row_index ); ?>">
					<?php echo IconRegistry::get_svg( $item_icon, [ 'width' => '18', 'height' => '18' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</button>
			<input type="hidden" name="items[<?php echo esc_attr( $row_index ); ?>][icon]" id="wpcal-icon-input-<?php echo esc_attr( $row_index ); ?>" value="<?php echo esc_attr( $item_icon ); ?>" />
		</div>
		<div class="wpcal-item-card__title">
			<input type="text" name="items[<?php echo esc_attr( $row_index ); ?>][label]" value="<?php echo esc_attr( $item_label ); ?>" placeholder="<?php esc_attr_e( 'Item label (e.g. Home)', 'wpcalibrate-mobile-dock' ); ?>" class="regular-text wpcal-item-label-input" required />
		</div>
		<div class="wpcal-item-card__action-type">
			<select name="items[<?php echo esc_attr( $row_index ); ?>][action_type]" class="wpcal-action-select">
				<?php foreach ( $action_types as $type_key => $type_label ) : ?>
					<?php if ( str_starts_with( $type_key, 'wc_' ) && ! $is_wc_active ) continue; ?>
					<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $item_action, $type_key ); ?>>
						<?php echo esc_html( $type_label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="wpcal-item-card__controls">
			<button type="button" class="button button-small wpcal-move-up-btn" title="<?php esc_attr_e( 'Move Up', 'wpcalibrate-mobile-dock' ); ?>">&uarr;</button>
			<button type="button" class="button button-small wpcal-move-down-btn" title="<?php esc_attr_e( 'Move Down', 'wpcalibrate-mobile-dock' ); ?>">&darr;</button>
			<button type="button" class="button button-small wpcal-toggle-details-btn" title="<?php esc_attr_e( 'Configure Details', 'wpcalibrate-mobile-dock' ); ?>">&#9662;</button>
			<button type="button" class="button button-small button-link-delete wpcal-delete-item-btn" title="<?php esc_attr_e( 'Remove Item', 'wpcalibrate-mobile-dock' ); ?>">&times;</button>
		</div>
	</div>

	<!-- Item Details Accordion Body -->
	<div class="wpcal-item-card__body">
		<!-- Dynamic Target Fields -->
		<div class="wpcal-target-field wpcal-target-custom_url" style="<?php echo NavigationItem::ACTION_CUSTOM_URL === $item_action ? '' : 'display:none;'; ?>">
			<label><strong><?php esc_html_e( 'Target URL', 'wpcalibrate-mobile-dock' ); ?>:</strong></label>
			<input type="url" name="items[<?php echo esc_attr( $row_index ); ?>][target_url]" value="<?php echo NavigationItem::ACTION_CUSTOM_URL === $item_action ? esc_url( $item_target ) : ''; ?>" placeholder="https://example.com/mypage" class="widefat" />
		</div>

		<div class="wpcal-target-field wpcal-target-page" style="<?php echo NavigationItem::ACTION_PAGE === $item_action ? '' : 'display:none;'; ?>">
			<label><strong><?php esc_html_e( 'Select Page', 'wpcalibrate-mobile-dock' ); ?>:</strong></label>
			<select name="items[<?php echo esc_attr( $row_index ); ?>][target_page]" class="widefat">
				<option value=""><?php esc_html_e( '— Select a Page —', 'wpcalibrate-mobile-dock' ); ?></option>
				<?php foreach ( $all_pages as $p ) : ?>
					<option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( (int) $item_target, $p->ID ); ?>>
						<?php echo esc_html( $p->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="wpcal-target-field wpcal-target-post" style="<?php echo NavigationItem::ACTION_POST === $item_action ? '' : 'display:none;'; ?>">
			<label><strong><?php esc_html_e( 'Post ID', 'wpcalibrate-mobile-dock' ); ?>:</strong></label>
			<input type="number" name="items[<?php echo esc_attr( $row_index ); ?>][target_post]" value="<?php echo NavigationItem::ACTION_POST === $item_action ? esc_attr( $item_target ) : ''; ?>" placeholder="123" class="small-text" />
		</div>

		<div class="wpcal-target-field wpcal-target-phone" style="<?php echo NavigationItem::ACTION_PHONE === $item_action ? '' : 'display:none;'; ?>">
			<label><strong><?php esc_html_e( 'Phone Number (with country code)', 'wpcalibrate-mobile-dock' ); ?>:</strong></label>
			<input type="tel" name="items[<?php echo esc_attr( $row_index ); ?>][target_phone]" value="<?php echo NavigationItem::ACTION_PHONE === $item_action ? esc_attr( $item_target ) : ''; ?>" placeholder="+447474795976" class="regular-text" />
		</div>

		<div class="wpcal-target-field wpcal-target-whatsapp" style="<?php echo NavigationItem::ACTION_WHATSAPP === $item_action ? '' : 'display:none;'; ?>">
			<label><strong><?php esc_html_e( 'WhatsApp Phone Number', 'wpcalibrate-mobile-dock' ); ?>:</strong></label>
			<input type="text" name="items[<?php echo esc_attr( $row_index ); ?>][target_whatsapp]" value="<?php echo NavigationItem::ACTION_WHATSAPP === $item_action ? esc_attr( $item_target ) : ''; ?>" placeholder="447474795976" class="regular-text" />
			<div class="wpcal-mt-1">
				<label><?php esc_html_e( 'Optional Prefilled Message', 'wpcalibrate-mobile-dock' ); ?>:</label>
				<input type="text" name="items[<?php echo esc_attr( $row_index ); ?>][extra][whatsapp_message]" value="<?php echo esc_attr( $item_extra['whatsapp_message'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Hello, I have a question about...', 'wpcalibrate-mobile-dock' ); ?>" class="widefat" />
			</div>
		</div>

		<div class="wpcal-target-field wpcal-target-email" style="<?php echo NavigationItem::ACTION_EMAIL === $item_action ? '' : 'display:none;'; ?>">
			<label><strong><?php esc_html_e( 'Recipient Email Address', 'wpcalibrate-mobile-dock' ); ?>:</strong></label>
			<input type="email" name="items[<?php echo esc_attr( $row_index ); ?>][target_email]" value="<?php echo NavigationItem::ACTION_EMAIL === $item_action ? esc_attr( $item_target ) : ''; ?>" placeholder="support@example.com" class="regular-text" />
			<div class="wpcal-mt-1">
				<label><?php esc_html_e( 'Email Subject', 'wpcalibrate-mobile-dock' ); ?>:</label>
				<input type="text" name="items[<?php echo esc_attr( $row_index ); ?>][extra][email_subject]" value="<?php echo esc_attr( $item_extra['email_subject'] ?? '' ); ?>" class="widefat" />
			</div>
		</div>

		<?php if ( $is_wc_active ) : ?>
			<div class="wpcal-target-field wpcal-target-wc_category" style="<?php echo NavigationItem::ACTION_WC_CATEGORY === $item_action ? '' : 'display:none;'; ?>">
				<label><strong><?php esc_html_e( 'Product Category', 'wpcalibrate-mobile-dock' ); ?>:</strong></label>
				<select name="items[<?php echo esc_attr( $row_index ); ?>][target_wc_category]" class="widefat">
					<option value=""><?php esc_html_e( '— Select Product Category —', 'wpcalibrate-mobile-dock' ); ?></option>
					<?php foreach ( $wc_cats as $cat_id => $cat_name ) : ?>
						<option value="<?php echo esc_attr( $cat_id ); ?>" <?php selected( (int) $item_target, $cat_id ); ?>>
							<?php echo esc_html( $cat_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>

		<!-- Target value carrier -->
		<input type="hidden" name="items[<?php echo esc_attr( $row_index ); ?>][target]" class="wpcal-target-carrier" value="<?php echo esc_attr( $item_target ); ?>" />

		<!-- Additional Item Settings -->
		<div class="wpcal-grid-3 wpcal-mt-2">
			<div>
				<label>
					<input type="checkbox" name="items[<?php echo esc_attr( $row_index ); ?>][is_center_raised]" value="1" class="wpcal-center-raised-checkbox" <?php checked( $item_center ); ?> />
					<strong><?php esc_html_e( 'Center Featured / Raised Button', 'wpcalibrate-mobile-dock' ); ?></strong>
				</label>
				<p class="description"><?php esc_html_e( 'Only 1 item per dock can be raised.', 'wpcalibrate-mobile-dock' ); ?></p>
			</div>
			<div>
				<label>
					<input type="checkbox" name="items[<?php echo esc_attr( $row_index ); ?>][open_new_tab]" value="1" <?php checked( $item_new_tab ); ?> />
					<?php esc_html_e( 'Open in new tab', 'wpcalibrate-mobile-dock' ); ?>
				</label>
			</div>
			<div>
				<label><?php esc_html_e( 'Visibility', 'wpcalibrate-mobile-dock' ); ?>:</label>
				<select name="items[<?php echo esc_attr( $row_index ); ?>][user_visibility]">
					<option value="all" <?php selected( $item_visibility, 'all' ); ?>><?php esc_html_e( 'All Visitors', 'wpcalibrate-mobile-dock' ); ?></option>
					<option value="logged_in" <?php selected( $item_visibility, 'logged_in' ); ?>><?php esc_html_e( 'Logged-in Users Only', 'wpcalibrate-mobile-dock' ); ?></option>
					<option value="logged_out" <?php selected( $item_visibility, 'logged_out' ); ?>><?php esc_html_e( 'Logged-out Users Only', 'wpcalibrate-mobile-dock' ); ?></option>
				</select>
			</div>
		</div>

		<div class="wpcal-grid-3 wpcal-mt-2">
			<div>
				<label><?php esc_html_e( 'Accessibility Label Override', 'wpcalibrate-mobile-dock' ); ?>:</label>
				<input type="text" name="items[<?php echo esc_attr( $row_index ); ?>][accessibility_label]" value="<?php echo esc_attr( $item_aria ); ?>" placeholder="<?php esc_attr_e( 'e.g., Navigate to Homepage', 'wpcalibrate-mobile-dock' ); ?>" class="widefat" />
			</div>
			<div>
				<label><?php esc_html_e( 'CSS Class', 'wpcalibrate-mobile-dock' ); ?>:</label>
				<input type="text" name="items[<?php echo esc_attr( $row_index ); ?>][css_class]" value="<?php echo esc_attr( $item_css ); ?>" placeholder="my-custom-item" class="widefat" />
			</div>
			<div>
				<label><?php esc_html_e( 'Link rel Attribute', 'wpcalibrate-mobile-dock' ); ?>:</label>
				<input type="text" name="items[<?php echo esc_attr( $row_index ); ?>][rel]" value="<?php echo esc_attr( $item_rel ); ?>" placeholder="nofollow" class="widefat" />
			</div>
		</div>
	</div>
</div>
