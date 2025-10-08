<?php
/**
 * CurrentWeather data  class file.
 * This class is used to hold the forecast data for a given location of current weather from WeatherAPI.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend\Api\WeatherApiData;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ShapedPlugin\Weather\Frontend\Api\Aid\City;
use ShapedPlugin\Weather\Frontend\Api\Aid\Sun;
use ShapedPlugin\Weather\Frontend\Api\Aid\Temperature;
use ShapedPlugin\Weather\Frontend\Api\Aid\Unit;
use ShapedPlugin\Weather\Frontend\Api\Aid\Weather;
use ShapedPlugin\Weather\Frontend\Api\Aid\Wind;
use ShapedPlugin\Weather\Frontend\Api\Aid\IconConverter;

/**
 * Weather class used to hold the current weather data.
 */
class CurrentWeatherData {
	/**
	 * The city object.
	 *
	 * @var Aid\City
	 */
	public $city;

	/**
	 * The temperature object.
	 *
	 * @var Aid\Temperature
	 */
	public $temperature;

	/**
	 * Humidity.
	 *
	 * @var Aid\Humidity
	 */
	public $humidity;

	/**
	 * Gusts.
	 *
	 * @var Aid\gusts
	 */
	public $gusts;

	/**
	 *  Pressure.
	 *
	 * @var Aid\Pressure
	 */
	public $pressure;

	/**
	 * Wind.
	 *
	 * @var Aid\Wind
	 */
	public $wind;

	/**
	 * Cloud.
	 *
	 * @var Aid\Clouds
	 */
	public $clouds;

	/**
	 * Visibility.
	 *
	 * @var Aid\Visibility
	 */
	public $visibility;

	/**
	 *  Precipitation.
	 *
	 * @var Aid\Precipitation
	 */
	public $precipitation;

	/**
	 * Sun.
	 *
	 * @var Aid\Sun
	 */
	public $sun;

	/**
	 * Weather.
	 *
	 * @var Aid\Weather
	 */
	public $weather;

	/**
	 * Rain chance.
	 *
	 * @var Rainchance of the day forecast.
	 */
	public $rainchance;

	/**
	 * Snow.
	 *
	 * @var $snow of the hourly forecast.
	 */
	public $snow;

	/**
	 *  Datetime.
	 *
	 * @var \DateTime
	 */
	public $last_update;

	/**
	 *  Timezone.
	 *
	 * @var \Timezone
	 */
	public $timezone;
	/**
	 * Create a new weather object.
	 *
	 * @param mixed  $data The place to get weather information for.
	 * @param string $units Can be either 'metric' or 'imperial' (default).
	 *
	 * @internal
	 */
	public function __construct( $data, $units ) {
		$utctz = new \DateTimeZone( 'UTC' );

		// Get the timezone from the API key.
		$timezone     = new \DateTimeZone( $data->location->tz_id );
		$location     = timezone_location_get( $timezone );
		$country_code = $location['country_code'] ?? $data->location->country;

		// Convert the temperature, winds, gust to the correct unit.
		if ( 'metric' === $units ) {
			$wind_speed_unit = 'm/s';
			$temp_unit       = 'c';
			$wind            = $data->current->wind_mph * 0.45;
			$gust            = $data->current->gust_mph * 0.45;
		} else {
			$wind_speed_unit = 'mph';
			$temp_unit       = 'f';
			$wind            = $data->current->wind_mph;
			$gust            = $data->current->gust_mph;
		}

		$this->city = new City( $data->location->region, $data->location->name, $data->location->lat, $data->location->lon, $country_code, null, $data->location->tz_id );

		$this->temperature = new Temperature( new Unit( $data->current->{'temp_' . $temp_unit}, '' ), new Unit( $data->forecast->forecastday[0]->day->{'mintemp_' . $temp_unit}, '' ), new Unit( $data->forecast->forecastday[0]->day->{'maxtemp_' . $temp_unit}, '' ) );

		$this->humidity      = new Unit( $data->current->humidity, '% ' );
		$this->gusts         = new Unit( $gust, $wind_speed_unit );
		$this->pressure      = new Unit( $data->current->pressure_mb, 'mb' );
		$this->wind          = new Wind(
			new Unit( $wind, $wind_speed_unit ),
			null !== $data->current->wind_degree ? new Unit( $data->current->wind_degree, $data->current->wind_dir, null ) : null
		);
		$this->clouds        = new Unit( $data->current->cloud, null );
		$this->visibility    = new Unit( ( $data->current->vis_km ) );
		$this->precipitation = $data->current->precip_mm;
		$this->weather       = new Weather( $data->current->condition->icon, $data->current->condition->text, IconConverter::get_owm_icon( $data->current->condition->code, $data->current->is_day ) );

		$location_tz    = new \DateTimeZone( $data->location->tz_id );
		$datetime       = new \DateTime( 'now', $utctz );
		$offset         = $location_tz->getOffset( $datetime );
		$this->timezone = $offset;

		$date              = $data->forecast->forecastday[0]->date;
		$sunrise           = $data->forecast->forecastday[0]->astro->sunrise;
		$sunset            = $data->forecast->forecastday[0]->astro->sunset;
		$sunrise_time      = \DateTime::createFromFormat( 'Y-m-d h:i A', "$date $sunrise", $location_tz );
		$sunset_time       = \DateTime::createFromFormat( 'Y-m-d h:i A', "$date $sunset", $location_tz );
		$this->sun         = new Sun( $sunrise_time, $sunset_time );
		$this->last_update = new \DateTime( $data->current->last_updated, $utctz );
	}
}
