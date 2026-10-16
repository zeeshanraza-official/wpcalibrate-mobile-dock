<?php
/**
 * Test Suite Bootstrap with lightweight WordPress environment doubles.
 *
 * @package WPCalibrate\MobileDock\Tests
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/../../' );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_VERSION' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_VERSION', '1.0.0' );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_DB_VERSION' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_DB_VERSION', 100 );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_PATH' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_PATH', dirname( __DIR__ ) . '/' );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_URL' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_URL', 'https://example.com/wp-content/plugins/wpcalibrate-mobile-dock/' );
}
if ( ! defined( 'WPCALIBRATE_MOBILE_DOCK_BASENAME' ) ) {
	define( 'WPCALIBRATE_MOBILE_DOCK_BASENAME', 'wpcalibrate-mobile-dock/wpcalibrate-mobile-dock.php' );
}

// In-memory WordPress mock state
class WPMockState {
	public static array $options = [];
	public static array $posts = [];
	public static array $post_meta = [];
	public static array $filters = [];
	public static array $actions = [];
	public static string $current_user_cap = 'manage_options';
	public static bool $is_logged_in = false;
	public static array $nonces = [];
	public static int $post_counter = 1;
	public static bool $wc_active = false;
	public static int $cart_count = 0;

	public static function reset(): void {
		self::$options = [];
		self::$posts = [];
		self::$post_meta = [];
		self::$filters = [];
		self::$actions = [];
		self::$current_user_cap = 'manage_options';
		self::$is_logged_in = false;
		self::$nonces = [];
		self::$post_counter = 1;
		self::$wc_active = false;
		self::$cart_count = 0;
	}
}

global $wpdb;
$wpdb = new class {
	public string $options = 'wp_options';
	public function query( $sql ) { return true; }
	public function prepare( $query, ...$args ) { return $query; }
	public function esc_like( $text ) { return addcslashes( $text, '_%\\' ); }
};

// WordPress Core Function Doubles
if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
	function esc_attr( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
	function esc_url( $url ) { return filter_var( $url, FILTER_SANITIZE_URL ) ?: ''; }
	function esc_url_raw( $url ) { return esc_url( $url ); }
	function esc_js( $text ) { return addslashes( (string) $text ); }
	function esc_textarea( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
	function sanitize_text_field( $str ) { return trim( strip_tags( (string) $str ) ); }
	function sanitize_key( $key ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) ); }
	function sanitize_html_class( $class ) { return preg_replace( '/[^a-zA-Z0-9_\-]/', '', (string) $class ); }
	function sanitize_email( $email ) { return filter_var( $email, FILTER_SANITIZE_EMAIL ) ?: ''; }
	function absint( $num ) { return abs( (int) $num ); }
	function wp_strip_all_tags( $text ) { return strip_tags( (string) $text ); }

	function __( $text, $domain = 'default' ) { return $text; }
	function _e( $text, $domain = 'default' ) { echo $text; }
	function esc_html__( $text, $domain = 'default' ) { return esc_html( $text ); }
	function esc_html_e( $text, $domain = 'default' ) { echo esc_html( $text ); }
	function esc_attr__( $text, $domain = 'default' ) { return esc_attr( $text ); }
	function esc_attr_e( $text, $domain = 'default' ) { echo esc_attr( $text ); }
	function _n( $single, $plural, $number, $domain = 'default' ) { return $number === 1 ? $single : $plural; }

	function plugin_dir_path( $file ) { return trailingslashit( dirname( $file ) ); }
	function plugin_dir_url( $file ) { return 'https://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/'; }
	function plugin_basename( $file ) { return basename( dirname( $file ) ) . '/' . basename( $file ); }
	function register_activation_hook( $file, $callback ) {}
	function register_deactivation_hook( $file, $callback ) {}
	function load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = false ) { return true; }
	function add_query_arg( $args = [], $input = '' ) { return $input ?: 'https://example.com/'; }
	function get_search_query() { return ''; }

	function get_option( $name, $default = false ) {
		return WPMockState::$options[ $name ] ?? $default;
	}
	function update_option( $name, $value, $autoload = null ) {
		WPMockState::$options[ $name ] = $value;
		return true;
	}
	function delete_option( $name ) {
		unset( WPMockState::$options[ $name ] );
		return true;
	}
	function delete_transient( $name ) { return true; }
	function wp_cache_delete( $key, $group = '' ) { return true; }

	function wp_parse_args( $args, $defaults = [] ) {
		if ( is_object( $args ) ) {
			$args = get_object_vars( $args );
		}
		return array_merge( $defaults, (array) $args );
	}

	function apply_filters( $tag, $value, ...$args ) {
		if ( isset( WPMockState::$filters[ $tag ] ) ) {
			foreach ( WPMockState::$filters[ $tag ] as $callback ) {
				$value = call_user_func( $callback, $value, ...$args );
			}
		}
		return $value;
	}

	function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		WPMockState::$filters[ $tag ][] = $function_to_add;
		return true;
	}

	function add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		WPMockState::$actions[ $tag ][] = $function_to_add;
		return true;
	}

	function do_action( $tag, ...$args ) {
		if ( isset( WPMockState::$actions[ $tag ] ) ) {
			foreach ( WPMockState::$actions[ $tag ] as $callback ) {
				call_user_func( $callback, ...$args );
			}
		}
	}

	function home_url( $path = '' ) {
		return 'https://example.com' . ( $path ? '/' . ltrim( $path, '/' ) : '' );
	}
	function admin_url( $path = '' ) {
		return 'https://example.com/wp-admin/' . ltrim( $path, '/' );
	}
	function get_permalink( $id = 0 ) {
		return home_url( '/page-' . absint( $id ) . '/' );
	}
	function wp_login_url() {
		return home_url( '/wp-login.php' );
	}

	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( $url, $component );
	}
	function untrailingslashit( $string ) {
		return rtrim( $string, '/\\' );
	}
	function trailingslashit( $string ) {
		return untrailingslashit( $string ) . '/';
	}

	function current_user_can( $capability ) {
		return WPMockState::$current_user_cap === $capability;
	}
	function is_user_logged_in() {
		return WPMockState::$is_logged_in;
	}

	function check_admin_referer( $action = -1, $query_arg = '_wpnonce' ) {
		$nonce = $_POST[ $query_arg ] ?? $_GET[ $query_arg ] ?? '';
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			throw new \RuntimeException( 'Invalid nonce' );
		}
		return true;
	}

	function wp_create_nonce( $action = -1 ) {
		$nonce = 'nonce_' . $action . '_' . time();
		WPMockState::$nonces[ $action ] = $nonce;
		return $nonce;
	}

	function wp_verify_nonce( $nonce, $action = -1 ) {
		return isset( WPMockState::$nonces[ $action ] ) && WPMockState::$nonces[ $action ] === $nonce;
	}

	function wp_nonce_url( $actionurl, $action = -1, $name = '_wpnonce' ) {
		$nonce = wp_create_nonce( $action );
		return $actionurl . ( str_contains( $actionurl, '?' ) ? '&' : '?' ) . $name . '=' . $nonce;
	}

	function register_post_type( $post_type, $args = [] ) {
		return true;
	}

	class WP_Post {
		public int $ID = 0;
		public string $post_title = '';
		public string $post_status = 'publish';
		public string $post_type = 'wpcal_mobile_dock';
		public int $menu_order = 0;
	}

	function get_post( $id ) {
		return WPMockState::$posts[ $id ] ?? null;
	}

	function get_posts( $args = [] ) {
		$results = array_values( WPMockState::$posts );
		if ( ! empty( $args['post_status'] ) && 'any' !== $args['post_status'] ) {
			$statuses = (array) $args['post_status'];
			$results  = array_filter( $results, fn( $p ) => in_array( $p->post_status, $statuses, true ) );
		}
		if ( ! empty( $args['fields'] ) && 'ids' === $args['fields'] ) {
			return array_map( fn( $p ) => $p->ID, $results );
		}
		return $results;
	}

	function wp_insert_post( $postarr, $wp_error = false ) {
		$post = new WP_Post();
		$post->ID = WPMockState::$post_counter++;
		$post->post_title = $postarr['post_title'] ?? '';
		$post->post_status = $postarr['post_status'] ?? 'publish';
		$post->post_type = $postarr['post_type'] ?? 'post';
		$post->menu_order = $postarr['menu_order'] ?? 0;

		WPMockState::$posts[ $post->ID ] = $post;
		return $post->ID;
	}

	function wp_update_post( $postarr, $wp_error = false ) {
		$id = $postarr['ID'] ?? 0;
		if ( ! isset( WPMockState::$posts[ $id ] ) ) {
			return 0;
		}
		$post = WPMockState::$posts[ $id ];
		if ( isset( $postarr['post_title'] ) ) {
			$post->post_title = $postarr['post_title'];
		}
		if ( isset( $postarr['post_status'] ) ) {
			$post->post_status = $postarr['post_status'];
		}
		if ( isset( $postarr['menu_order'] ) ) {
			$post->menu_order = $postarr['menu_order'];
		}
		return $id;
	}

	function wp_delete_post( $postid, $force_delete = false ) {
		if ( isset( WPMockState::$posts[ $postid ] ) ) {
			unset( WPMockState::$posts[ $postid ] );
			unset( WPMockState::$post_meta[ $postid ] );
			return true;
		}
		return false;
	}

	function get_post_meta( $post_id, $key = '', $single = false ) {
		if ( $key ) {
			return WPMockState::$post_meta[ $post_id ][ $key ] ?? ( $single ? '' : [] );
		}
		return WPMockState::$post_meta[ $post_id ] ?? [];
	}

	function update_post_meta( $post_id, $key, $value ) {
		WPMockState::$post_meta[ $post_id ][ $key ] = $value;
		return true;
	}

	function is_wp_error( $thing ) {
		return $thing instanceof WP_Error;
	}

	class WP_Error {
		public function __construct( public string $code = '', public string $message = '' ) {}
		public function get_error_message() { return $this->message; }
	}

	function is_admin() { return false; }
	function wp_doing_ajax() { return false; }
	function wp_is_json_request() { return false; }
	function wp_enqueue_script( $handle, $src = '', $deps = [], $ver = false, $in_footer = false ) {}
	function wp_enqueue_style( $handle, $src = '', $deps = [], $ver = false, $media = 'all' ) {}
	function wp_localize_script( $handle, $object_name, $l10n ) {}
	function wp_add_inline_style( $handle, $data ) {}

	function is_front_page() { return false; }
	function is_home() { return false; }
	function is_page( $page = '' ) { return false; }
	function is_single( $post = '' ) { return false; }
	function is_singular( $post_types = '' ) { return false; }
	function get_the_ID() { return 1; }
	function get_post_type() { return 'page'; }
	function is_tax() { return false; }
	function is_category() { return false; }
	function is_tag() { return false; }
	function get_queried_object_id() { return 0; }
	function is_multisite() { return false; }
	function wp_get_theme() {
		return new class {
			public function get( $prop ) { return $prop === 'Name' ? 'Twenty Twenty-Four' : '1.0'; }
		};
	}
	function get_pages( $args = [] ) { return []; }
	function checked( $checked, $current = true, $echo = true ) {
		$res = ( (string) $checked === (string) $current ) ? ' checked="checked"' : '';
		if ( $echo ) echo $res;
		return $res;
	}
	function selected( $selected, $current = true, $echo = true ) {
		$res = ( (string) $selected === (string) $current ) ? ' selected="selected"' : '';
		if ( $echo ) echo $res;
		return $res;
	}
	function disabled( $disabled, $current = true, $echo = true ) {
		$res = ( (string) $disabled === (string) $current ) ? ' disabled="disabled"' : '';
		if ( $echo ) echo $res;
		return $res;
	}

	// WooCommerce Mocks
	function WC() {
		return new class {
			public $cart;
			public function __construct() {
				$this->cart = new class {
					public function get_cart_contents_count() {
						return WPMockState::$cart_count;
					}
				};
			}
		};
	}
	function wc_get_page_permalink( $page ) { return home_url( '/' . $page . '/' ); }
	function wc_get_cart_url() { return home_url( '/cart/' ); }
	function get_term_link( $term, $taxonomy = '' ) { return home_url( '/category/' . $term . '/' ); }
}

// Autoload plugin classes
spl_autoload_register(
	static function ( string $class ): void {
		$prefix   = 'WPCalibrate\\MobileDock\\';
		$base_dir = dirname( __DIR__ ) . '/src/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);
