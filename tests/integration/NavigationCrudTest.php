<?php
/**
 * Navigation CRUD and Multiple Configuration Isolation Integration Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Integration
 */

namespace WPCalibrate\MobileDock\Tests\Integration;

use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Navigation\NavigationRepository;
use WPMockState;

class NavigationCrudTest {

	public function test_crud_and_seven_items_support(): void {
		WPMockState::reset();
		$repo = new NavigationRepository();

		// Create dock with 7 distinct items
		$items = [];
		for ( $i = 1; $i <= 7; $i++ ) {
			$items[] = new NavigationItem(
				id: 'item_' . $i,
				label: 'Item ' . $i,
				icon: 'home',
				action_type: NavigationItem::ACTION_HOME
			);
		}

		$dock = new NavigationConfig(
			id: 0,
			title: 'Seven Items Dock',
			status: 'publish',
			items: $items
		);

		$dock_id = $repo->save( $dock );
		assert( $dock_id > 0, 'Dock ID should be greater than 0 after saving' );

		// Retrieve dock
		$retrieved = $repo->get( $dock_id );
		assert( null !== $retrieved, 'Saved dock must be retrievable' );
		assert( count( $retrieved->items ) === 7, 'Retrieved dock must contain all 7 items' );
		assert( $retrieved->title === 'Seven Items Dock', 'Title must match' );

		// Update dock
		$retrieved->title = 'Updated Title';
		$repo->save( $retrieved );
		$updated = $repo->get( $dock_id );
		assert( $updated->title === 'Updated Title', 'Updated title must be saved' );

		// Duplicate dock
		$copy_id = $repo->duplicate( $dock_id );
		assert( $copy_id > 0 && $copy_id !== $dock_id, 'Duplicate must produce a new unique dock ID' );
		$copy = $repo->get( $copy_id );
		assert( str_contains( $copy->title, '(Copy)' ), 'Duplicate title must indicate copy' );
		assert( $copy->status === 'draft', 'Duplicated dock starts in draft status' );
		assert( count( $copy->items ) === 7, 'Duplicated dock must copy items' );

		// Delete dock
		$deleted = $repo->delete( $dock_id );
		assert( $deleted === true, 'Delete operation must succeed' );
		assert( null === $repo->get( $dock_id ), 'Deleted dock should no longer be retrievable' );
		assert( null !== $repo->get( $copy_id ), 'Duplicate must remain intact when original is deleted' );
	}

	public function test_multiple_navigations_isolation(): void {
		WPMockState::reset();
		$repo = new NavigationRepository();

		$dock_a = new NavigationConfig(
			id: 0,
			title: 'Dock Alpha',
			items: [ new NavigationItem( id: 'a1', label: 'Alpha Item 1' ) ]
		);
		$id_a = $repo->save( $dock_a );

		$dock_b = new NavigationConfig(
			id: 0,
			title: 'Dock Beta',
			items: [ new NavigationItem( id: 'b1', label: 'Beta Item 1' ) ]
		);
		$id_b = $repo->save( $dock_b );

		// Edit Dock A
		$loaded_a = $repo->get( $id_a );
		$loaded_a->items[] = new NavigationItem( id: 'a2', label: 'Alpha Item 2' );
		$repo->save( $loaded_a );

		// Verify Dock B is completely unaffected
		$loaded_b = $repo->get( $id_b );
		assert( count( $loaded_b->items ) === 1, 'Dock B items must not be affected by edits to Dock A' );
		assert( $loaded_b->items[0]->id === 'b1', 'Dock B items must remain isolated' );
	}
}
