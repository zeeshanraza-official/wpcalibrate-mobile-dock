<?php
/**
 * Navigation Configuration Repository.
 *
 * Implements CRUD for private post type wpcal_mobile_dock.
 *
 * @package WPCalibrate\MobileDock\Navigation
 */

namespace WPCalibrate\MobileDock\Navigation;

use WPCalibrate\MobileDock\Domain\NavigationConfig;

defined( 'ABSPATH' ) || exit;

class NavigationRepository {

	public const POST_TYPE = 'wpcal_mobile_dock';
	public const META_KEY  = '_wpcal_mobile_dock_config';
	public const CACHE_KEY = 'wpcal_dock_all_configs';

	public static function register_post_type(): void {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'              => [
					'name'          => __( 'Mobile Docks', 'wpcalibrate-mobile-dock' ),
					'singular_name' => __( 'Mobile Dock', 'wpcalibrate-mobile-dock' ),
				],
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => false,
				'show_in_menu'        => false,
				'query_var'           => false,
				'rewrite'             => false,
				'capability_type'     => 'post',
				'has_archive'         => false,
				'hierarchical'        => false,
				'supports'            => [ 'title' ],
				'can_export'          => false,
				'exclude_from_search' => true,
			]
		);
	}

	public function get( int $id ): ?NavigationConfig {
		if ( $id <= 0 ) {
			return null;
		}

		$post = get_post( $id );
		if ( ! $post || self::POST_TYPE !== $post->post_type ) {
			return null;
		}

		$meta = get_post_meta( $id, self::META_KEY, true );
		if ( ! is_array( $meta ) ) {
			$meta = [];
		}

		return NavigationConfig::from_post( $post, $meta );
	}

	/**
	 * Retrieve all navigation configurations.
	 *
	 * @param bool $include_inactive Whether to include draft/disabled configurations.
	 * @return NavigationConfig[]
	 */
	public function get_all( bool $include_inactive = false ): array {
		$posts = get_posts(
			[
				'post_type'        => self::POST_TYPE,
				'post_status'      => $include_inactive ? [ 'publish', 'draft' ] : 'publish',
				'numberposts'      => -1,
				'orderby'          => 'menu_order',
				'order'            => 'ASC',
				'suppress_filters' => true,
			]
		);

		$configs = [];
		foreach ( $posts as $post ) {
			$meta      = get_post_meta( $post->ID, self::META_KEY, true );
			$configs[] = NavigationConfig::from_post( $post, is_array( $meta ) ? $meta : [] );
		}

		// Sort by priority ASC (lower number = higher priority)
		usort(
			$configs,
			static fn( NavigationConfig $a, NavigationConfig $b ) => $a->priority <=> $b->priority
		);

		return $configs;
	}

	public function get_default(): ?NavigationConfig {
		$all = $this->get_all( false );
		foreach ( $all as $config ) {
			if ( $config->is_default ) {
				return $config;
			}
		}
		// Fallback to first available published dock if none explicitly marked default
		return $all[0] ?? null;
	}

	public function save( NavigationConfig $config ): int {
		$is_new = $config->id <= 0;

		$post_data = [
			'post_type'   => self::POST_TYPE,
			'post_title'  => sanitize_text_field( $config->title ),
			'post_status' => in_array( $config->status, [ 'publish', 'draft' ], true ) ? $config->status : 'publish',
			'menu_order'  => $config->priority,
		];

		if ( $is_new ) {
			$post_id = wp_insert_post( $post_data, true );
			if ( is_wp_error( $post_id ) ) {
				return 0;
			}
			$config->id = $post_id;
		} else {
			$post_data['ID'] = $config->id;
			$updated         = wp_update_post( $post_data, true );
			if ( is_wp_error( $updated ) ) {
				return 0;
			}
			$post_id = $config->id;
		}

		// If this is set as default, unset default on other configurations.
		if ( $config->is_default ) {
			$this->clear_other_defaults( $post_id );
		}

		update_post_meta( $post_id, self::META_KEY, $config->to_meta_array() );
		$this->clear_cache();

		return $post_id;
	}

	public function delete( int $id ): bool {
		$deleted = wp_delete_post( $id, true );
		if ( $deleted ) {
			$this->clear_cache();
			return true;
		}
		return false;
	}

	public function duplicate( int $id ): int {
		$original = $this->get( $id );
		if ( ! $original ) {
			return 0;
		}

		$duplicate_title = sprintf(
			/* translators: %s: original navigation title */
			__( '%s (Copy)', 'wpcalibrate-mobile-dock' ),
			$original->title
		);

		$new_config = new NavigationConfig(
			id: 0,
			title: $duplicate_title,
			status: 'draft', // duplicated docks start as draft
			is_default: false,
			priority: $original->priority + 1,
			items: $original->items,
			appearance: $original->appearance,
			visibility_rules: $original->visibility_rules,
			breakpoint: $original->breakpoint,
			show_on_tablet: $original->show_on_tablet,
			tablet_breakpoint: $original->tablet_breakpoint
		);

		return $this->save( $new_config );
	}

	public function set_default( int $id ): bool {
		$config = $this->get( $id );
		if ( ! $config ) {
			return false;
		}

		$config->is_default = true;
		$this->save( $config );
		return true;
	}

	public function update_priorities( array $ordered_ids ): bool {
		$priority = 1;
		foreach ( $ordered_ids as $id ) {
			$id = absint( $id );
			if ( $id <= 0 ) {
				continue;
			}
			$config = $this->get( $id );
			if ( $config ) {
				$config->priority = $priority++;
				$this->save( $config );
			}
		}
		$this->clear_cache();
		return true;
	}

	private function clear_other_defaults( int $current_id ): void {
		$all = $this->get_all( true );
		foreach ( $all as $c ) {
			if ( $c->id !== $current_id && $c->is_default ) {
				$c->is_default = false;
				update_post_meta( $c->id, self::META_KEY, $c->to_meta_array() );
			}
		}
	}

	public function clear_cache(): void {
		delete_transient( self::CACHE_KEY );
		wp_cache_delete( self::CACHE_KEY, 'wpcalibrate_mobile_dock' );
	}
}
