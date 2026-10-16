<?php
/**
 * Unconfigured / Null License Provider.
 *
 * Safe default implementation when no external licensing provider is configured.
 * Does not simulate network validation or activation.
 *
 * @package WPCalibrate\MobileDock\Licensing
 */

namespace WPCalibrate\MobileDock\Licensing;

defined( 'ABSPATH' ) || exit;

class UnconfiguredLicenseProvider implements LicenseProviderInterface {

	public function is_configured(): bool {
		return false;
	}

	public function get_status(): string {
		return 'unconfigured';
	}

	public function get_masked_key(): string {
		return '';
	}

	public function get_expiration(): ?string {
		return null;
	}

	public function get_renewal_url(): string {
		return 'https://wpcalibrate.com';
	}

	public function activate( string $key ): array {
		return [
			'success' => false,
			'message' => __( 'Licensing integration has not been configured for this build.', 'wpcalibrate-mobile-dock' ),
		];
	}

	public function deactivate(): array {
		return [
			'success' => false,
			'message' => __( 'Licensing integration has not been configured for this build.', 'wpcalibrate-mobile-dock' ),
		];
	}
}
