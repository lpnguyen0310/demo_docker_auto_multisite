<?php
/**
 * Weather Icons Set one File
 *
 * Includes Icons Set one svg icons for the additional data frontend view.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$show_weather_icons        = ! empty( $splw_meta['lw-weather-icons'] );
$show_weather_icon_wrapper = (
	'vertical' === $layout &&
	in_array( $active_additional_data_layout, array( 'center', 'left', 'justified' ), true ) &&
	! $show_comport_data_position
);

$colon = ( 'justified' !== $active_additional_data_layout ) ? ':' : '';

if ( ! function_exists( 'splwp_get_icon' ) ) {
	/**
	 * Returns a weather icon HTML span with optional rotation styling.
	 *
	 * @param string   $icon_class CSS class for the weather icon.
	 * @param bool     $title  Whether icon title.
	 * @param int|null $show   Optional icon show or hide.
	 *
	 * @return string HTML markup for the icon span or an empty string.
	 */
	function splwp_get_icon( $icon_class, $title = '', $show = false ) {
		return $show ? '<span class="details-icon"' . ( $title ? ' title="' . esc_attr( $title ) . '"' : '' ) . '><i class="' . esc_attr( $icon_class ) . '"></i></span>' : '';
	}
}

/**
 * Weather details configuration.
 */
$weather_details = array(
	'humidity'   => array(
		'icon'  => 'splwp-icon-humidity-1',
		'label' => __( 'Humidity', 'location-weather' ),
	),
	'pressure'   => array(
		'icon'  => 'splwp-icon-pressure-1',
		'label' => __( 'Pressure', 'location-weather' ),
	),
	'wind'       => array(
		'icon'  => 'splwp-icon-wind-1',
		'label' => __( 'Wind', 'location-weather' ),
	),
	'wind_gust'  => array(
		'icon'  => 'splwp-icon-wind-gust-1',
		'label' => __( 'Wind Gust', 'location-weather' ),
	),
	'clouds'     => array(
		'icon'  => 'splwp-icon-clouds-1',
		'label' => __( 'Clouds', 'location-weather' ),
	),
	'visibility' => array(
		'icon'  => 'splwp-icon-visibility-1',
		'label' => __( 'Visibility', 'location-weather' ),
	),
	'sunrise'    => array(
		'icon'  => 'splwp-icon-sunrise-1',
		'label' => __( 'Sunrise', 'location-weather' ),
	),
	'sunset'     => array(
		'icon'  => 'splwp-icon-sunset-1',
		'label' => __( 'Sunset', 'location-weather' ),
	),
);

/**
 * Generate icons and titles.
 */
foreach ( $weather_details as $key => $detail ) {
	$show_icon = ( in_array( $key, array( 'humidity', 'pressure', 'wind' ), true ) && ( $show_weather_icons || $show_weather_icon_wrapper ) )
		|| ( $show_weather_icons && ! in_array( $key, array( 'humidity', 'pressure', 'wind' ), true ) );

	${ $key . '_icon' }  = splwp_get_icon( $detail['icon'], $detail['label'], $show_icon );
	${ $key . '_title' } = ( $show_weather_icon_wrapper && in_array( $key, array( 'humidity', 'pressure', 'wind' ), true ) ) ? '' : $detail['label'] . $colon;
}
