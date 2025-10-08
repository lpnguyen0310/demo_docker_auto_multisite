<?php
/**
 * Weather Current Temperature Template
 *
 * This template displays the daily weather current temperature for a specific location.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$weather_icon = apply_filters( 'sp_lwp_weather_icon', LOCATION_WEATHER_URL . '/assets/images/icons/weather-static-icons/' . $weather_data['icon'] . '.svg' );
if ( 'forecast_icon_set_one' === $lw_current_icon_type ) {
	$weather_icon = apply_filters( 'sp_lwp_weather_icon', LOCATION_WEATHER_URL . '/assets/images/icons/weather-icons/' . $weather_data['icon'] . '.svg' );
}
$weather_icon_size = $splw_meta['lw_current_icon_size'] ?? '58';

?>
<?php if ( $show_icon || $show_temperature ) : ?>
<div class="splw-lite-current-temp">
	<div class="splw-cur-temp">
		<!-- weather icon html area start -->
		<?php if ( $show_icon ) : ?>
			<img decoding="async" src="<?php echo esc_url( $weather_icon ); ?>" class="splw-weather-icon" alt="temperature icon" width="<?php echo esc_attr( $weather_icon_size ); ?>" height="<?php echo esc_attr( $weather_icon_size ); ?>">
		<?php endif; ?><!-- weather icon html area end -->
		<!-- weather current temperature html area start -->
		<?php if ( $show_temperature ) : ?>
			<span class="cur-temp"> 
				<?php echo $weather_data['temp']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output safely escaped ?>
			</span>
		<?php endif; ?><!-- temperature html area end -->
	</div>
</div>
<?php endif; ?>
<?php if ( $short_description ) : ?>
	<div class="splw-lite-desc">
		<?php echo esc_html( $weather_data['desc'] ); ?>
	</div>
<?php endif; ?>
