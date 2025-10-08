<?php
/**
 * IconConverter class file for WeatherAPI.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend\Api\Aid;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The location class representing a location object.
 */
class IconConverter {
	/**
	 * Get OpenWeatherMap-compatible icon code based on weather condition code.
	 *
	 * Maps weather condition codes (from sources like WeatherAPI or similar)
	 * to OpenWeatherMap-style icon IDs (e.g., '01d', '10n').
	 *
	 * @param int  $code       The weather condition code to map.
	 * @param bool $is_daytime Whether it's daytime (true) or nighttime (false).
	 *
	 * @return string The OWM icon code (e.g., '01d', '10n').
	 */
	public static function get_owm_icon( $code, $is_daytime = true ) {

		$suffix = $is_daytime ? 'd' : 'n';

		$map = array(
			// Clear / Cloudy.
			1000 => '01', // Sunny/Clear.
			1003 => '02', // Partly cloudy.
			1006 => '03', // Cloudy.
			1009 => '04', // Overcast.

		// Mist / Fog.
			1030 => '50', // Mist.
			1135 => '50', // Fog.
			1147 => '50', // Freezing fog.

		// Rain / Drizzle.
			1063 => '09',
			1150 => '09',
			1153 => '09',
			1180 => '09',
			1183 => '09',
			1186 => '10',
			1189 => '10',
			1192 => '10',
			1195 => '10',
			1198 => '13',
			1201 => '13', // Freezing rain.

		// Sleet / Snow.
			1066 => '13',
			1210 => '13',
			1213 => '13',
			1216 => '13',
			1219 => '13',
			1222 => '13',
			1225 => '13',
			1237 => '13',
			1249 => '13',
			1252 => '13',
			1255 => '13',
			1258 => '13',
			1261 => '13',
			1264 => '13',

			// Thunder.
			1087 => '11',
			1273 => '11',
			1276 => '11',
			1279 => '11',
			1282 => '11',
		);

		$icon = isset( $map[ $code ] ) ? $map[ $code ] : '01'; // fallback to clear.
		return "{$icon}{$suffix}";
	}
}
