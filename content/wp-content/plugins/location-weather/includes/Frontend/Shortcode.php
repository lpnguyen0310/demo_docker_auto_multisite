<?php
/**
 * Shortcode class file.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ShapedPlugin\Weather\Frontend\Scripts;
use ShapedPlugin\Weather\Frontend\Manage_API;

/**
 * Shortcode handler class.
 */
class Shortcode {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_shortcode( 'location-weather', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Full html show.
	 *
	 * @param array $shortcode_id Shortcode ID.
	 * @param array $splw_option get all options.
	 * @param array $splw_meta get all meta options.
	 * @param array $layout_meta get all layout meta options.
	 */
	public static function splw_html_show( $shortcode_id, $splw_option, $splw_meta, $layout_meta, $is_admin = false ) {
		// Weather option meta area.
		$api_source = $splw_option['lw_api_source_type'] ?? 'openweather_api';

		// Get the weather data.
		if ( 'openweather_api' === $api_source ) {
			$open_api_key = $splw_option['open-api-key'] ?? '';
			$appid        = ! empty( $open_api_key ) ? $open_api_key : '';
			// Set default API key if not found any API.
			if ( ! $appid ) {
				$default_api_calls = (int) get_option( 'splw_default_call', 0 );
				if ( $default_api_calls < 20 ) {
					$appid          = 'e930dd32085dea457d1d66d01cd89f50';
					$transient_name = 'sp_open_weather_data' . $shortcode_id;
					$weather_data   = Manage_API::splw_get_transient( $transient_name );
					if ( false === $weather_data ) {
						++$default_api_calls;
						update_option( 'splw_default_call', $default_api_calls );
					}
				}
			}
		} else {
			// WeatherAPI API key.
			$weather_api_key = $splw_option['weather-api-key'] ?? '';
			$appid           = $weather_api_key;
		}

		// Check if the API key is empty.
		// If the API key is empty, show a warning message.
		if ( ! $appid ) {
			$url            = 'openweather_api' === $api_source ? 'https://openweathermap.org/api' : 'https://weatherapi.com/';
			$weather_from   = 'openweather_api' === $api_source ? 'OpenWeatherMap' : 'WeatherAPI';
			$weather_output = sprintf(
				'<div id="splw-location-weather-%1$s" class="splw-main-wrapper">
				<div class="splw-weather-title">%2$s</div>
				<div class="splw-lite-wrapper">
					<div class="splw-warning">%3$s</div> 
					<div class="splw-weather-attribution">
						<a href="' . esc_url( $url ) . '" target="_blank">' . __( 'Weather from ', 'location-weather' ) . $weather_from . '</a>
					</div>
				</div>
			</div>',
				esc_attr( $shortcode_id ),
				esc_html( get_the_title( $shortcode_id ) ),
				'Please set your weather <a href="' . admin_url( 'edit.php?post_type=location_weather&page=lw-settings#tab=weather-api-key' ) . '" target="_blank">API key.</a>'
			);

			echo $weather_output; // phpcs:ignore
			return;
		}
		$layout                        = isset( $layout_meta['weather-view'] ) && ! wp_is_mobile() ? $layout_meta['weather-view'] : 'vertical';
		$active_additional_data_layout = $splw_meta['weather-additional-data-layout'] ?? 'center';
		$show_comport_data_position    = $splw_meta['lw-comport-data-position'] ?? false;

		// Weather setup meta area .
		$custom_name     = $splw_meta['lw-custom-name'] ?? '';
		$pressure_unit   = $splw_meta['lw-pressure-unit'] ?? 'mb';
		$visibility_unit = $splw_meta['lw-visibility-unit'] ?? 'km';
		$wind_speed_unit = $splw_meta['lw-wind-speed-unit'] ?? 'mph';
		$lw_language     = $splw_meta['lw-language'] ?? 'en';

		// Display settings meta section.
		$show_weather_title = $splw_meta['lw-title'] ?? true;
		$time_format        = $splw_meta['lw-time-format'] ?? 'g:i a';
		$utc_timezone       = isset( $splw_meta['lw-utc-time-zone'] ) && ! empty( $splw_meta['lw-utc-time-zone'] ) ? (float) str_replace( 'UTC', '', $splw_meta['lw-utc-time-zone'] ) * 3600 : '';

		$lw_modify_date_format = $splw_meta['lw_client_date_format'] ?? 'F j, Y';
		$lw_custom_date_format = preg_replace( '/\s*,?\s*\b(?:g:i a|g:i A|H:i|h:i|g:ia|g:iA)\b\s*,?\s*/i', '', $lw_modify_date_format );
		$lw_client_date_format = isset( $splw_meta['lw_date_format'] ) && 'custom' !== $splw_meta['lw_date_format'] ? $splw_meta['lw_date_format'] : $lw_custom_date_format;
		$show_date             = $splw_meta['lw-date'] ?? true;
		$show_time             = $splw_meta['lw-show-time'] ?? true;
		$show_icon             = $splw_meta['lw-icon'] ?? true;

		// Temperature and weather units show hide meta.
		$show_temperature          = $splw_meta['lw-temperature'] ?? true;
		$temperature_scale         = $splw_meta['lw-display-temp-scale'] ?? true;
		$short_description         = $splw_meta['lw-short-description'] ?? true;
		$show_pressure             = $splw_meta['lw-pressure'] ?? true;
		$show_humidity             = $splw_meta['lw-humidity'] ?? true;
		$show_clouds               = $splw_meta['lw-clouds'] ?? true;
		$show_wind                 = $splw_meta['lw-wind'] ?? true;
		$show_wind_gusts           = $splw_meta['lw-wind-gusts'] ?? true;
		$show_visibility           = $splw_meta['lw-visibility'] ?? true;
		$show_sunrise_sunset       = $splw_meta['lw-sunrise-sunset'] ?? true;
		$lw_current_icon_type      = $splw_meta['weather-current-icon-type'] ?? 'forecast_icon_set_one';
		$show_weather_attr         = $splw_meta['lw-attribution'] ?? true;
		$show_weather_detailed     = $splw_meta['lw-weather-details'] ?? false;
		$show_weather_updated_time = $splw_meta['lw-weather-update-time'] ?? false;

		$forecast_icon_type      = $splw_meta['weather-forecast-icon-type'] ?? 'forecast_icon_set_one';
		$hourly_type             = $splw_meta['lw-hourly-type'] ?? 'three-hour';
		$one_forecasting_hours   = $splw_meta['lw-number-forecast-hours'] ?? '8';
		$three_forecasting_hours = $splw_meta['lw-number-forecast-three-hours'] ?? '8';
		if ( 'openweather_api' === $api_source ) {
			$hourly_type = 'three-hour';
		}
		$number_of_hours = 'three-hour' === $hourly_type ? (int) $three_forecasting_hours : (int) $one_forecasting_hours;
		$number_of_hours = $number_of_hours > 8 ? 8 : $number_of_hours;

		// Units show hide meta.
		$weather_units     = $splw_meta['lw-units'] ?? 'metric';
		$temperature_scale = $temperature_scale || 'none' !== $weather_units ? true : false;
		if ( 'auto_temp' === $weather_units || 'auto' === $weather_units || 'none' === $weather_units ) {
			$weather_units = 'metric';
		}

		$weather_by = $splw_meta['get-weather-by'] ?? 'city_name';

		switch ( $weather_by ) {
			case 'city_name':
				$city  = trim( $splw_meta['lw-city-name'] ?? '' );
				$query = ! empty( $city ) ? $city : 'london';
				break;

			case 'city_id':
				$city_id = $splw_meta['lw-city-id'] ?? '';
				$query   = ! empty( $city_id ) ? (int) $city_id : 2643743;
				break;

			case 'latlong':
				$latlong_raw = $splw_meta['lw-latlong'] ?? '';
				$default     = array(
					'lat' => 51.509865,
					'lon' => -0.118092,
				);

				if ( ! empty( $latlong_raw ) && strpos( $latlong_raw, ',' ) !== false ) {
					$latlong = explode( ',', str_replace( ' ', '', trim( $latlong_raw ) ) );
					$lat     = $latlong[0] ?? null;
					$lon     = $latlong[1] ?? null;
					$query   = ( is_numeric( $lat ) && is_numeric( $lon ) ) ? array(
						'lat' => (float) $lat,
						'lon' => (float) $lon,
					) : $default;
				} else {
					$query = $default;
				}
				break;

			case 'zip':
				$zip   = trim( $splw_meta['lw-zip'] ?? '' );
				$query = ! empty( $zip ) ? 'zip:' . $zip . '' : 'zip:77070,US';
				break;

			default:
				$query = 'london';
				break;
		}

		if ( 'openweather_api' === $api_source ) {
			$data = Manage_API::get_weather( $query, $weather_units, $lw_language, $appid, $shortcode_id );
		} else {
			$api_query        = is_array( $query ) ? implode( ',', $query ) : $query;
			$weather_api_data = Manage_API::weather_api_data( $api_query, $weather_units, $lw_language, $appid, $shortcode_id, $number_of_hours, $hourly_type );

			// Api call error check.
			if ( is_array( $weather_api_data ) && isset( $weather_api_data['code'] ) && ( 1006 === $weather_api_data['code'] || 2006 === $weather_api_data['code'] ) ) {
				$weather_error_status = sprintf( '<div id="splw-location-weather-%1$s" class="splw-main-wrapper"><div class="splw-weather-title">%2$s</div><div class="splw-lite-wrapper"><div class="splw-warning">%3$s</div> <div class="splw-weather-attribution"><a href = "https://www.weatherapi.com/docs/key.aspx" target="_blank">' . __( 'Weather from WeatherAPI ', 'location-weather' ) . '</a></div></div></div>', esc_attr( $shortcode_id ), esc_html( get_the_title( $shortcode_id ) ), $weather_api_data['message'] );
				echo $weather_error_status; // phpcs:ignore
				return;
			}
			$data = $weather_api_data['current'];
		}

		if ( is_array( $data ) && isset( $data['code'] ) && ( 401 === $data['code'] || 404 === $data['code'] ) ) {
			$weather_output = sprintf( '<div id="splw-location-weather-%1$s" class="splw-main-wrapper"><div class="splw-weather-title">%2$s</div><div class="splw-lite-wrapper"><div class="splw-warning">%3$s</div> <div class="splw-weather-attribution"><a href = "https://openweathermap.org/" target="_blank">' . __( 'Weather from OpenWeatherMap', 'location-weather' ) . '</a></div></div></div>', esc_attr( $shortcode_id ), esc_html( get_the_title( $shortcode_id ) ), $data['message'] );

			echo $weather_output; // phpcs:ignore
			return;
		}

		// Current weather data.
		$weather_data = self::current_weather_data( $data, $time_format, $temperature_scale, $wind_speed_unit, $weather_units, $pressure_unit, $visibility_unit, $lw_client_date_format, $utc_timezone, $api_source );

		// Forecast meta options.
		$forecast_data = '';
		$show_forecast = $splw_meta['lw-enable-forecast'] ?? true;
		if ( $show_forecast ) {
			$forecast_data_sortable        = $splw_meta['lw_forecast_data_sortable'] ?? array(
				'temperature'   => true,
				'precipitation' => true,
				'rainchance'    => true,
				'wind'          => true,
				'humidity'      => true,
				'pressure'      => true,
				'snow'          => false,
			);
			$hourly_forecast_section_title = $splw_meta['hourly-forecast-title'] ?? 'Hourly Forecast';
			$forecast_settings             = array(
				'type'        => 'hourly',
				'hours'       => $number_of_hours,
				'hourly_type' => $hourly_type,
			);

			$measurement_units = array(
				'temperature_scale' => $temperature_scale,
				'pressure_unit'     => $pressure_unit,
				'visibility_unit'   => $visibility_unit,
				'wind_speed_unit'   => $wind_speed_unit,
				'weather_unit'      => $weather_units,
				'humidity_unit'     => '%',
			);

			$city_time_zone = $utc_timezone && ! empty( $utc_timezone ) || '' !== $utc_timezone ? (int) $utc_timezone : (int) $data->timezone;

			// Time settings configuration.
			$time_settings = array(
				'time_format'       => $time_format,
				'date_format'       => $lw_client_date_format,
				'time_zone'         => $city_time_zone,
				'weather_time_zone' => $utc_timezone && ! empty( $utc_timezone ) || '' !== $utc_timezone ? (int) $utc_timezone
				: ( 'openweather_api' === $api_source ? $city_time_zone : null ),
			);

			if ( 'openweather_api' === $api_source ) {
				$forecast = Manage_API::get_weather_hourly_forecast_data( $query, $weather_units, $lw_language, $appid, $shortcode_id, $forecast_settings );
			} else {
				$forecast = $weather_api_data['forecast'];
			}

			if ( is_object( $forecast ) ) {
				$forecast_data = $forecast->hourly_forecast;
			}
		}

		ob_start();
		include self::lw_locate_template( 'main-template.php' );
		$weather_output = ob_get_clean();
		echo $weather_output;// phpcs:ignore.
	}

	/**
	 * Shortcode render class.
	 *
	 * @param array  $attribute The shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return void
	 */
	public function render_shortcode( $attribute, $content = '' ) {
		if ( empty( $attribute['id'] ) || 'location_weather' !== get_post_type( $attribute['id'] ) || ( get_post_status( $attribute['id'] ) === 'trash' ) ) {
			return;
		}
		$shortcode_id = esc_attr( intval( $attribute['id'] ) );
		$splw_option  = get_option( 'location_weather_settings', true );
		$splw_meta    = get_post_meta( $shortcode_id, 'sp_location_weather_generator', true );
		$layout_meta  = get_post_meta( $shortcode_id, 'sp_location_weather_layout', true );
		$is_admin     = $attribute['is_admin'] ?? false;
		// Stylesheet loading problem solving here. Shortcode id to push page id option for getting how many shortcode in the page.
		$get_page_data      = Scripts::get_page_data();
		$found_generator_id = $get_page_data['generator_id'];
		ob_start();
		// This shortcode id not in page id option. Enqueue stylesheets in shortcode.
		if ( ! is_array( $found_generator_id ) || ! $found_generator_id || ! in_array( $shortcode_id, $found_generator_id ) ) {
			wp_enqueue_style( 'splw-fontello' );
			wp_enqueue_style( 'splw-styles' );
			wp_enqueue_style( 'splw-old-styles' );
			/* Load dynamic style in the header based on found shortcode on the page. */
			$dynamic_style = Scripts::load_dynamic_style( $shortcode_id, $splw_meta );
			echo '<style id="sp_lw_dynamic_css' . $shortcode_id . '">' . wp_strip_all_tags( $dynamic_style['dynamic_css'] ) . '</style>';//phpcs:ignore
		}
		// Update options if the existing shortcode id option not found.
		Scripts::lw_db_options_update( $shortcode_id, $get_page_data );
		self::splw_html_show( $shortcode_id, $splw_option, $splw_meta, $layout_meta, $is_admin );
		wp_enqueue_script( 'splw-old-script' );
		wp_enqueue_script( 'splw-scripts' );
		return ob_get_clean();
	}
	// Shortcode render method end.

	/**
	 * Retrieves and formats current weather data.
	 *
	 * @param stdClass $data              The weather data object.
	 * @param string   $time_format       The time format (12-hour or 24-hour).
	 * @param string   $temperature_scale The temperature scale (e.g., 'C' or 'F').
	 * @param string   $wind_speed_unit   The wind speed unit (e.g., 'm/s' or 'mph').
	 * @param string   $weather_units     The units for weather data.
	 * @param string   $pressure_unit     The unit for pressure (e.g., 'hPa' or 'inHg').
	 * @param string   $visibility_unit   The unit for visibility (e.g., 'km' or 'mi').
	 * @param string   $lw_client_date_format The date format for the client's timezone.
	 * @param int|null $utc_timezone      The UTC timezone offset.
	 * @param string   $api_source      The API source.
	 *
	 * @return array|null An array containing formatted weather data or null if the input data is not an object.
	 */
	public static function current_weather_data( $data, $time_format, $temperature_scale, $wind_speed_unit, $weather_units, $pressure_unit, $visibility_unit, $lw_client_date_format, $utc_timezone = null, $api_source = 'openweather_api' ) {
		if ( ! is_object( $data->city ) ) {
			return;
		}
		$scale         = self::temperature_scale( $temperature_scale, $weather_units );
		$temp          = '<span class="current-temperature">' . round( $data->temperature->now->value ) . '</span>' . $scale;
		$sunrise       = $data->sun->rise;
		$sunset        = $data->sun->set;
		$last_update   = $data->last_update;
		$timezone      = $utc_timezone && ! empty( $utc_timezone ) || '' !== $utc_timezone ? (int) $utc_timezone : (int) $data->timezone;
		$api_time_zone = 'openweather_api' !== $api_source ? null : $timezone;
		$wind          = self::get_wind_speed( $weather_units, $wind_speed_unit, $data, false );
		$gust          = self::get_wind_speed( $weather_units, $wind_speed_unit, $data, true );
		$now           = new \DateTime();

		// Check date and time format.
		if ( $time_format && null !== $last_update ) {
			$time         = date_i18n( $time_format, strtotime( $now->format( 'Y-m-d g:i:sa' ) ) + $timezone );
			$date         = date_i18n( $lw_client_date_format, strtotime( $now->format( 'Y-m-d g:i:sa' ) ) + $timezone );
			$sunrise_time = gmdate( $time_format, strtotime( $sunrise->format( 'Y-m-d g:i:sa' ) ) + $api_time_zone );
			$sunset_time  = gmdate( $time_format, strtotime( $sunset->format( 'Y-m-d g:i:sa' ) ) + $api_time_zone );
			$updated_time = gmdate( $time_format, strtotime( $last_update->format( 'Y-m-d g:i:sa' ) ) + $timezone );
		}
		return array(
			'city_id'      => $data->city->id,
			'city'         => $data->city->name,
			'country'      => $data->city->country,
			'temp'         => $temp,
			'pressure'     => self::get_pressure( $pressure_unit, $data ),
			'humidity'     => $data->humidity,
			'wind'         => $wind,
			'gust'         => $gust,
			'visibility'   => self::get_visibility( $visibility_unit, $data ),
			'clouds'       => $data->clouds->value . '%',
			'desc'         => $data->weather->description,
			'icon'         => $data->weather->icon,
			'time'         => $time,
			'date'         => $date,
			'updated_time' => $updated_time,
			'sunrise_time' => $sunrise_time,
			'sunset_time'  => $sunset_time,
		);
	}

	/**
	 * Get forecast data for display.
	 *
	 * @param object $data             The weather data object for the location.
	 * @param array  $measurement_units Array of measurement units:
	 * - 'temperature_scale': 'F' or 'C' (default: 'C').
	 * - 'pressure_unit': 'mb' or 'kpa' (default: 'mb').
	 * - 'visibility_unit': 'km' or 'mi' (default: 'km').
	 * - 'wind_speed_unit': 'mph', 'kmh', 'kts', or 'm/s' (default: 'mph').
	 * - 'weather_units': 'metric' or 'imperial' (default: 'metric').
	 * - 'precipitation_unit': e.g., 'mm' or 'in' (default: 'mm').
	 * @param array  $time_settings     Array of time settings:
	 * - 'time_format': '24' or '12' (default: '12').
	 * - 'date_format': Date format (default: 'Y-m-d').
	 *
	 * @return array Forecast data.
	 */
	public static function get_forecast_data( $data, $measurement_units, $time_settings ) {

		$last_update = $data->last_update;
		// Calculate time with timezone offset.
		$time               = date_i18n( $time_settings['time_format'], strtotime( $last_update->format( 'D M d g:i a' ) ) + $time_settings['weather_time_zone'] );
		$date_with_timezone = gmdate( 'D M d g:i a', strtotime( $last_update->format( 'D M d g:i a' ) ) + $time_settings['weather_time_zone'] );
		$date_format        = $last_update->format( 'D M d' );

		// Determine temperature values based on available data.
		if ( ! empty( $data->temperature->value ) ) {
			$min_value    = $data->temperature->value;
			$max_temp     = 0;
			$current_temp = $data->temperature->value;
		} else {
			$min_value    = $data->temperature->min->value;
			$max_temp     = $data->temperature->max->value;
			$current_temp = $data->temperature->now->value;
		}
		$scale = self::temperature_scale( $measurement_units['temperature_scale'], $measurement_units['weather_unit'] );

		// Format min and max temperature values.
		$min_temp = '<span class="low">' . round( $min_value ) . '</span><span class="low-scale">°</span>';

		$max_temp = $max_temp ? '<span class="high">' . round( $max_temp ) . '</span><span class="high-scale">°</span>' . $scale : '';
		$pressure = self::get_pressure( $measurement_units['pressure_unit'], $data );
		$wind     = self::get_wind_speed( $measurement_units['weather_unit'], $measurement_units['wind_speed_unit'], $data, false );
		$gusts    = isset( $data->gusts ) ? self::get_wind_speed( $measurement_units['weather_unit'], $measurement_units['wind_speed_unit'], $data, true ) : null;

		// Return the forecast data as an array.
		return array(
			'now'             => $current_temp,
			'min'             => $min_temp,
			'max'             => $max_temp,
			'humidity'        => $data->humidity . '%',
			'precipitation'   => $data->precipitation . ' mm',
			'rain'            => $data->rainchance,
			'snow'            => $data->snow,
			'icon'            => $data->weather->icon,
			'desc'            => $data->weather->description,
			'times'           => $time,
			'date_format'     => $date_format,
			'timezone_offset' => $time_settings['time_zone'],
			'pressure'        => self::get_pressure( $measurement_units['pressure_unit'], $data ),
			'wind'            => $wind,
			'id'              => $data->weather->id,
			'gusts'           => $gusts,
			'clouds'          => isset( $data->clouds ) ? $data->clouds->value : null,
		);
	}

	/**
	 * Get the forecast weather data.
	 *
	 * @param string $temperature_scale Can be either 'F' or 'C' (default).
	 * @param string $weather_units Can be either 'metric' or 'imperial' (default). This affects almost all units returned.
	 *
	 * @return scale The weather temperature scale object.
	 */
	public static function temperature_scale( $temperature_scale, $weather_units ) {
		$scale = '°';
		if ( $temperature_scale && 'imperial' === $weather_units ) {
			$scale = '°F';
		} elseif ( $temperature_scale && 'metric' === $weather_units ) {
			$scale = '°C';
		} else {
			$scale = '°';
		}
		return '<span class="temperature-scale">' . $scale . '</span>';
	}

	/**
	 * Get the weather wind speed unit.
	 *
	 * @param string            $weather_units Can be either 'metric' or 'imperial' (default). This affects almost all units returned.
	 * @param string            $wind_speed_unit Can be either 'mph', 'kmh','kts'  or 'mph' (default). This affects almost all units returned.
	 * @param object|int|string $data The place to get weather information for. For possible values see below.
	 * @param string            $gust Can be either 'mph', 'kmh','kts'  or 'mph' (default). This affects almost all units returned.
	 * @return wind The weather object
	 */
	public static function get_wind_speed( $weather_units, $wind_speed_unit, $data, $gust = false ) {
		if ( $gust ) {
			$winds = $data->gusts->value;
		} else {
			$winds = $data->wind->speed->value;
		}
		if ( 'imperial' === $weather_units ) {
			switch ( $wind_speed_unit ) {
				case 'kmh':
					$wind = round( $winds * 1.61 ) . ' Km/h';
					break;
				default:
					$wind = round( $winds ) . ' mph';
					break;
			}
		} else {
			switch ( $wind_speed_unit ) {
				case 'kmh':
					$wind = round( $winds * 3.6 ) . ' Km/h';
					break;
				default:
					$wind = round( $winds * 2.2 ) . ' mph';
					break;
			}
		}
		return $wind;
	}

	/**
	 * Get the weather wind speed unit.
	 *
	 * @param string            $pressure_unit Can be either 'mb' or 'kpa' (default). This affects almost all units returned.
	 * @param object|int|string $data The place to get weather information for. For possible values see below.
	 * @return Pressure The weather object.
	 **/
	public static function get_pressure( $pressure_unit, $data ) {
		$pressures = $data->pressure->value;
		if ( 'hpa' === $pressure_unit ) {
			$pressure = round( $pressures ) . __( ' hPa', 'location-weather' );
		} else {
			$pressure = round( $pressures ) . __( ' mb', 'location-weather' );
		}
		return $pressure;
	}

	/**
	 * Get and format visibility data based on the specified unit.
	 *
	 * @param string   $visibility_unit The unit for visibility data ('km' or 'mi').
	 * @param stdClass $data           The weather data object containing visibility information.
	 *
	 * @return string Formatted visibility data based on the specified unit.
	 */
	public static function get_visibility( $visibility_unit, $data ) {
		$visibility_value = $data->visibility->value;

		if ( 'km' === $visibility_unit ) {
			$visibility = $visibility_value . __( ' km', 'location-weather' );
		} else {
			$visibility = round( $visibility_value * 0.621371 ) . __( ' mi', 'location-weather' );
		}
		return $visibility;
	}

	/**
	 * Locates the template file for the specified template name.
	 *
	 * Searches for the template file in the given template path or falls back to the default path.
	 *
	 * @param string $template_name The name of the template file to locate.
	 * @param string $template_path Optional. The path where the template file should be searched. Defaults to 'location-weather-pro/templates'.
	 * @param  mixed  $default_path default path.
	 * @return string The path to the located template file.
	 */
	public static function lw_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = 'location-weather/templates';
		}
		if ( ! $default_path ) {
			$default_path = LOCATION_WEATHER_TEMPLATE_PATH . 'Frontend/templates/';
		}
		$template = locate_template( trailingslashit( $template_path ) . $template_name );
		// Get default template.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}
		// Return what we found.
		return $template;
	}
}
