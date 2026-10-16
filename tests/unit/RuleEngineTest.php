<?php
/**
 * Rule Engine Unit Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Unit
 */

namespace WPCalibrate\MobileDock\Tests\Unit;

use WPCalibrate\MobileDock\Domain\NavigationConfig;
use WPCalibrate\MobileDock\Domain\VisibilityRule;
use WPCalibrate\MobileDock\Rules\RuleEngine;

class RuleEngineTest {

	public function test_matches_entire_site(): void {
		$engine = new RuleEngine();

		$config = new NavigationConfig(
			id: 1,
			title: 'Entire Site Dock',
			status: 'publish',
			visibility_rules: [
				new VisibilityRule( condition_type: VisibilityRule::TYPE_ENTIRE_SITE, operator: VisibilityRule::OPERATOR_INCLUDE )
			]
		);

		$score = $engine->evaluate_config( $config, [ 'is_logged_in' => false ] );
		assert( false !== $score, 'Entire site dock should match any view' );
		assert( $score === RuleEngine::SPECIFICITY_ENTIRE_SITE, 'Specificity score should be ENTIRE_SITE' );
	}

	public function test_exclusion_takes_precedence(): void {
		$engine = new RuleEngine();

		$config = new NavigationConfig(
			id: 2,
			title: 'Dock Excluded on Checkout',
			status: 'publish',
			visibility_rules: [
				new VisibilityRule( condition_type: VisibilityRule::TYPE_ENTIRE_SITE, operator: VisibilityRule::OPERATOR_INCLUDE ),
				new VisibilityRule( condition_type: VisibilityRule::TYPE_WC_CHECKOUT, operator: VisibilityRule::OPERATOR_EXCLUDE )
			]
		);

		// Non-checkout view
		$match = $engine->evaluate_config( $config, [ 'is_logged_in' => false, 'is_wc_checkout' => false ] );
		assert( false !== $match, 'Should match when not on checkout' );

		// Checkout view
		$checkout_match = $engine->evaluate_config( $config, [ 'is_logged_in' => false, 'is_wc_checkout' => true ] );
		assert( false === $checkout_match, 'Exclusion must override inclusion on checkout' );
	}

	public function test_authentication_rule_matching(): void {
		$engine = new RuleEngine();

		$logged_in_config = new NavigationConfig(
			id: 3,
			title: 'Logged-in Only Dock',
			status: 'publish',
			visibility_rules: [
				new VisibilityRule( condition_type: VisibilityRule::TYPE_ENTIRE_SITE, operator: VisibilityRule::OPERATOR_INCLUDE, user_auth: VisibilityRule::AUTH_LOGGED_IN )
			]
		);

		$guest_match = $engine->evaluate_config( $logged_in_config, [ 'is_logged_in' => false ] );
		assert( false === $guest_match, 'Logged-in dock should not match guest visitors' );

		$user_match = $engine->evaluate_config( $logged_in_config, [ 'is_logged_in' => true ] );
		assert( false !== $user_match, 'Logged-in dock should match authenticated visitors' );
	}
}
