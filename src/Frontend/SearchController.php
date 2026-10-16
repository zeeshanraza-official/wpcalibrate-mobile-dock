<?php
/**
 * Search Modal Controller.
 *
 * Renders an accessible, lightweight search overlay.
 *
 * @package WPCalibrate\MobileDock\Frontend
 */

namespace WPCalibrate\MobileDock\Frontend;

use WPCalibrate\MobileDock\Icons\IconRegistry;

defined( 'ABSPATH' ) || exit;

class SearchController {

	/**
	 * Render the accessible search modal markup.
	 *
	 * @return string HTML markup
	 */
	public function render_modal(): string {
		$search_icon = IconRegistry::get_svg( 'search', [ 'class' => 'wpcal-icon wpcal-search-submit-icon', 'aria-hidden' => 'true' ] );

		ob_start();
		?>
		<div id="wpcal-search-modal" class="wpcal-search-modal" role="dialog" aria-modal="true" aria-labelledby="wpcal-search-title" hidden>
			<div class="wpcal-search-modal__backdrop" data-wpcal-close-search tabindex="-1"></div>
			<div class="wpcal-search-modal__dialog" role="document">
				<h2 id="wpcal-search-title" class="screen-reader-text"><?php esc_html_e( 'Search website', 'wpcalibrate-mobile-dock' ); ?></h2>
				<form role="search" method="get" class="wpcal-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label for="wpcal-search-input" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'wpcalibrate-mobile-dock' ); ?></label>
					<div class="wpcal-search-input-wrap">
						<input
							type="search"
							id="wpcal-search-input"
							class="wpcal-search-input"
							name="s"
							value="<?php echo esc_attr( get_search_query() ); ?>"
							placeholder="<?php esc_attr_e( 'Search...', 'wpcalibrate-mobile-dock' ); ?>"
							required
							autocomplete="off"
						/>
						<button type="submit" class="wpcal-search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'wpcalibrate-mobile-dock' ); ?>">
							<?php echo $search_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
						<button type="button" class="wpcal-search-close" data-wpcal-close-search aria-label="<?php esc_attr_e( 'Close search', 'wpcalibrate-mobile-dock' ); ?>">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				</form>
			</div>
		</div>
		<?php
		return trim( ob_get_clean() );
	}
}
