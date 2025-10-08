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
update_option( 'location_weather_db_version', '2.0.18' );
update_option( 'location_weather_version', '2.0.18' );

// Query posts of type 'location_weather' to update shortcode meta.
$args = new WP_Query(
	array(
		'post_type'      => 'location_weather',
		'post_status'    => 'any',
		'posts_per_page' => '300',
	)
);

$shortcode_ids = wp_list_pluck( $args->posts, 'ID' );

if ( count( $shortcode_ids ) > 0 ) {
	foreach ( $shortcode_ids as $shortcode_id ) {
		$shortcode_meta = get_post_meta( $shortcode_id, 'sp_location_weather_generator', true );
		if ( ! is_array( $shortcode_meta ) ) {
			continue;
		}
		$additional_icon_color = isset( $shortcode_meta['lw-icon-color'] ) && is_string( $shortcode_meta['lw-icon-color'] ) ? $shortcode_meta['lw-icon-color'] : '#fff';

		$shortcode_meta['lw-icon-color'] = array(
			'color' => $additional_icon_color,
		);

		// Update shortcode meta.
		update_post_meta( $shortcode_id, 'sp_location_weather_generator', $shortcode_meta );
	}
}
