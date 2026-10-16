<?php
/**
 * License Provider Interface.
 *
 * Defines the contract for license management implementations.
 *
 * @package WPCalibrate\MobileDock\Licensing
 */

namespace WPCalibrate\MobileDock\Licensing;

defined( 'ABSPATH' ) || exit;

interface LicenseProviderInterface {

	/**
	 * Check if a real licensing provider is configured and available.
	 *
	 * @return bool
	 */
	public function is_configured(): bool;

	/**
	 * Get the current license status ('valid', 'invalid', 'expired', 'unconfigured').
	 *
	 * @return string
	 */
	public function get_status(): string;

	/**
	 * Get the stored license key (masked).
	 *
	 * @return string
	 */
	public function get_masked_key(): string;

	/**
	 * Get license expiration date if available.
	 *
	 * @return string|null
	 */
	public function get_expiration(): ?string;

	/**
	 * Get renewal link URL.
	 *
	 * @return string
	 */
	public function get_renewal_url(): string;

	/**
	 * Attempt to activate a license key.
	 *
	 * @param string $key
	 * @return array{success: bool, message: string}
	 */
	public function activate( string $key ): array;

	/**
	 * Attempt to deactivate the active license.
	 *
	 * @return array{success: bool, message: string}
	 */
	public function deactivate(): array;
}
