<?php
/**
 * Active State Matcher Unit Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Unit
 */

namespace WPCalibrate\MobileDock\Tests\Unit;

use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Frontend\ActiveStateMatcher;

class ActiveStateMatcherTest {

	public function test_home_active_on_front_page(): void {
		$matcher = new ActiveStateMatcher();
		$home_item = new NavigationItem( id: 'home', action_type: NavigationItem::ACTION_HOME );

		assert( $matcher->is_active( $home_item, [ 'is_front_page' => true ] ), 'Home item must be active on front page' );
		assert( ! $matcher->is_active( $home_item, [ 'is_front_page' => false ] ), 'Home item must not be active on non-front page' );
	}

	public function test_page_active_matching(): void {
		$matcher = new ActiveStateMatcher();
		$page_item = new NavigationItem( id: 'about', action_type: NavigationItem::ACTION_PAGE, target: '25' );

		assert( $matcher->is_active( $page_item, [ 'is_page' => true, 'page_id' => 25 ] ), 'Page item must be active when page ID matches' );
		assert( ! $matcher->is_active( $page_item, [ 'is_page' => true, 'page_id' => 99 ] ), 'Page item must not be active when page ID differs' );
	}

	public function test_avoids_substring_false_positives_for_custom_urls(): void {
		$matcher = new ActiveStateMatcher();

		$target_url  = 'https://example.com/shop';
		$current_url = 'https://example.com/shop-custom-items';

		// Substring is present in current_url ("shop"), but path is different
		assert( ! $matcher->match_custom_url( $target_url, $current_url ), 'Must NOT mark active on substring match' );

		// Exact path match
		assert( $matcher->match_custom_url( $target_url, 'https://example.com/shop/' ), 'Must match exact path regardless of trailing slash' );
	}

	public function test_external_urls_not_marked_active(): void {
		$matcher = new ActiveStateMatcher();

		$external_target = 'https://google.com/search';
		$current_url     = 'https://example.com/search';

		assert( ! $matcher->match_custom_url( $external_target, $current_url ), 'External URLs must not be marked active' );
	}
}
