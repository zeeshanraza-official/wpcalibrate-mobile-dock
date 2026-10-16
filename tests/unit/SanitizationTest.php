<?php
/**
 * Sanitization and Security Unit Tests.
 *
 * @package WPCalibrate\MobileDock\Tests\Unit
 */

namespace WPCalibrate\MobileDock\Tests\Unit;

use WPCalibrate\MobileDock\Domain\NavigationItem;
use WPCalibrate\MobileDock\Navigation\SettingsRegistry;

class SanitizationTest {

	public function test_css_classes_sanitization(): void {
		$malicious_input = 'valid-class-1 another_class "><script>alert(1)</script> bad@class';
		$sanitized = NavigationItem::sanitize_css_classes( $malicious_input );

		assert( ! str_contains( $sanitized, '<script>' ), 'CSS classes must not contain script tags' );
		assert( ! str_contains( $sanitized, '"' ), 'CSS classes must not contain quotes' );
		assert( ! str_contains( $sanitized, '@' ), 'CSS classes must not contain special symbols' );
		assert( str_contains( $sanitized, 'valid-class-1' ), 'Valid class 1 must be preserved' );
		assert( str_contains( $sanitized, 'another_class' ), 'Valid class 2 must be preserved' );
	}

	public function test_custom_css_sanitization(): void {
		$raw_css = '<style>body { background: red; }</style><script>alert("xss")</script>div { width: expression(alert(1)); behavior: url(x.htc); }';
		$clean_css = SettingsRegistry::sanitize_custom_css( $raw_css );

		assert( ! str_contains( $clean_css, '<style>' ), 'Custom CSS must not contain style tags' );
		assert( ! str_contains( $clean_css, '<script>' ), 'Custom CSS must not contain script tags' );
		assert( ! str_contains( $clean_css, 'expression' ), 'Custom CSS must not contain expression keyword' );
		assert( ! str_contains( $clean_css, 'behavior' ), 'Custom CSS must not contain behavior keyword' );
	}

	public function test_item_label_xss_prevention(): void {
		$item = NavigationItem::from_array( [
			'label' => '<img src=x onerror=alert(1)> Shop',
			'target' => 'https://example.com" onmouseover="alert(1)',
		] );

		assert( ! str_contains( $item->label, '<img' ), 'Item label must be stripped of HTML tags' );
		assert( str_contains( $item->label, 'Shop' ), 'Item label text must remain intact' );
	}
}
