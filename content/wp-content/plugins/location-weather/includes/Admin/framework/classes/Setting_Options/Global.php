<?php
/**
 * The settings configuration.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

// Set a unique slug-like ID.
$prefix = 'location_weather_settings';

// Create options.
SPLW::createOptions(
	$prefix,
	array(
		'menu_title'         => __( 'Settings', 'location-weather' ),
		'menu_slug'          => 'lw-settings',
		'menu_parent'        => 'edit.php?post_type=location_weather',
		'menu_type'          => 'submenu',
		'show_search'        => false,
		'show_all_options'   => false,
		'show_reset_all'     => false,
		'framework_title'    => __( 'Settings', 'location-weather' ),
		'framework_class'    => 'splw-options',
		'theme'              => 'light',
		'show_reset_section' => true,

	)
);
// Create a section.
SPLW::createSection(
	$prefix,
	array(
		'title'  => __( 'Weather API Key', 'location-weather' ),
		'icon'   => '<i class="splwp-icon-api-sett splwt-lite-tab-icon"></i>',
		'fields' => array(
			array(
				'id'         => 'lw_api_source_type',
				'class'      => 'lw_api_source_type',
				'type'       => 'button_set',
				'title'      => __( 'API Source Type', 'location-weather' ),
				'options'    => array(
					'openweather_api' => __( 'OpenWeather', 'location-weather' ),
					'weather_api'     => __( 'WeatherAPI', 'location-weather' ),
				),
				'title_info' => sprintf(
					/* translators: %1$s: modify title, %2$s: anchor tag start, %3$s: anchor tag end,%4$s: another anchor start, %5$s: another anchor end. */
					'<div class="lw-info-label">%1$s</div><div class="lw-short-content">' . __(
						'Location Weather plugin integrates with %2$sOpenWeatherMap%3$s and %4$sWeatherAPI%5$s to fetch real-time weather data. Select your preferred API source based on your requirements to ensure accurate and up-to-date weather information.',
						'location-weather'
					) . '</div>',
					__( 'API Source Type', 'location-weather' ),
					'<a href="https://openweathermap.org/" target="_blank">',
					'</a>',
					'<a href="https://www.weatherapi.com/" target="_blank">',
					'</a>'
				),
				'default'    => 'openweather_api',
			),
			array(
				'id'         => 'open-api-key',
				'type'       => 'text',
				'class'      => 'open-api-key',
				'title'      => __( 'Add Your API Key', 'location-weather' ),
				'desc'       => sprintf(
					/* translators: %1$s: anchor tag start, %2$s: anchor tag end. */
					__( '%1$sGet your API key!%2$s A newly created API key from OpenWeather takes %3$sabout 15 minutes to activate and start displaying weather data.', 'location-weather' ),
					'<a href="https://home.openweathermap.org/api_keys" target="_blank">',
					'</a>',
					'</br>'
				),
				'dependency' => array( 'lw_api_source_type', '==', 'openweather_api', true ),
			),
			array(
				'id'         => 'weather-api-key',
				'type'       => 'text',
				'class'      => 'open-api-key',
				'title'      => __( 'Add Your API Key', 'location-weather' ),
				'desc'       => sprintf(
					/* translators: %1$s: anchor tag start, %2$s: anchor tag end. */
					__( '%1$sGet your WeatherAPI key!%2$s', 'location-weather' ),
					'<a href="https://www.weatherapi.com/my/" target="_blank">',
					'</a>'
				),
				'dependency' => array( 'lw_api_source_type', '==', 'weather_api', true ),
			),
		),
	)
);

// Custom CSS Field.
SPLW::createSection(
	$prefix,
	array(
		'class'  => 'splw_advance_setting',
		'title'  => __( 'Advanced Controls', 'location-weather' ),
		'icon'   => '<i class="splwt-lite-tab-icon splwp-icon-advanced"></i>',
		'fields' => array(
			array(
				'id'         => 'splw_delete_on_remove',
				'type'       => 'checkbox',
				'title'      => __( 'Clean-up Data on Deletion', 'location-weather' ),
				'default'    => false,
				'title_info' => __( 'Check this box if you would like location weather to completely clean-up all of its data when the plugin is deleted.', 'location-weather' ),
			),
			array(
				'id'         => 'splw_skipping_cache',
				'type'       => 'switcher',
				'title'      => __( 'Skip Cache for Weather Update', 'location-weather' ),
				'default'    => false,
				'text_on'    => __( 'Enabled', 'location-weather' ),
				'text_off'   => __( 'Disabled', 'location-weather' ),
				'text_width' => '95',
				'title_info' => sprintf( '<div class="lw-info-label">%1$s</div>%2$s', __( 'Skip Cache for Weather Update', 'location-weather' ), __( 'By enabling this option, you can bypass caching mechanisms in certain plugins or themes, ensuring accurate and timely weather updates. Use this if you encounter caching-related issues.', 'location-weather' ) ),
			),
			array(
				'id'         => 'splw_enable_cache',
				'type'       => 'switcher',
				'title'      => __( 'Cache', 'location-weather' ),
				'default'    => false,
				'text_on'    => __( 'Enabled', 'location-weather' ),
				'text_off'   => __( 'Disabled', 'location-weather' ),
				'text_width' => '95',
				'title_info' => '<div class="lw-info-label">' . __( 'Cache', 'location-weather' ) . '</div>' . __( 'Set the duration for storing weather data, balancing freshness and server load. Shorter times provide more real-time data, while longer times reduce server requests.', 'location-weather' ),
			),
			array(
				'id'              => 'splw_cache_time',
				'class'           => 'splw_cache_time',
				'title'           => __( 'Cache Time', 'location-weather' ),
				'type'            => 'spacing',
				'units'           => array(
					__( 'Mins', 'location-weather' ),
				),
				'all'             => true,
				'all_icon'        => '',
				'all_placeholder' => 15,
				'default'         => array(
					'all' => '15',
				),
				'dependency'      => array( 'splw_enable_cache', '==', 'true', true ),
			),
			array(
				'id'      => 'cache_remove',
				'class'   => 'cache_remove',
				'type'    => 'button_clean',
				'options' => array(
					'' => 'Delete',
				),
				'title'   => __( 'Purge Cache', 'location-weather' ),
				'default' => false,
			),
		),
	)
);


// Custom CSS Field.
SPLW::createSection(
	$prefix,
	array(
		'id'     => 'custom_css_section',
		'title'  => __( 'Additional CSS & JS', 'location-weather' ),
		'icon'   => '<i class="splwt-lite-tab-icon splwp-icon-code"></i>',
		'fields' => array(
			array(
				'id'       => 'splw_custom_css',
				'type'     => 'code_editor',
				'title'    => __( 'Custom CSS', 'location-weather' ),
				'settings' => array(
					'mode'  => 'css',
					'theme' => 'default',
				),
			),
			array(
				'id'       => 'splw_custom_js',
				'type'     => 'code_editor',
				'title'    => __( 'Custom JS', 'location-weather' ),
				'settings' => array(
					'theme' => 'default',
					'mode'  => 'javascript',
				),
			),
		),
	)
);
