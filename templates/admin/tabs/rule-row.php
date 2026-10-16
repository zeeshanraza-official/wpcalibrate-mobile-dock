<?php
/**
 * Visibility Rule Row Sub-Template.
 *
 * @package WPCalibrate\MobileDock\Admin
 *
 * @var \WPCalibrate\MobileDock\Domain\VisibilityRule|null $rule Visibility rule domain model instance.
 * @var int|string                                         $r_idx Row index or template placeholder.
 */

use WPCalibrate\MobileDock\Domain\VisibilityRule;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

$rule          = isset( $rule ) && $rule instanceof VisibilityRule ? $rule : null;
$r_idx         = isset( $r_idx ) ? $r_idx : '{{R_INDEX}}';
$rule_type     = $rule ? $rule->condition_type : VisibilityRule::TYPE_ENTIRE_SITE;
$rule_operator = $rule ? $rule->operator : VisibilityRule::OPERATOR_INCLUDE;
$rule_auth     = $rule ? $rule->user_auth : VisibilityRule::AUTH_ALL;
$rule_targets  = $rule ? implode( ', ', $rule->target_ids ) : '';
$is_wc_active  = WooCommerceIntegration::is_active();

$r_index = $r_idx;
?>
<div class="wpcal-rule-card" data-rule-index="<?php echo esc_attr( $r_index ); ?>">
	<div class="wpcal-rule-card__inner">
		<!-- Operator (Include / Exclude) -->
		<div class="wpcal-rule-field">
			<label><?php esc_html_e( 'Action', 'wpcalibrate-mobile-dock' ); ?>:</label>
			<select name="visibility_rules[<?php echo esc_attr( $r_index ); ?>][operator]" class="wpcal-rule-operator-select">
				<option value="include" <?php selected( $rule_operator, 'include' ); ?>><?php esc_html_e( 'Show On (Include)', 'wpcalibrate-mobile-dock' ); ?></option>
				<option value="exclude" <?php selected( $rule_operator, 'exclude' ); ?>><?php esc_html_e( 'Hide On (Exclude)', 'wpcalibrate-mobile-dock' ); ?></option>
			</select>
		</div>

		<!-- Condition Type -->
		<div class="wpcal-rule-field">
			<label><?php esc_html_e( 'Location', 'wpcalibrate-mobile-dock' ); ?>:</label>
			<select name="visibility_rules[<?php echo esc_attr( $r_index ); ?>][condition_type]" class="wpcal-rule-type-select">
				<option value="entire_site" <?php selected( $rule_type, 'entire_site' ); ?>><?php esc_html_e( 'Entire Site', 'wpcalibrate-mobile-dock' ); ?></option>
				<option value="front_page" <?php selected( $rule_type, 'front_page' ); ?>><?php esc_html_e( 'Front Page', 'wpcalibrate-mobile-dock' ); ?></option>
				<option value="blog_page" <?php selected( $rule_type, 'blog_page' ); ?>><?php esc_html_e( 'Blog / Posts Index', 'wpcalibrate-mobile-dock' ); ?></option>
				<option value="pages" <?php selected( $rule_type, 'pages' ); ?>><?php esc_html_e( 'Selected Pages', 'wpcalibrate-mobile-dock' ); ?></option>
				<option value="posts" <?php selected( $rule_type, 'posts' ); ?>><?php esc_html_e( 'Selected Posts', 'wpcalibrate-mobile-dock' ); ?></option>
				<option value="post_types" <?php selected( $rule_type, 'post_types' ); ?>><?php esc_html_e( 'Public Post Types', 'wpcalibrate-mobile-dock' ); ?></option>
				<option value="taxonomies" <?php selected( $rule_type, 'taxonomies' ); ?>><?php esc_html_e( 'Categories / Taxonomies', 'wpcalibrate-mobile-dock' ); ?></option>

				<?php if ( $is_wc_active ) : ?>
					<optgroup label="<?php esc_attr_e( 'WooCommerce', 'wpcalibrate-mobile-dock' ); ?>">
						<option value="wc_shop" <?php selected( $rule_type, 'wc_shop' ); ?>><?php esc_html_e( 'Shop Page', 'wpcalibrate-mobile-dock' ); ?></option>
						<option value="wc_products" <?php selected( $rule_type, 'wc_products' ); ?>><?php esc_html_e( 'Product Pages', 'wpcalibrate-mobile-dock' ); ?></option>
						<option value="wc_categories" <?php selected( $rule_type, 'wc_categories' ); ?>><?php esc_html_e( 'Product Categories', 'wpcalibrate-mobile-dock' ); ?></option>
						<option value="wc_cart" <?php selected( $rule_type, 'wc_cart' ); ?>><?php esc_html_e( 'Cart Page', 'wpcalibrate-mobile-dock' ); ?></option>
						<option value="wc_checkout" <?php selected( $rule_type, 'wc_checkout' ); ?>><?php esc_html_e( 'Checkout Page', 'wpcalibrate-mobile-dock' ); ?></option>
						<option value="wc_account" <?php selected( $rule_type, 'wc_account' ); ?>><?php esc_html_e( 'My Account Page', 'wpcalibrate-mobile-dock' ); ?></option>
					</optgroup>
				<?php endif; ?>
			</select>
		</div>

		<!-- Target IDs -->
		<div class="wpcal-rule-field wpcal-rule-targets-field">
			<label><?php esc_html_e( 'Target IDs (comma-separated)', 'wpcalibrate-mobile-dock' ); ?>:</label>
			<input type="text" name="visibility_rules[<?php echo esc_attr( $r_index ); ?>][target_ids_input]" value="<?php echo esc_attr( $rule_targets ); ?>" placeholder="<?php esc_attr_e( 'e.g., 12, 45, 88 (leave blank for all)', 'wpcalibrate-mobile-dock' ); ?>" class="regular-text" />
		</div>

		<!-- User Auth Requirement -->
		<div class="wpcal-rule-field">
			<label><?php esc_html_e( 'User Authentication', 'wpcalibrate-mobile-dock' ); ?>:</label>
			<select name="visibility_rules[<?php echo esc_attr( $r_index ); ?>][user_auth]">
				<option value="all" <?php selected( $rule_auth, 'all' ); ?>><?php esc_html_e( 'All Users', 'wpcalibrate-mobile-dock' ); ?></option>
				<option value="logged_in" <?php selected( $rule_auth, 'logged_in' ); ?>><?php esc_html_e( 'Logged-in Only', 'wpcalibrate-mobile-dock' ); ?></option>
				<option value="logged_out" <?php selected( $rule_auth, 'logged_out' ); ?>><?php esc_html_e( 'Logged-out Only', 'wpcalibrate-mobile-dock' ); ?></option>
			</select>
		</div>

		<!-- Remove Button -->
		<div class="wpcal-rule-field wpcal-rule-actions">
			<button type="button" class="button button-small button-link-delete wpcal-delete-rule-btn" title="<?php esc_attr_e( 'Delete Rule', 'wpcalibrate-mobile-dock' ); ?>">
				&times; <?php esc_html_e( 'Remove', 'wpcalibrate-mobile-dock' ); ?>
			</button>
		</div>
	</div>
</div>
