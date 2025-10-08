<?php
/**
 * Weather API hourly forecast class file.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend\Api\WeatherApiData;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ShapedPlugin\Weather\Frontend\Api\Aid\Temperature;
use ShapedPlugin\Weather\Frontend\Api\Aid\Unit;
use ShapedPlugin\Weather\Frontend\Api\Aid\Weather;
use ShapedPlugin\Weather\Frontend\Api\Aid\Wind;
use ShapedPlugin\Weather\Frontend\Api\Aid\Time;
use ShapedPlugin\Weather\Frontend\Api\Aid\IconConverter;


/**
 * Class Forecast.
 */
class Hourly {

	/**
	 * Temperature.
	 *
	 * @var Temperature of the forecast.
	 */
	public $temperature;

	/**
	 * Feel_like.
	 *
	 * @var feel_like of the forecast.
	 */
	public $feel_like;

	/**
	 * Humidity.
	 *
	 * @var Humidity of the day forecast.
	 */
	public $humidity;

	/**
	 * Wind.
	 *
	 * @var Wind of the day forecast.
	 */
	public $wind;

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
	 * @var dew_point
	 */
	public $dew_point;

	/**
	 * Pressure.
	 *
	 * @var Pressure of the day forecast.
	 */
	public $pressure;

	/**
	 * Precipitation.
	 *
	 * @var Precipitation of the day forecast.
	 */
	public $precipitation;

	/**
	 * Rain chance.
	 *
	 * @var Rainchance of the day forecast.
	 */
	public $rainchance;

	/**
	 * Weather.
	 *
	 * @var weather of the day forecast.
	 */
	public $weather;

	/**
	 * Snow.
	 *
	 * @var Snow of the day forecast.
	 */
	public $snow;

	/**
	 * Timezone.
	 *
	 * @var Timezone The time of the forecast.
	 */
	public $timezone;

	/**
	 * Sunrise.
	 *
	 * @var Sunrise The time of the forecast.
	 */
	public $sunrise;

	/**
	 * Sunset.
	 *
	 * @var Sunset The time of the forecast.
	 */
	public $sunset;

	/**
	 * Datetime.
	 *
	 * @var Time The time of the forecast.
	 */
	public $time;

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
	 * @param string $units The units used.
	 * @param mixed  $location_tz The location time.
	 * @param string $hourly_type The forecast hourly type.
	 * @param mixed  $is_day check day mode.
	 *
	 * @internal
	 */
	public function __construct( $data, $units, $location_tz, $hourly_type, $is_day ) {
		$utctz = new \DateTimeZone( 'UTC' );

		// Convert the temperature, winds, gust to the correct unit.
		if ( 'metric' === $units ) {
			$temperature_unit = '°C';
			$wind_speed_unit  = 'm/s';
			$temp_unit        = 'c';
			$wind             = $data->wind_mph * 0.45;
			$gust             = $data->gust_mph * 0.45;
		} else {
			$temperature_unit = '°F';
			$wind_speed_unit  = 'mph';
			$temp_unit        = 'f';
			$wind             = $data->wind_mph;
			$gust             = $data->gust_mph;
		}

		if ( 'three-hour' === $hourly_type ) {
			$this->temperature = new Temperature(
				new Unit( $data->{'temp_' . $temp_unit}, $temperature_unit ),
				new Unit( $data->{'min_temp_' . $temp_unit}, $temperature_unit ),
				new Unit( $data->{'max_temp_' . $temp_unit}, $temperature_unit )
			);
		} else {
			// For hourly forecast.
			$this->temperature = new Unit( $data->{'temp_' . $temp_unit}, $temperature_unit );
		}

		$this->humidity      = new Unit( round( $data->humidity ) );
		$this->pressure      = new Unit( $data->pressure_mb, 'mb' );
		$this->wind          = new Wind(
			new Unit( $wind, $wind_speed_unit ),
			null !== $data->wind_degree ? new Unit( $data->wind_degree ) : null
		);
		$this->gusts         = new Unit( $gust, $wind_speed_unit );
		$this->clouds        = new Unit( round( $data->cloud ), '%' );
		$this->precipitation = $data->precip_mm;
		$this->rainchance    = ! empty( $data->chance_of_rain ) ? round( $data->chance_of_rain ) . '%' : '0%';
		$this->snow          = ! empty( $data->snow_cm ) ? round( (float) $data->snow_cm, 2 ) . ' cm' : '0 cm';
		$this->weather       = new Weather( $data->condition->icon, $data->condition->text, IconConverter::get_owm_icon( $data->condition->code, $is_day ) );
		$this->last_update   = \DateTime::createFromFormat( 'Y-m-d H:i', $data->time, $location_tz );
	}
}
