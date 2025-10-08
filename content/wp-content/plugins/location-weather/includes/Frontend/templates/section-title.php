<?php
/**
 * Weather Section Title Template
 *
 * This template displays the weather section title for a specific location.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<!-- section title html area start -->
<?php if ( $show_weather_title ) : ?>
	<div class="splw-weather-title">
		<?php echo esc_html( get_the_title( $shortcode_id ) ); ?>
	</div>
<?php endif; ?><!-- section area end -->
