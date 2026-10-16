<?php
/**
 * Center Raised Action Button Uniqueness Unit Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Unit
 */

namespace WPCalibrate\MobileDock\Tests\Unit;

use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\NavigationItem;

class CenterButtonTest {

	public function test_enforces_at_most_one_center_button(): void {
		$item1 = new NavigationItem( id: 'item_1', label: 'Item 1', is_center_raised: true );
		$item2 = new NavigationItem( id: 'item_2', label: 'Item 2', is_center_raised: true );
		$item3 = new NavigationItem( id: 'item_3', label: 'Item 3', is_center_raised: false );

		$config = new NavigationConfig(
			id: 1,
			title: 'Test Dock',
			items: [ $item1, $item2, $item3 ]
		);

		// Verification after NavigationConfig constructor enforces uniqueness:
		assert( $item1->is_center_raised === true, 'First center-raised item remains true' );
		assert( $item2->is_center_raised === false, 'Second center-raised item must be reset to false' );
		assert( $item3->is_center_raised === false, 'Non-center-raised item remains false' );

		// Count center raised items
		$center_count = 0;
		foreach ( $config->items as $item ) {
			if ( $item->is_center_raised ) {
				$center_count++;
			}
		}

		assert( $center_count === 1, 'Exactly one center-raised action must exist' );
	}
}
