<?php
/**
 * Appearance Settings Unit Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Unit
 */

namespace WPCalibrate\MobileDock\Tests\Unit;

use WPCalibrate\MobileDock\Domain\AppearanceSettings;

class AppearanceSettingsTest {

	public function test_color_sanitization(): void {
		assert( AppearanceSettings::sanitize_color( '#ffffff' ) === '#ffffff', 'Valid 6-digit hex must pass' );
		assert( AppearanceSettings::sanitize_color( '#fff' ) === '#fff', 'Valid 3-digit hex must pass' );
		assert( AppearanceSettings::sanitize_color( 'rgba(0, 0, 0, 0.5)' ) === 'rgba(0, 0, 0, 0.5)', 'Valid rgba must pass' );
		assert( AppearanceSettings::sanitize_color( 'transparent' ) === 'transparent', 'transparent keyword must pass' );

		// Malicious or invalid colors
		assert( AppearanceSettings::sanitize_color( 'red; alert(1);' ) === '#ffffff', 'Invalid syntax must fallback' );
		assert( AppearanceSettings::sanitize_color( 'javascript:evil' ) === '#ffffff', 'Malicious protocol must fallback' );
	}

	public function test_dimension_sanitization(): void {
		assert( AppearanceSettings::sanitize_dimension( '60px' ) === '60px', 'Valid px dimension must pass' );
		assert( AppearanceSettings::sanitize_dimension( '1.5rem' ) === '1.5rem', 'Valid rem dimension must pass' );
		assert( AppearanceSettings::sanitize_dimension( '0' ) === '0px', 'Zero must normalize to 0px' );
		assert( AppearanceSettings::sanitize_dimension( '100%22' ) === '0px', 'Malformed dimension must fallback' );
	}

	public function test_css_variables_output(): void {
		$appearance = new AppearanceSettings(
			bg_color: '#123456',
			height: '65px'
		);

		$vars = $appearance->to_css_variables();
		assert( isset( $vars['--wpcal-dock-bg'] ), 'Must contain --wpcal-dock-bg' );
		assert( $vars['--wpcal-dock-bg'] === '#123456', 'Background color must match' );
		assert( $vars['--wpcal-dock-height'] === '65px', 'Height must match' );
	}
}
