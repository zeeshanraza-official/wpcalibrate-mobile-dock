<?php
/**
 * Action Resolver Unit Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Unit
 */

namespace WPCalibrate\MobileDock\Tests\Unit;

use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Frontend\ActionResolver;

class ActionResolverTest {

	public function test_phone_normalization(): void {
		$raw_phone = '+44 (0) 7474-795 976';
		$normalized = ActionResolver::normalize_phone( $raw_phone );
		assert( $normalized === '+4407474795976', 'Phone normalization should preserve leading plus and digits' );
	}

	public function test_whatsapp_url_generation(): void {
		$url = ActionResolver::generate_whatsapp_url( '+447474795976', 'Hello WPCalibrate!' );
		assert( str_starts_with( $url, 'https://api.whatsapp.com/send?' ), 'Must point to WhatsApp send API' );
		assert( str_contains( $url, 'phone=447474795976' ), 'Must contain numeric phone parameter' );
		assert( str_contains( $url, 'text=Hello%20WPCalibrate%21' ), 'Must RFC3986 URL-encode prefilled message' );
	}

	public function test_mailto_url_generation(): void {
		$url = ActionResolver::generate_mailto_url( 'support@wpcalibrate.com', 'Mobile Dock Help', 'Please assist.' );
		assert( str_starts_with( $url, 'mailto:support@wpcalibrate.com' ), 'Must be mailto destination' );
		assert( str_contains( $url, 'subject=Mobile%20Dock%20Help' ), 'Subject must be encoded' );
		assert( str_contains( $url, 'body=Please%20assist.' ), 'Body must be encoded' );
	}

	public function test_new_tab_rel_attributes(): void {
		$resolver = new ActionResolver();
		$item = new NavigationItem(
			id: 'ext_link',
			action_type: NavigationItem::ACTION_CUSTOM_URL,
			target: 'https://example.com',
			open_new_tab: true,
			rel: 'nofollow'
		);

		$resolved = $resolver->resolve( $item );
		assert( isset( $resolved['attributes']['target'] ) && '_blank' === $resolved['attributes']['target'], 'Target must be _blank' );
		assert( isset( $resolved['attributes']['rel'] ), 'Must have rel attribute' );
		assert( str_contains( $resolved['attributes']['rel'], 'noopener' ), 'Must contain noopener' );
		assert( str_contains( $resolved['attributes']['rel'], 'noreferrer' ), 'Must contain noreferrer' );
		assert( str_contains( $resolved['attributes']['rel'], 'nofollow' ), 'Must preserve user nofollow rel' );
	}

	public function test_button_actions_resolution(): void {
		$resolver = new ActionResolver();

		$back_item = new NavigationItem( id: 'back', action_type: NavigationItem::ACTION_BACK );
		$res_back  = $resolver->resolve( $back_item );
		assert( $res_back['is_button'] === true, 'Back action must resolve as button' );
		assert( $res_back['attributes']['data-wpcal-action'] === 'back', 'Must have data-wpcal-action="back"' );

		$search_item = new NavigationItem( id: 'search', action_type: NavigationItem::ACTION_SEARCH );
		$res_search  = $resolver->resolve( $search_item );
		assert( $res_search['is_button'] === true, 'Search action must resolve as button' );
		assert( $res_search['attributes']['aria-haspopup'] === 'dialog', 'Must have aria-haspopup="dialog"' );
	}
}
