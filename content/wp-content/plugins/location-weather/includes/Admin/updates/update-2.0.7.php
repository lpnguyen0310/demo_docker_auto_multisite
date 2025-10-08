<?php
/**
 * Updater file.
 *
 * @link       https://shapedplugin.com
 * @since      1.3.12
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
update_option( 'location_weather_db_version', '2.0.7' );
update_option( 'location_weather_version', '2.0.7' );

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
		$splw_shortcode_meta = get_post_meta( $shortcode_id, 'sp_location_weather_generator', true );
		if ( ! is_array( $splw_shortcode_meta ) ) {
			continue;
		}

		$splw_shortcode_meta['lw-weather-title-color'] = isset( $splw_shortcode_meta['lw-weather-title-typography']['color'] ) ? $splw_shortcode_meta['lw-weather-title-typography']['color'] : '#000';
		if ( isset( $splw_shortcode_meta['lw-weather-title-typography']['margin-top'] ) ) {
			$splw_shortcode_meta['lw-weather-title-margin'] = array(
				'top'    => $splw_shortcode_meta['lw-weather-title-typography']['margin-top'],
				'bottom' => $splw_shortcode_meta['lw-weather-title-typography']['margin-bottom'],
			);
		}

		$splw_shortcode_meta['lw-city-name-color'] = isset( $splw_shortcode_meta['lw-city-name-typography']['color'] ) ? $splw_shortcode_meta['lw-city-name-typography']['color'] : '#fff';
		if ( isset( $splw_shortcode_meta['lw-city-name-typography']['margin-top'] ) ) {
			$splw_shortcode_meta['lw-city-name-margin'] = array(
				'top'    => $splw_shortcode_meta['lw-city-name-typography']['margin-top'],
				'bottom' => $splw_shortcode_meta['lw-city-name-typography']['margin-bottom'],
			);
		}
		if ( isset( $splw_shortcode_meta['lw-date-time-typography']['color'] ) ) {
			$splw_shortcode_meta['lw-date-time-color'] = $splw_shortcode_meta['lw-date-time-typography']['color'];
		}
		if ( isset( $splw_shortcode_meta['lw-date-time-typography']['margin-top'] ) ) {
			$splw_shortcode_meta['lw-date-time-margin'] = array(
				'top'    => $splw_shortcode_meta['lw-date-time-typography']['margin-top'],
				'bottom' => $splw_shortcode_meta['lw-date-time-typography']['margin-bottom'],
			);
		}
		$splw_shortcode_meta['lw-temp-scale-color'] = isset( $splw_shortcode_meta['lw-temp-scale-typography']['color'] ) ? $splw_shortcode_meta['lw-temp-scale-typography']['color'] : '#fff';
		if ( isset( $splw_shortcode_meta['lw-temp-scale-typography']['margin-top'] ) ) {
			$splw_shortcode_meta['lw-temp-scale-margin'] = array(
				'top'    => $splw_shortcode_meta['lw-temp-scale-typography']['margin-top'],
				'bottom' => $splw_shortcode_meta['lw-temp-scale-typography']['margin-bottom'],
			);
		}

		$splw_shortcode_meta['lw-real-feel-weather-desc-color'] = isset( $splw_shortcode_meta['lw-real-feel-weather-desc-typography']['color'] ) ? $splw_shortcode_meta['lw-real-feel-weather-desc-typography']['color'] : '#fff';
		if ( isset( $splw_shortcode_meta['lw-real-feel-weather-desc-typography']['margin-top'] ) ) {
			$splw_shortcode_meta['lw-real-feel-weather-desc-margin'] = array(
				'top'    => $splw_shortcode_meta['lw-real-feel-weather-desc-typography']['margin-top'],
				'bottom' => $splw_shortcode_meta['lw-real-feel-weather-desc-typography']['margin-bottom'],
			);
		}
		if ( isset( $splw_shortcode_meta['lw-weather-units-typography']['color'] ) ) {
			$splw_shortcode_meta['lw-weather-units-color'] = $splw_shortcode_meta['lw-weather-units-typography']['color'];
		}

		$splw_shortcode_meta['lw-weather-units-icons-color'] = isset( $splw_shortcode_meta['lw-weather-units-icons-typography']['color'] ) ? $splw_shortcode_meta['lw-weather-units-icons-typography']['color'] : '#fff';
		if ( isset( $splw_shortcode_meta['lw-weather-units-icons-typography']['margin-top'] ) ) {
			$splw_shortcode_meta['lw-weather-units-icons-margin'] = array(
				'top'    => $splw_shortcode_meta['lw-weather-units-icons-typography']['margin-top'],
				'bottom' => $splw_shortcode_meta['lw-weather-units-icons-typography']['margin-bottom'],
			);
		}
		if ( isset( $splw_shortcode_meta['lw-weather-attr-typography']['color'] ) ) {
			$splw_shortcode_meta['lw-weather-attr-color'] = $splw_shortcode_meta['lw-weather-attr-typography']['color'];
		}

		// Update shortcode meta.
		update_post_meta( $shortcode_id, 'sp_location_weather_generator', $splw_shortcode_meta );
	}
}
