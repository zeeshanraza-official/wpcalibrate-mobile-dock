<?php
/**
 * Navigation Priority and Conflict Resolution Unit Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Unit
 */

namespace WPCalibrate\MobileDock\Tests\Unit;

use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\VisibilityRule;
use WPCalibrate\MobileDock\Rules\RuleEngine;

class NavigationPriorityTest {

	public function test_explicit_content_beats_default(): void {
		$engine = new RuleEngine();

		$default_dock = new NavigationConfig(
			id: 1,
			title: 'Default Dock',
			status: 'publish',
			is_default: true,
			priority: 10,
			visibility_rules: [
				new VisibilityRule( condition_type: VisibilityRule::TYPE_ENTIRE_SITE, operator: VisibilityRule::OPERATOR_INCLUDE )
			]
		);

		$contact_page_dock = new NavigationConfig(
			id: 2,
			title: 'Contact Page Custom Dock',
			status: 'publish',
			is_default: false,
			priority: 10,
			visibility_rules: [
				new VisibilityRule( condition_type: VisibilityRule::TYPE_PAGES, operator: VisibilityRule::OPERATOR_INCLUDE, target_ids: [ 42 ] )
			]
		);

		$context = [
			'is_logged_in' => false,
			'is_page'      => true,
			'page_id'      => 42,
		];

		$winner = $engine->resolve( [ $default_dock, $contact_page_dock ], $context );
		assert( null !== $winner, 'A winning dock should be resolved' );
		assert( $winner->id === 2, 'Page-specific dock (score 400) must beat default dock (score 100)' );
	}

	public function test_priority_tie_breaker(): void {
		$engine = new RuleEngine();

		$dock_low_priority = new NavigationConfig(
			id: 10,
			title: 'General Dock 1',
			status: 'publish',
			priority: 5,
			visibility_rules: [
				new VisibilityRule( condition_type: VisibilityRule::TYPE_ENTIRE_SITE, operator: VisibilityRule::OPERATOR_INCLUDE )
			]
		);

		$dock_high_priority = new NavigationConfig(
			id: 20,
			title: 'General Dock 2 (Higher Priority)',
			status: 'publish',
			priority: 1, // Lower number = higher priority
			visibility_rules: [
				new VisibilityRule( condition_type: VisibilityRule::TYPE_ENTIRE_SITE, operator: VisibilityRule::OPERATOR_INCLUDE )
			]
		);

		$context = [ 'is_logged_in' => false ];

		$winner = $engine->resolve( [ $dock_low_priority, $dock_high_priority ], $context );
		assert( null !== $winner, 'A winning dock should be resolved' );
		assert( $winner->id === 20, 'Dock with priority 1 must beat dock with priority 5 when specificity is equal' );
	}
}
