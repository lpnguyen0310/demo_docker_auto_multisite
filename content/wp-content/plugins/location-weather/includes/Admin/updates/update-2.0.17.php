<?php
/**
 * Updater file.
 *
 * @link       https://shapedplugin.com
 * @since      2.0.17
 *
 * @package    Location_Weather
 * @subpackage Location_Weather/Admin/updates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Update DB version.
 */
update_option( 'location_weather_db_version', '2.0.17' );
update_option( 'location_weather_version', '2.0.17' );

// If the transient exists, delete it.
if ( get_transient( 'splw_plugins' ) ) {
	delete_transient( 'splw_plugins' );
}
