<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Deletes plugin data ONLY when explicitly configured in Advanced settings.
 * Leaves all data intact by default.
 *
 * @package WPCalibrate\MobileDock
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Retrieve settings to check data retention policy.
$wpcal_settings = get_option( 'wpcalibrate_mobile_dock_settings', [] );

// Default retention policy is to preserve data unless explicitly opted into deletion.
$delete_data = ! empty( $wpcal_settings['delete_data_on_uninstall'] );

if ( ! $delete_data ) {
	return;
}

global $wpdb;

// 1. Delete all private navigation configuration posts and associated metadata.
$dock_posts = get_posts(
	[
		'post_type'      => 'wpcal_mobile_dock',
		'post_status'    => 'any',
		'numberposts'    => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'suppress_filters' => true,
	]
);

if ( ! empty( $dock_posts ) ) {
	foreach ( $dock_posts as $post_id ) {
		wp_delete_post( $post_id, true ); // Bypass trash, force delete post + postmeta.
	}
}

// 2. Delete plugin options.
delete_option( 'wpcalibrate_mobile_dock_settings' );
delete_option( 'wpcalibrate_mobile_dock_version' );
delete_option( 'wpcalibrate_mobile_dock_db_version' );

// 3. Delete any transients or cached entries created by this plugin.
if ( ! empty( $wpdb ) && isset( $wpdb->options ) && method_exists( $wpdb, 'query' ) ) {
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
			$wpdb->esc_like( '_transient_wpcal_dock_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_wpcal_dock_' ) . '%'
		)
	);
}
