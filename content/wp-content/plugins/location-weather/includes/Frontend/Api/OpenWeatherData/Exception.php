<?php
/**
 * Exception class file.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend\Api\OpenWeatherData;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Dummy class extending \Exception to allow checking if it is an error
 * or an argument error.
 */
class Exception extends \Exception {

}
