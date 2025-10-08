<?php
/**
 * HourlyForecastWeather class file.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend\Api\OpenWeatherData;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ShapedPlugin\Weather\Frontend\Api\Aid\Location;

/**
 * HourlyForecastWeather class used to hold the uv index for a given date, time and location.
 */
class ForecastWeather {

	/**
	 * Location
	 *
	 * @var Location
	 */
	public $location;

	/**
	 * Wind gust.
	 *
	 * @var gusts of the day forecast.
	 */
	public $gusts;

	/**
	 * Clouds.
	 *
	 * @var clouds of the day forecast.
	 */
	public $clouds;

	/**
	 * The uv_index
	 *
	 * @var uv_index
	 */
	public $uv_index;

	/**
	 * The dew_point
	 *
	 * @var uv_index
	 */
	public $dew_point;

	/**
	 * The TimeZone
	 *
	 * @var time_zone
	 */
	public $time_zone;

	/**
	 * The Alerts
	 *
	 * @var alerts
	 */
	public $alerts;

	/**
	 * The daily_forecast
	 *
	 * @var daily_forecast
	 */
	public $daily_forecasts;

	/**
	 * An array of {@link hourly_forecast} objects.
	 *
	 * @var HourlyForecast[]
	 *
	 * @see Forecast The Forecast class.
	 */
	public $hourly_forecast;

	/**
	 * WeatherData constructor.
	 *
	 * Initializes the WeatherData object with weather information.
	 *
	 * @param object $data The raw weather data retrieved from the API.
	 * @param string $units The units for temperature and other measurements (e.g., 'metric', 'imperial').
	 * @param int    $hours The number of hours forecasts to include.
	 * @param int    $days The number of days forecasts to include.
	 * @param string $forecast_type forecasts type.
	 * @param string $lw_hourly_type forecasts hourly type.
	 */
	public function __construct( $data, $units, $hours, $days, $forecast_type, $lw_hourly_type ) {
		$utctz            = new \DateTimeZone( 'UTC' );
		$lat              = ! empty( $data->lat ) ? $data->lat : '';
		$lon              = ! empty( $data->lon ) ? $data->lon : '';
		$this->location   = new Location( $lat, $lon );
		$this->uv_index   = ! empty( $data->current->uvi ) ? (float) $data->current->uvi : '';
		$time_zone        = $data->city->timezone;
		$hourly_forecasts = $data->list;
		$hourly_counter   = 0;
		if ( $hourly_forecasts && 'three-hour' === $lw_hourly_type && 'daily' !== $forecast_type ) {
			foreach ( $hourly_forecasts as $hourly_forecast ) {
				$hourly_forecast         = new HourlyForecast( $hourly_forecast, $units );
				$this->hourly_forecast[] = $hourly_forecast;

				++$hourly_counter;
				// Make sure to only return the requested number of hours .
				if ( $hourly_counter === $hours ) {
					break;
				}
			}
		}
	}
}
