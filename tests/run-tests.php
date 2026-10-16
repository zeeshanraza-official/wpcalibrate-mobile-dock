<?php
/**
 * Test Runner Script for WPCalibrate Mobile Dock.
 *
 * Runs all unit and integration tests under PHP CLI and asserts correctness.
 *
 * @package WPCalibrate\MobileDock\Tests
 */

require_once __DIR__ . '/bootstrap.php';

// Load Unit Tests
require_once __DIR__ . '/unit/RuleEngineTest.php';
require_once __DIR__ . '/unit/NavigationPriorityTest.php';
require_once __DIR__ . '/unit/SanitizationTest.php';
require_once __DIR__ . '/unit/AppearanceSettingsTest.php';
require_once __DIR__ . '/unit/IconRegistryTest.php';
require_once __DIR__ . '/unit/ActionResolverTest.php';
require_once __DIR__ . '/unit/ActiveStateMatcherTest.php';
require_once __DIR__ . '/unit/CenterButtonTest.php';
require_once __DIR__ . '/unit/MigrationManagerTest.php';

// Load Integration Tests
require_once __DIR__ . '/integration/NavigationCrudTest.php';
require_once __DIR__ . '/integration/LifecycleAndRetentionTest.php';
require_once __DIR__ . '/integration/MenuAndPermissionTest.php';
require_once __DIR__ . '/integration/WooCommerceIntegrationTest.php';

$test_classes = [
	\WPCalibrate\MobileDock\Tests\Unit\RuleEngineTest::class,
	\WPCalibrate\MobileDock\Tests\Unit\NavigationPriorityTest::class,
	\WPCalibrate\MobileDock\Tests\Unit\SanitizationTest::class,
	\WPCalibrate\MobileDock\Tests\Unit\AppearanceSettingsTest::class,
	\WPCalibrate\MobileDock\Tests\Unit\IconRegistryTest::class,
	\WPCalibrate\MobileDock\Tests\Unit\ActionResolverTest::class,
	\WPCalibrate\MobileDock\Tests\Unit\ActiveStateMatcherTest::class,
	\WPCalibrate\MobileDock\Tests\Unit\CenterButtonTest::class,
	\WPCalibrate\MobileDock\Tests\Unit\MigrationManagerTest::class,
	\WPCalibrate\MobileDock\Tests\Integration\NavigationCrudTest::class,
	\WPCalibrate\MobileDock\Tests\Integration\LifecycleAndRetentionTest::class,
	\WPCalibrate\MobileDock\Tests\Integration\MenuAndPermissionTest::class,
	\WPCalibrate\MobileDock\Tests\Integration\WooCommerceIntegrationTest::class,
];

echo "\n=======================================================\n";
echo "  WPCalibrate Mobile Dock - Automated Test Suite\n";
echo "  PHP Version: " . PHP_VERSION . "\n";
echo "=======================================================\n\n";

$total_tests  = 0;
$passed_tests = 0;
$failed_tests = 0;
$errors       = [];

$start_time = microtime( true );

foreach ( $test_classes as $class_name ) {
	$reflection = new \ReflectionClass( $class_name );
	$short_name = $reflection->getShortName();
	echo "[SUITE] " . $short_name . "\n";

	$instance = new $class_name();
	$methods  = $reflection->getMethods( \ReflectionMethod::IS_PUBLIC );

	foreach ( $methods as $method ) {
		if ( ! str_starts_with( $method->getName(), 'test_' ) ) {
			continue;
		}

		$total_tests++;
		$test_name = $method->getName();

		try {
			$method->invoke( $instance );
			$passed_tests++;
			echo "  [PASS] {$test_name}\n";
		} catch ( \Throwable $t ) {
			$failed_tests++;
			$errors[] = [
				'suite'   => $short_name,
				'test'    => $test_name,
				'message' => $t->getMessage(),
				'file'    => $t->getFile(),
				'line'    => $t->getLine(),
			];
			echo "  [FAIL] {$test_name}: " . $t->getMessage() . "\n";
		}
	}
	echo "\n";
}

$elapsed = round( microtime( true ) - $start_time, 4 );

echo "=======================================================\n";
echo "  TEST RESULTS SUMMARY\n";
echo "  Total:  {$total_tests}\n";
echo "  Passed: {$passed_tests}\n";
echo "  Failed: {$failed_tests}\n";
echo "  Duration: {$elapsed}s\n";
echo "=======================================================\n\n";

if ( $failed_tests > 0 ) {
	echo "FAILURES DETAILS:\n";
	foreach ( $errors as $err ) {
		echo "- [{$err['suite']}::{$err['test']}] {$err['message']} at {$err['file']}:{$err['line']}\n";
	}
	exit( 1 );
}

echo "ALL TESTS PASSED SUCCESSFULLY!\n\n";
exit( 0 );
