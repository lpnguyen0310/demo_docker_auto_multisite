<?php
/**
 * Preloader.
 *
 * This template can be overridden by copying it to yourtheme/Location-Weather-Pro/templates/preloader.php
 *
 * @package    Location_Weather
 * @subpackage Location_Weather/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$preloader       = isset( $splw_meta['lw-preloader'] ) ? $splw_meta['lw-preloader'] : true;
$preloader_class = $preloader ? 'lw-preloader-wrapper' : '';
if ( $preloader && ! is_admin() && ! $is_admin ) {
	$preloader_image = LOCATION_WEATHER_URL . '/assets/images/spinner.svg';
	if ( ! empty( $preloader_image ) ) {
		?>
		<div id="lw-preloader-<?php echo esc_attr( $shortcode_id ); ?>" class="lw-preloader">
			<img src="<?php echo esc_url( $preloader_image ); ?>" class="skip-lazy"  alt="loader-image" width="50" height="50"/>
		</div>
		<?php
	}
}
