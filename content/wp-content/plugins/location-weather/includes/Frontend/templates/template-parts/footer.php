<?php
/**
 * Weather Footer Template File
 *
 * This template displays the weather footer for a specific location.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$lw_openweather_map_link = $splw_meta['lw-openweather-links'] ?? false;
$url                     = 'weather_api' === $api_source ? 'https://www.weatherapi.com/' : 'https://openweathermap.org/';
$details_url             = 'weather_api' === $api_source ? 'https://www.weatherapi.com/weather/q/' . sanitize_title( $weather_data['city'] ) : 'https://openweathermap.org/city/' . $weather_data['city_id'];
?>
<!-- weather detailed and updated html area start -->
<?php if ( $show_weather_detailed || $show_weather_updated_time || $show_weather_attr ) : ?>
	<div class="lw-footer">
	<?php if ( $show_weather_detailed || $show_weather_updated_time ) : ?>
	<div class="splw-weather-detailed-updated-time">
		<?php if ( $show_weather_detailed ) : ?>
		<div class='splw-weather-detailed'>
			<a href="<?php echo esc_url( $details_url ); ?>" target="_blank">
				<?php echo esc_html( __( 'Detailed weather', 'location-weather' ) ); ?>
			</a>
		</div>
		<?php endif ?>
		<?php if ( $show_weather_updated_time ) : ?>
		<div class='splw-weather-updated-time'>
				<?php echo esc_html( __( 'Last updated:', 'location-weather' ) ); ?>
				<?php echo esc_html( $weather_data['updated_time'] ); ?>
		</div>
		<?php endif ?>
	</div>
<?php endif; ?><!-- weather detailed and updated html area end -->
<!-- weather attribute html area start -->
	<?php if ( $show_weather_attr && $appid ) : ?>
	<div class="splw-weather-attribution">
		<?php if ( $lw_openweather_map_link ) : ?>
		<a href="<?php echo esc_url( $url ); ?>" target="_blank">
			<?php endif ?>
			<?php
			$source = 'weather_api' === $api_source ? __( 'WeatherAPI', 'location-weather' ) : __( 'OpenWeatherMap', 'location-weather' );
			/* translators: %s: property modify source. */
			printf( esc_html__( 'Weather from %s', 'location-weather' ), esc_html( $source ) );
			?>
		<?php if ( $lw_openweather_map_link ) : ?>
		</a>
		<?php endif ?>
	</div>
<?php endif; ?><!-- weather attribute html area end -->
</div>
<?php endif; ?><!-- weather attribute html area end -->
