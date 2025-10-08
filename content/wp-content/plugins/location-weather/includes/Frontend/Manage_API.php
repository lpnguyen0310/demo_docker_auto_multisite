<?php
/**
 * The Location Weather API Manage class file.
 *
 * @since      3.0.0
 * @version    3.0.0
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ShapedPlugin\Weather\Frontend\Api\OpenWeatherData\CurrentWeather;
use ShapedPlugin\Weather\Frontend\Api\WeatherApiData\CurrentWeatherData;
use ShapedPlugin\Weather\Frontend\Api\OpenWeatherData\ForecastWeather;
use ShapedPlugin\Weather\Frontend\Api\WeatherApiData\ForecastData;
use ShapedPlugin\Weather\Frontend\Api\OpenWeatherData\Exception as LWException;

/**
 * The Manage API class to manage all public facing stuffs.
 *
 * @since 2.1.0
 */
class Manage_API {

	/**
	 * The basic api URL.
	 *
	 * @var string The basic api url to fetch weather data from.
	 */
	private static $open_weather_api = 'https://api.openweathermap.org/data/2.5/weather?';

	/**
	 * The basic api URL for the three hourly forecast.
	 *
	 * @var string The basic api url to fetch weather hourly forecast data from.
	 */
	private static $hourly_forecast_api = 'https://api.openweathermap.org/data/2.5/forecast?';

	/**
	 * The api URL to fetch weather API data.
	 *
	 * @var string The basic api url to fetch current weather data and forecast data.
	 */
	private static $weather_api = 'https://api.weatherapi.com/v1/forecast.json?';

	/**
	 * The api key.
	 *
	 * @var string
	 */
	private static $api_key = '';

	/**
	 * Get current weather data for a location.
	 *
	 * @param string $query        The location or query for weather data.
	 * @param string $units        The units for temperature and other weather data (e.g., 'metric' or 'imperial').
	 * @param string $lang         The language for weather data responses (e.g., 'en' for English).
	 * @param string $appid        The API key for authentication (optional).
	 * @param string $shortcode_id The shortcode ID (optional).
	 *
	 * @return CurrentWeather|array An instance of CurrentWeather containing weather data, or an error message as an array.
	 */
	public static function get_weather( $query, $units = 'imperial', $lang = 'en', $appid = '', $shortcode_id = '' ) {
		$answer    = self::get_raw_weather_data( $query, $units, $lang, $appid, 'xml', $shortcode_id );
		$value     = self::parse_xml( $answer );
		$arr_value = (array) $value;
		if ( isset( $value['cod'] ) && 401 === $value['cod'] ) {
			$value = array(
				'code'    => 401,
				'message' => 'Your API key is not activated yet. Remember that newly created API keys will need ~ 15 minutes to be activated and show data, so you might see an API error in the meantime. <br/>Or<br/> Invalid API key. Please see <a href="http://openweathermap.org/faq#error401" target="_blank">http://openweathermap.org/faq#error401</a> for more info.',
			);
			return $value;
		} elseif ( isset( $arr_value['message'] ) && 'city not found' === $arr_value['message'] ) {
			$value = array(
				'code'    => 404,
				'message' => esc_html__( 'Please set your valid city name and country code.', 'location-weather' ),
			);
			return $value;
		}
		return new CurrentWeather( $value, $units );
	}

	/**
	 * Returns the current weather for a group of city ids.
	 *
	 * @param array|int|string $query The place to get weather information for. For possible values see ::getWeather.
	 * @param string           $units Can be either 'metric' or 'imperial' (default). This affects almost all units returned.
	 * @param string           $lang  The language to use for descriptions, default is 'en'. For possible values see http://openweathermap.org/current#multi.
	 * @param string           $appid Your app id, default ''. See http://openweathermap.org/appid for more details.
	 * @param string           $mode  The format of the data fetched. Possible values are 'json', 'html' and 'xml' (default).
	 * @param string           $shortcode_id get the existing shortcode id from the page.
	 * @return CurrentWeather
	 *
	 * @api
	 */
	public static function get_raw_weather_data( $query, $units = 'imperial', $lang = 'en', $appid = '', $mode = 'xml', $shortcode_id = '' ) {
		$transient_name = 'sp_open_weather_data' . $shortcode_id;
		$weather_data   = self::splw_get_transient( $transient_name );
		// Check if the transient exists and has not expired.
		if ( false === $weather_data ) {
			$url      = self::build_url( $query, $units, $lang, $appid, $mode, self::$open_weather_api );
			$response = wp_remote_get( $url );
			$data     = wp_remote_retrieve_body( $response );
			// Save the data in the transient.
			if ( $data && strpos( $data, '"cod":401' ) === false ) {
				self::splw_set_transient( $transient_name, $data );
			}
		} else {
			$data = $weather_data;
		}
		return $data;
	}

