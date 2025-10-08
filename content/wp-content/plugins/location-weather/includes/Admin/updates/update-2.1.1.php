<?php
/**
 * Updater file.
 *
 * @link       https://shapedplugin.com
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
update_option( 'location_weather_db_version', '2.1.1' );
update_option( 'location_weather_version', '2.1.1' );

$args = new WP_Query(
	array(
		'post_type'      => 'location_weather',
		'post_status'    => 'any',
		'posts_per_page' => 300,
		'fields'         => 'ids',
	)
);

$shortcode_ids = $args->posts;

if ( ! empty( $shortcode_ids ) && is_array( $shortcode_ids ) ) {
	foreach ( $shortcode_ids as $shortcode_id ) {
		$shortcode_meta = get_post_meta( $shortcode_id, 'sp_location_weather_generator', true );

		if ( ! is_array( $shortcode_meta ) ) {
			continue;
		}

		$shortcode_meta['lw-enable-forecast'] = false;

		update_post_meta( $shortcode_id, 'sp_location_weather_generator', $shortcode_meta );
	}
}
