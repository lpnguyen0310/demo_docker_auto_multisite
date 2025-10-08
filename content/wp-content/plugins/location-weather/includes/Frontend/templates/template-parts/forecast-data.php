<?php
/**
 * Weather Forecast data Template
 *
 * This template displays the weather forecast for a specific location.
 *
 * This template can be overridden by copying it to yourtheme/location-weather/templates/template-parts/forecast-data.php
 *
 * @since      2.3.0
 * @version    3.0.0
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( $forecast_data ) :
	$separator = 'three-hour' === $hourly_type ? '/' : '';
	echo '<div class="lw-forecast-wrapper">';
	foreach ( $forecast_data as $data ) :
		$lw_forecast   = self::get_forecast_data( $data, $measurement_units, $time_settings );
		$forecast_icon = apply_filters( 'sp_lwp_weather_icon', LOCATION_WEATHER_URL . '/assets/images/icons/weather-static-icons/' . $lw_forecast['icon'] . '.svg' );
		if ( 'forecast_icon_set_one' === $forecast_icon_type ) {
			$forecast_icon = apply_filters( 'sp_lwp_weather_icon', LOCATION_WEATHER_URL . '/assets/images/icons/weather-icons/' . $lw_forecast['icon'] . '.svg' );
		}

		$max_value_with_separator = $lw_forecast['max'] ? $separator : '';
		$hourly_forecast_time     = '<span class="lw-hourly-forecast-time">' . $lw_forecast['times'] . '</span>';
		?>
<!-- Forecast details html area start -->
<div class="splw-forecast">
	<div class="splw-forecast-time">
		<?php echo wp_kses_post( $hourly_forecast_time ); ?>
	</div>
	<div class="splw-forecast-icons <?php echo esc_attr( $lw_forecast['max'] ? 'max' : '' ); ?>">
		<img decoding="async" src="<?php echo esc_url( $forecast_icon ); ?>" class="splw-weather-icon" alt="temperature icon" width="50" height="50">
	</div>
	<div class="splw-weather-details">
		<span id="temperature" data-tab-content class="temp-min-mex active">
		<?php echo '<span class=lw-low-temp>' . wp_kses_post( $lw_forecast['min'] ) . '</span>' . esc_attr( $separator ) . '<span class=lw-high-temp>' . wp_kses_post( $lw_forecast['max'] ) . '</span>'; ?>
		</span>
		<span id="precipitation" data-tab-content class="temp-precipitation">
		<?php echo esc_html( $lw_forecast['precipitation'] ); ?>
		</span>
		<span id="rainchance" data-tab-content class="temp-rainchance">
		<?php echo esc_html( $lw_forecast['rain'] ); ?>
		</span>
		<span id="wind" data-tab-content class="temp-wind">
		<?php echo wp_kses_post( $lw_forecast['wind'] ); ?>
		</span>
		<span id="humidity" data-tab-content class="temp-humidity">
		<?php echo esc_html( $lw_forecast['humidity'] ); ?>
		</span>
		<span id="pressure" data-tab-content class="temp-pressure">
		<?php echo esc_html( $lw_forecast['pressure'] ); ?>
		</span>
		<span id="snow" data-tab-content class="temp-snow">
		<?php echo esc_html( $lw_forecast['snow'] ); ?>
		</span>
	</div>
</div>
<!-- End forecast details area -->
		<?php
endforeach;
	echo '</div>';
endif;

