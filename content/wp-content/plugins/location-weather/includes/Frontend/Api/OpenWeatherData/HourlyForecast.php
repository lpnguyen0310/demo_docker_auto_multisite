<?php
/**
 * HourlyForecast class file.
 * This file contains the HourlyForecast class, which is used to represent the weather forecast for every 3 hours from Three hourly API.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend\Api\OpenWeatherData;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ShapedPlugin\Weather\Frontend\Api\Aid\Temperature;
use ShapedPlugin\Weather\Frontend\Api\Aid\Unit;
use ShapedPlugin\Weather\Frontend\Api\Aid\Weather;
use ShapedPlugin\Weather\Frontend\Api\Aid\Wind;
use ShapedPlugin\Weather\Frontend\Api\Aid\Time;

/**
 * Class Forecast.
 */
class HourlyForecast {
	/**
	 * Temperature.
	 *
	 * @var Temperature of the forecast.
	 */
	public $temperature;
	/**
	 * Humidity.
	 *
	 * @var Humidity of the hourly forecast.
	 */
	public $humidity;

	/**
	 * Wind.
	 *
	 * @var Wind of the hourly forecast.
	 */
	public $wind;

	/**
	 * Pressure.
	 *
	 * @var Pressure of the hourly forecast.
	 */
	public $pressure;

	/**
	 * Precipitation.
	 *
	 * @var Precipitation of the hourly forecast.
	 */
	public $precipitation;

	/**
	 * Rain chance.
	 *
	 * @var Rainchance of the hourly forecast.
	 */
	public $rainchance;

	/**
	 * Weather.
	 *
	 * @var weather of the hourly forecast.
	 */
	public $weather;

	/**
	 * Datetime.
	 *
	 * @var Time The time of the forecast.
	 */
	public $time;

	/**
	 * Snow.
	 *
	 * @var $snow of the hourly forecast.
	 * @param int $snow of the hourly forecast
	 */
	public $snow;

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
	 *  Datetime.
	 *
	 * @var \DateTime
	 */
	public $last_update;
	/**
	 * Create a new weather object for forecasts.
	 *
	 * @param object $data The forecasts data.
	 * @param string $units Ths units used.
	 * @param string $api_type Api type.
	 *
	 * @internal
	 */
	public function __construct( $data, $units, $api_type = '' ) {
		$utctz = new \DateTimeZone( 'UTC' );
		if ( 'metric' === $units ) {
			$temperature_unit = '°C';
		} else {
			$temperature_unit = '°F';
		}

		$this->temperature   = new Temperature(
			new Unit( $data->main->temp, $temperature_unit ),
			new Unit( $data->main->temp_min, $temperature_unit ),
			new Unit( $data->main->temp_max, $temperature_unit )
		);
		$this->humidity      = new Unit( $data->main->humidity );
		$this->pressure      = new Unit( $data->main->pressure, 'hPa' );
		$this->wind          = new Wind(
			new Unit( $data->wind->speed, 'm/s' ),
			property_exists( $data->wind, 'deg' ) && null !== $data->wind->deg ? new Unit( $data->wind->deg ) : null
		);
		$this->gusts         = new Unit( $data->wind->gust, 'm/s' );
		$this->clouds        = new Unit( $data->clouds->all, '%' );
		$hour_check          = 'premium_call' === $api_type ? '1h' : '3h';
		$this->rainchance    = $data->pop * 100 . '%';
		$this->snow          = ! empty( $data->snow->{$hour_check } ) ? round( (float) $data->snow->{$hour_check }, 2 ) . ' mm/h' : '0 mm/h';
		$this->precipitation = $data->pop;
		$this->weather       = new Weather( $data->weather[0]->id, $data->weather[0]->description, $data->weather[0]->icon );
		$this->last_update   = \DateTime::createFromFormat( 'U', $data->dt, $utctz );
	}
}
