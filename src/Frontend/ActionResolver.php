<?php
/**
 * Action Resolver for Mobile Dock items.
 *
 * Resolves URLs, buttons, safe tel/mailto/whatsapp strings, and accessibility attributes.
 *
 * @package WPCalibrate\MobileDock\Frontend
 */

namespace WPCalibrate\MobileDock\Frontend;

use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\WooCommerce\WooCommerceIntegration;

defined( 'ABSPATH' ) || exit;

class ActionResolver {

	/**
	 * Resolves item target data: href, tag (a vs button), data attributes, rel, target.
	 *
	 * @param NavigationItem $item
	 * @return array{tag: string, href: string, attributes: array<string, string>, is_button: bool}
	 */
	public function resolve( NavigationItem $item ): array {
		$tag        = 'a';
		$href       = '#';
		$attributes = [];
		$is_button  = false;

		switch ( $item->action_type ) {
			case NavigationItem::ACTION_HOME:
				$href = home_url( '/' );
				break;

			case NavigationItem::ACTION_PAGE:
			case NavigationItem::ACTION_POST:
				$id = absint( $item->target );
				$href = $id > 0 ? ( get_permalink( $id ) ?: home_url( '/' ) ) : home_url( '/' );
				break;

			case NavigationItem::ACTION_BLOG:
				$page_for_posts = get_option( 'page_for_posts' );
				$href = $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/' );
				break;

			case NavigationItem::ACTION_CUSTOM_URL:
				$href = esc_url_raw( $item->target );
				if ( empty( $href ) ) {
					$href = '#';
				}
				if ( $item->open_new_tab ) {
					$attributes['target'] = '_blank';
					$rel_tokens = [ 'noopener', 'noreferrer' ];
					if ( ! empty( $item->rel ) ) {
						$rel_tokens[] = sanitize_text_field( $item->rel );
					}
					$attributes['rel'] = implode( ' ', array_unique( $rel_tokens ) );
				} elseif ( ! empty( $item->rel ) ) {
					$attributes['rel'] = sanitize_text_field( $item->rel );
				}
				break;

			case NavigationItem::ACTION_SEARCH:
				$tag                         = 'button';
				$is_button                   = true;
				$href                        = '';
				$attributes['type']          = 'button';
				$attributes['data-wpcal-action'] = 'search';
				$attributes['aria-haspopup'] = 'dialog';
				$attributes['aria-expanded'] = 'false';
				$attributes['aria-controls'] = 'wpcal-search-modal';
				break;

			case NavigationItem::ACTION_BACK:
				$tag                         = 'button';
				$is_button                   = true;
				$href                        = '';
				$attributes['type']          = 'button';
				$attributes['data-wpcal-action'] = 'back';
				break;

			case NavigationItem::ACTION_SCROLL_TOP:
				$tag                         = 'button';
				$is_button                   = true;
				$href                        = '';
				$attributes['type']          = 'button';
				$attributes['data-wpcal-action'] = 'scroll-top';
				break;

			case NavigationItem::ACTION_PHONE:
				$phone = self::normalize_phone( $item->target );
				$href  = 'tel:' . $phone;
				break;

			case NavigationItem::ACTION_WHATSAPP:
				$href = self::generate_whatsapp_url( $item->target, $item->extra['whatsapp_message'] ?? '' );
				$attributes['target'] = '_blank';
				$attributes['rel']    = 'noopener noreferrer';
				break;

			case NavigationItem::ACTION_EMAIL:
				$href = self::generate_mailto_url(
					$item->target,
					$item->extra['email_subject'] ?? '',
					$item->extra['email_body'] ?? ''
				);
				break;

			case NavigationItem::ACTION_WC_SHOP:
				$href = WooCommerceIntegration::get_shop_url();
				break;

			case NavigationItem::ACTION_WC_CATEGORY:
				$cat_id = absint( $item->target );
				$href   = WooCommerceIntegration::get_category_url( $cat_id );
				break;

			case NavigationItem::ACTION_WC_CART:
				$href = WooCommerceIntegration::get_cart_url();
				break;

			case NavigationItem::ACTION_WC_ACCOUNT:
				$href = WooCommerceIntegration::get_account_url();
				break;

			default:
				$href = home_url( '/' );
				break;
		}

		return [
			'tag'        => $tag,
			'href'       => $href,
			'attributes' => $attributes,
			'is_button'  => $is_button,
		];
	}

	/**
	 * Normalize phone numbers safely (keeps leading + and digits).
	 */
	public static function normalize_phone( string $phone ): string {
		$trimmed = trim( $phone );
		$is_plus = str_starts_with( $trimmed, '+' );
		$digits  = preg_replace( '/\D+/', '', $trimmed );
		return ( $is_plus ? '+' : '' ) . $digits;
	}

	/**
	 * Generate WhatsApp URL safely without external script dependencies.
	 */
	public static function generate_whatsapp_url( string $phone, string $prefilled_message = '' ): string {
		$normalized_phone = preg_replace( '/\D+/', '', $phone ); // WhatsApp expects numbers only
		$url = 'https://api.whatsapp.com/send?phone=' . rawurlencode( $normalized_phone );
		if ( ! empty( $prefilled_message ) ) {
			$url .= '&text=' . rawurlencode( $prefilled_message );
		}
		return $url;
	}

	/**
	 * Generate Mailto destination safely with encoded subject and body.
	 */
	public static function generate_mailto_url( string $email, string $subject = '', string $body = '' ): string {
		$sanitized_email = sanitize_email( $email );
		$params          = [];

		if ( ! empty( $subject ) ) {
			$params['subject'] = $subject;
		}
		if ( ! empty( $body ) ) {
			$params['body'] = $body;
		}

		$query = ! empty( $params ) ? '?' . http_build_query( $params, '', '&', PHP_QUERY_RFC3986 ) : '';
		return 'mailto:' . $sanitized_email . $query;
	}
}
