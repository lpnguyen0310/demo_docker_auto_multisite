<?php
/**
 * Weather class file.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend\Api\Aid;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The wind class representing a wind object.
 */
class Wind {
	/**
	 * The wind speed.
	 *
	 * @var Unit
	 */
	public $speed;

	/**
	 * Create a new wind object.
	 *
	 * @param Unit $speed     The wind speed.
	 *
	 * @internal
	 */
	public function __construct( Unit $speed ) {
		$this->speed = $speed;
	}
}
