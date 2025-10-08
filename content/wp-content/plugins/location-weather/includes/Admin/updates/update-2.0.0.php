<?php
/**
 * Update options for the version 1.3.12
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
update_option( 'location_weather_db_version', '2.0.0' );
update_option( 'location_weather_version', '2.0.0' );

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

		// Show Sunset and Sunrise time field update.
		$lw_sunrise = isset( $splw_shortcode_meta['lw-sunrise'] ) ? $splw_shortcode_meta['lw-sunrise'] : '';
		$lw_sunset  = isset( $splw_shortcode_meta['lw-sunset'] ) ? $splw_shortcode_meta['lw-sunset'] : '';
		if ( $lw_sunrise || $lw_sunset ) {
			$splw_shortcode_meta['lw-sunrise-sunset'] = true;
		}

		// Update time format.
		$old_time_format = isset( $splw_shortcode_meta['lw-time-format'] ) ? $splw_shortcode_meta['lw-time-format'] : '';
		switch ( $old_time_format ) {
			case '12':
				$splw_shortcode_meta['lw-time-format'] = 'g:i a';
				break;
			case '24':
				$splw_shortcode_meta['lw-time-format'] = 'H:i';
				break;
		}
		// Update font color.
		$old_attr_color = isset( $splw_shortcode_meta['lw-text-color'] ) ? $splw_shortcode_meta['lw-text-color'] : '#fff';
		$splw_shortcode_meta['lw-city-name-typography']['color']              = $old_attr_color;
		$splw_shortcode_meta['lw-date-time-typography']['color']              = $old_attr_color;
		$splw_shortcode_meta['lw-temp-scale-typography']['color']             = $old_attr_color;
		$splw_shortcode_meta['lw-real-feel-weather-desc-typography']['color'] = $old_attr_color;
		$splw_shortcode_meta['llw-weather-units-typography']['color']         = $old_attr_color;
		$splw_shortcode_meta['lw-weather-units-icons-typography']['color']    = $old_attr_color;
		$splw_shortcode_meta['lw-weather-attr-typography']['color']           = $old_attr_color;
		$splw_shortcode_meta['lw-weather-additional-data-margin']['top']      = isset( $splw_shortcode_meta['lw-weather-units-typography']['margin-top'] ) ? $splw_shortcode_meta['lw-weather-units-typography']['margin-top'] : '8';
		$splw_shortcode_meta['lw-weather-additional-data-margin']['bottom']   = isset( $splw_shortcode_meta['lw-weather-units-typography']['margin-top'] ) ? $splw_shortcode_meta['lw-weather-units-typography']['margin-top'] : '3';
		$splw_shortcode_meta['lw-attr-margin']['top']                         = isset( $splw_shortcode_meta['lw-weather-attr-typography']['margin-top'] ) ? $splw_shortcode_meta['lw-weather-attr-typography']['margin-top'] : '0';
		$splw_shortcode_meta['lw-attr-margin']['bottom']                      = isset( $splw_shortcode_meta['lw-weather-attr-typography']['margin-bottom'] ) ? $splw_shortcode_meta['lw-weather-attr-typography']['margin-bottom'] : '0';
		$show_animated_icons                              = isset( $splw_meta['lw-animated-icons'] ) ? $splw_meta['lw-animated-icons'] : '';
		$splw_shortcode_meta['weather-current-icon-type'] = $show_animated_icons ? 'forecast_icon_set_one' : 'forecast_icon_set_two';

		// Update openweather links option.
		$splw_shortcode_meta['lw-openweather-links'] = true;

		// Update weather section margin.
		$old_weather_section_margin_top                                      = isset( $splw_shortcode_meta['lw_title_margin']['top'] ) ? $splw_shortcode_meta['lw_title_margin']['top'] : 0;
		$old_weather_section_margin_bottom                                   = isset( $splw_shortcode_meta['lw_title_margin']['bottom'] ) ? $splw_shortcode_meta['lw_title_margin']['bottom'] : 20;
		$splw_shortcode_meta['lw-weather-title-typography']['margin-top']    = $old_weather_section_margin_top;
		$splw_shortcode_meta['lw-weather-title-typography']['margin-bottom'] = $old_weather_section_margin_bottom;

		// Update shortcode meta.
		update_post_meta( $shortcode_id, 'sp_location_weather_generator', $splw_shortcode_meta );
	}
}
