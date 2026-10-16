<?php
/**
 * Icon Registry Unit Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Unit
 */

namespace WPCalibrate\MobileDock\Tests\Unit;

use WPCalibrate\MobileDock\Icons\IconRegistry;

class IconRegistryTest {

	public function test_approved_icons_allowlist(): void {
		$icons = IconRegistry::get_icons();
		assert( count( $icons ) >= 20, 'At least 20 curated icons must be registered' );
		assert( IconRegistry::is_valid( 'home' ), 'home icon must be valid' );
		assert( IconRegistry::is_valid( 'cart' ), 'cart icon must be valid' );
		assert( IconRegistry::is_valid( 'search' ), 'search icon must be valid' );
		assert( ! IconRegistry::is_valid( 'malicious_icon' ), 'Unregistered icon must be invalid' );
		assert( ! IconRegistry::is_valid( '<script>alert(1)</script>' ), 'XSS payload must be invalid' );
	}

	public function test_svg_rendering_safety(): void {
		$home_svg = IconRegistry::get_svg( 'home', [ 'class' => 'test-icon' ] );
		assert( str_starts_with( $home_svg, '<svg' ), 'Must return SVG element' );
		assert( str_contains( $home_svg, 'class="test-icon"' ), 'Must inject clean custom attributes' );

		// Fallback on invalid icon
		$fallback_svg = IconRegistry::get_svg( 'non_existent_icon' );
		assert( str_starts_with( $fallback_svg, '<svg' ), 'Must fallback to home SVG safely' );
	}
}