	/**
	 * Fetches the current weather data from the weather API.
	 *
	 * Builds a request URL based on provided parameters and retrieves the data
	 * using `wp_remote_get`. The API response includes current weather data
	 * and air quality index (AQI) for the specified location.
	 *
	 * Optionally supports caching through WordPress transients.
	 *
	 * @param string   $query         Location query (city name, lat/lon, or other supported formats).
	 * @param string   $units         Unit system to use. Accepts 'metric', 'imperial', or 'standard'. Default 'metric'.
	 * @param string   $lang          Language code for localized descriptions. Default 'en'.
	 * @param string   $appid         API key for authentication.
	 * @param int|null $shortcode_id  Optional shortcode ID used for caching purposes.
	 *
	 * @return CurrentWeather|false      Returns a `CurrentWeather` object on success, or false on failure.
	 */
	public static function weather_api_data( $query, $units = 'metric', $lang = 'en', $appid = '', $shortcode_id = null, $hours = null, $hourly_type = 'one-hour' ) {
		$answer = self::weather_api_build_url( $query, $units, $lang, $appid, self::$weather_api ) . '&days=2';

		// Optionally use transient caching (commented out).
		$transient_name = 'sp_weather_api_data_' . $shortcode_id;
		$weather_data   = self::splw_get_transient( $transient_name );
		if ( ! $weather_data ) {

			$request = wp_remote_get( $answer );
			if ( is_wp_error( $request ) ) {
				return false;
			}

			$body = wp_remote_retrieve_body( $request );
			$data = json_decode( $body );

			// Optionally save to cache (commented out).
			if ( $data ) {
				self::splw_set_transient( $transient_name, $data );
			}
		} else {
			$data = $weather_data;
		}

		if ( isset( $data->error->code ) && (int) 2006 === $data->error->code ) {
			return array(
				'code'    => 2006,
				'message' => sprintf(
					// translators: 1: start link, 2: close link.
					__( 'Your WeatherAPI key is invalid. Get your API key from %1$shere%2$s.', 'location-weather' ),
					'<a href="https://www.weatherapi.com/my/" target="_blank" rel="noopener noreferrer">',
					'</a>'
				),
			);
		}
		if ( isset( $data->error->code ) && (int) 1006 === $data->error->code ) {
			return array(
				'code'    => 1006,
				'message' => __(
					'Search by city name, ID, ZIP code, or coordinates.',
					'location-weather'
				),
			);
		}
		// Check if weather is day.
		$current_weather_data  = new CurrentWeatherData( $data, $units );
		$forecast_weather_data = new ForecastData( $data, $units, $hours, $hourly_type );

		return array(
			'current'  => $current_weather_data,
			'forecast' => $forecast_weather_data,
		);
	}

	/**
	 * Fetches hourly weather forecast data for a specified location.
	 *
	 * @param string   $query               Location identifier (e.g., city name).
	 * @param string   $units               Measurement units: 'metric', 'imperial', or 'standard' (default: 'metric').
	 * @param string   $lang                Language for weather data (default: 'en').
	 * @param string   $appid               Optional API key.
	 * @param int|null $shortcode_id        Optional shortcode or component ID.
	 * @param array    $forecast_settings Settings: 'type' (daily/hourly/both), 'days' (int), 'hours' (int), 'hourly_type'.
	 * @return CustomForecastWeather|false  Weather data or false on failure.
	 */
	public static function get_weather_hourly_forecast_data( $query, $units = 'metric', $lang = 'en', $appid = '', $shortcode_id = null, $forecast_settings = array() ) {
		$answer         = self::get_weather_hourly_forecast_url( $query, $units, $lang, $appid, 'json' );
		$transient_name = 'sp_open_weather_hourly_forecast_data' . $shortcode_id;
		$weather_data   = self::splw_get_transient( $transient_name );
		// Check if the transient exists and has not expired or if we should use the visitor's location.
		if ( ! $weather_data ) {
			$request = wp_remote_get( $answer );

			if ( is_wp_error( $request ) ) {
				return false;
			}
			$body = wp_remote_retrieve_body( $request );
			$data = json_decode( $body );

			if ( $data ) {
				self::splw_set_transient( $transient_name, $data );
			}
		} else {
			$data = $weather_data;
		}
		return new ForecastWeather( $data, $units, $forecast_settings['hours'], '', $forecast_settings['type'], $forecast_settings['hourly_type'] );
	}

	/**
	 * Get the URL for retrieving hourly weather forecast data.
	 *
	 * @param string $query   A query string or location identifier (e.g., city name,city id, ZIP code, and coordinates).
	 * @param string $units   The units for temperature and other measurements (default: 'metric').
	 *                        Possible values: 'metric', 'imperial', 'standard'.
	 * @param string $lang    The language for the weather data (default: 'en').
	 * @param string $appid   Your API key for accessing the weather data service (optional).
	 * @param string $mode    The format of the response data (default: 'json').
	 *                        Possible values: 'json', 'xml', 'html'.
	 *
	 * @return string The URL for fetching hourly weather forecast data.
	 */
	public static function get_weather_hourly_forecast_url( $query, $units = 'metric', $lang = 'en', $appid = '', $mode = 'json' ) {
		// Build the URL using the provided parameters and the base hourly forecast URL.
		$url = self::build_url( $query, $units, $lang, $appid, $mode, self::$hourly_forecast_api );

		// Return the constructed URL.
		return $url;
	}

