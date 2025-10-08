<?php
/**
 * WeatherApi forecast data class file.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend\Api\WeatherApiData;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ShapedPlugin\Weather\Frontend\Api\Aid\Location;

/**
 * OneCallWeather class used to hold the uv index for a given date, time and location.
 */
class ForecastData {

	/**
	 * Location
	 *
	 * @var Location
	 */
	public $location;

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
	 * The TimeZone
	 *
	 * @var time_zone
	 */
	public $time_zone;

	/**
	 * The daily_forecast
	 *
	 * @var array
	 */
	public $daily_forecast;

	/**
	 * An array of {@link hourly_forecast} objects.
	 *
	 * @var array
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
	 * @param int    $hours The number of hours of hourly forecasts to include.
	 * @param int    $hourly_type  the type of hourly forecasts to include.
	 */
	public function __construct( $data, $units, $hours, $hourly_type ) {
		$utctz = new \DateTimeZone( 'UTC' );

		$location_tz    = new \DateTimeZone( $data->location->tz_id );
		$this->location = new Location( $data->location->lat, $data->location->lon );
		$forecast_count = count( $data->forecast->forecastday );
		$is_day         = $data->current->is_day;

		$daily_forecasts = $data->forecast->forecastday;
		// Initialize hourly forecasts.
		$stop           = false;
		$hourly_counter = 0;
		$current_time   = $data->location->localtime_epoch;
		foreach ( $daily_forecasts as $daily_forecast ) {
			$hourly_forecasts = $daily_forecast->hour;

			if ( ! $stop && 'one-hour' === $hourly_type ) {
				foreach ( $hourly_forecasts as $hourly_forecast ) {
					// Skip hours that have already passed.
					if ( $hourly_forecast->time_epoch <= $current_time ) {
						continue;
					}

					$hourly_forecast         = new Hourly( $hourly_forecast, $units, $location_tz, $hourly_type, $is_day );
					$this->hourly_forecast[] = $hourly_forecast;

					++$hourly_counter;

					// Stop once the requested number of future hours is reached.
					if ( $hourly_counter === $hours ) {
						$stop = true;
						break;
					}
				}
			}
			if ( ! $stop && 'three-hour' === $hourly_type ) {
				$group = array();
				foreach ( $hourly_forecasts as $hourly_forecast ) {
					// Skip past hours.
					if ( $hourly_forecast->time_epoch < $current_time ) {
						continue;
					}

					$group[] = $hourly_forecast;

					// Process every 3-hour group.
					if ( count( $group ) === 3 ) {
						$avg_data                = $this->average_hourly_group( $group );
						$hourly_forecast_obj     = new Hourly( (object) $avg_data, $units, $location_tz, $hourly_type, $is_day );
						$this->hourly_forecast[] = $hourly_forecast_obj;

						++$hourly_counter;

						$group = array();

						if ( $hourly_counter === $hours ) {
							$stop = true;
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * Three hourly average data groups.
	 *
	 * @param  mixed $group The group of hourly data.
	 * @return array The average data for the group.
	 */
	private function average_hourly_group( $group ) {
		$count = count( $group );

		if ( 0 === $count ) {
			return array();
		}

		$first = $group[0];

		$avg = array(
			'time_epoch'     => intval( round( array_sum( array_column( $group, 'time_epoch' ) ) / $count ) ),
			'time'           => $first->time,
			'temp_c'         => 0,
			'temp_f'         => 0,
			'min_temp_c'     => $group[0]->temp_c,
			'min_temp_f'     => $group[0]->temp_f,
			'max_temp_c'     => $group[0]->temp_c,
			'max_temp_f'     => $group[0]->temp_f,
			'wind_mph'       => 0,
			'wind_degree'    => 0,
			'pressure_mb'    => 0,
			'precip_mm'      => 0,
			'snow_cm'        => 0,
			'humidity'       => 0,
			'cloud'          => 0,
			'chance_of_rain' => 0,
			'gust_mph'       => 0,
			'condition'      => (object) array(
				'text' => $group[1]->condition->text,
				'icon' => $group[1]->condition->icon,
				'code' => $group[1]->condition->code,
			),
		);

		// Accumulate numeric values.
		foreach ( $group as $item ) {
			$avg['temp_c']         += $item->temp_c;
			$avg['temp_f']         += $item->temp_f;
			$avg['wind_mph']       += $item->wind_mph;
			$avg['wind_degree']    += $item->wind_degree;
			$avg['pressure_mb']    += $item->pressure_mb;
			$avg['precip_mm']      += $item->precip_mm;
			$avg['snow_cm']        += $item->snow_cm;
			$avg['humidity']       += $item->humidity;
			$avg['cloud']          += $item->cloud;
			$avg['chance_of_rain'] += $item->chance_of_rain;
			$avg['gust_mph']       += $item->gust_mph;

			// Calculate min/max.
			if ( $item->temp_c < $avg['min_temp_c'] ) {
				$avg['min_temp_c'] = $item->temp_c;
			}
			if ( $item->temp_c > $avg['max_temp_c'] ) {
				$avg['max_temp_c'] = $item->temp_c;
			}
			if ( $item->temp_f < $avg['min_temp_f'] ) {
				$avg['min_temp_f'] = $item->temp_f;
			}
			if ( $item->temp_f > $avg['max_temp_f'] ) {
				$avg['max_temp_f'] = $item->temp_f;
			}
		}

		// Divide for final averages.
		foreach ( $avg as $key => $value ) {
			if ( is_numeric( $value ) && ! in_array( $key, array( 'min_temp_c', 'max_temp_c', 'min_temp_f', 'max_temp_f' ), true ) ) {
				$avg[ $key ] = round( $value / $count, 2 );
			}
		}

		return $avg;
	}
}
