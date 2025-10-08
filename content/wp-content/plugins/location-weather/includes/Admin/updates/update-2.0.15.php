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
update_option( 'location_weather_db_version', '2.0.15' );
update_option( 'location_weather_version', '2.0.15' );

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

		$layout = isset( $splw_shortcode_meta['weather-view'] ) ? $splw_shortcode_meta['weather-view'] : 'vertical';
		if ( $layout ) {
			$layout_meta['weather-view']                = $layout;
			$layout_meta['weather-template']            = 'template-one';
			$layout_meta['weather-horizontal-template'] = 'horizontal-one';
		}

		// Update shortcode meta.
		update_post_meta( $shortcode_id, 'sp_location_weather_layout', $layout_meta );
	}
}