	/**
	 * Directly returns the SimpleXMLElement string returned by OpenWeatherMap.
	 *
	 * @param string $answer The content returned by OpenWeatherMap  OpenWeatherMap.
	 *
	 * @throws LWException If the content isn't valid JSON.
	 */
	private static function parse_xml( $answer ) {

		// Disable default error handling of SimpleXML (Do not throw E_WARNINGs).
		libxml_use_internal_errors( true );
		try {
			return new \SimpleXMLElement( $answer );
		} catch ( \Exception $e ) {
			// Invalid xml format. This happens in case OpenWeatherMap returns an error.
			// OpenWeatherMap always uses json for errors, even if one specifies xml as format.
			$error = json_decode( $answer, true );
			if ( isset( $error['message'] ) ) {
				return $error;
			}
		}
		libxml_clear_errors();
	}

	/**
	 * Build a complete API URL for weather data retrieval.
	 *
	 * @param string $query   The location or query for weather data.
	 * @param string $units   The units for temperature and other weather data (e.g., 'metric' or 'imperial').
	 * @param string $lang    The language for weather data responses (e.g., 'en' for English).
	 * @param string $appid   The API key for authentication (optional, can be provided as a parameter or from a class property).
	 * @param string $mode    The mode for weather data (e.g., 'json' or 'xml').
	 * @param string $url     The base URL for the weather API.
	 *
	 * @return string The complete API URL for weather data retrieval.
	 */
	private static function build_url( $query, $units, $lang, $appid, $mode, $url ) {
		// Build the query URL parameter.
		$query_url = self::build_query_url_parameter( $query );

		// Build the complete API URL with all parameters.
		$url = $url . "$query_url&units=$units&lang=$lang&mode=$mode&APPID=";

		// Append the API key (either provided or from a class property).
		$url .= empty( $appid ) ? self::$api_key : $appid;

		return $url;
	}

	/**
	 * Build the full weather API URL.
	 *
	 * @param array  $params   Query parameters (e.g., location).
	 * @param string $units    Measurement units (e.g., 'metric').
	 * @param string $lang     Language code (e.g., 'en').
	 * @param string $appid    Optional API key.
	 * @param string $base_url Weather API base URL.
	 * @return string          Complete request URL.
	 */
	private static function weather_api_build_url( $params, string $units, string $lang, string $appid, string $base_url ) {
		$query   = self::build_query_url_parameter( $params );
		$api_key = empty( $appid ) ? self::$api_key : $appid;

		return "{$base_url}{$query}&units=" . rawurlencode( $units ) . '&lang=' . rawurlencode( $lang ) . '&key=' . rawurlencode( $api_key );
	}

	/**
	 * Builds the query string for the url.
	 *
	 * @param mixed $query query of the URL parameter.
	 *
	 * @return string The built query string for the url.
	 *
	 * @throws \InvalidArgumentException If the query parameter is invalid.
	 */
	private static function build_query_url_parameter( $query ) {
		switch ( $query ) {
			case is_array( $query ) && isset( $query['lat'] ) && isset( $query['lon'] ) && is_numeric( $query['lat'] ) && is_numeric( $query['lon'] ):
				return "lat={$query['lat']}&lon={$query['lon']}";
			case is_array( $query ) && is_numeric( $query[0] ):
				return 'id=' . implode( ',', $query );
			case is_numeric( $query ):
				return "id=$query";
			case is_string( $query ) && strpos( $query, 'zip:' ) === 0:
				$sub_query = str_replace( 'zip:', '', $query );
				return 'zip=' . urlencode( $sub_query );
			case is_string( $query ):
				return 'q=' . urlencode( $query );
			default:
				return "lat={$query['lat']}&lon={$query['lon']}";
		}
	}

	/**
	 * Custom set transient
	 *
	 * @param  mixed $cache_key Key.
	 * @param  mixed $cache_data data.
	 * @return void
	 */
	public static function splw_set_transient( $cache_key, $cache_data ) {
		$cache_expire_time = apply_filters( 'sp_open_weather_api_cache_time', 600 ); // 10 minutes
		if ( ! is_admin() ) {
			if ( is_multisite() ) {
				$cache_key = $cache_key . '_' . get_current_blog_id();
				set_site_transient( $cache_key, $cache_data, $cache_expire_time );
			} else {
				set_transient( $cache_key, $cache_data, $cache_expire_time );
			}
		}
	}

	/**
	 * Custom get transient.
	 *
	 * @param  mixed $cache_key Cache key.
	 * @return content
	 */
	public static function splw_get_transient( $cache_key ) {
		if ( is_admin() ) {
			return false;
		}

		if ( is_multisite() ) {
			$cache_key   = $cache_key . '_' . get_current_blog_id();
			$cached_data = get_site_transient( $cache_key );
		} else {
			$cached_data = get_transient( $cache_key );
		}
		return $cached_data;
	}
}
