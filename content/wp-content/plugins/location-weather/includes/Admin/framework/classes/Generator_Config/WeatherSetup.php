<?php
/**
 * The weather setup configuration.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

/**
 * Set a unique slug-like ID.
 */
$splw_opts_prefix = 'sp_location_weather_generator';

/**
 * Preview metabox.
 *
 * @param string $prefix The metabox main Key.
 * @return void
 */
SPLW::createMetabox(
	'sp_location_weather_live_preview',
	array(
		'title'        => __( 'Live Preview', 'location-weather' ),
		'post_type'    => 'location_weather',
		'show_restore' => false,
		'preview'      => false,
		'context'      => 'normal',
	)
);
SPLW::createSection(
	'sp_location_weather_live_preview',
	array(
		'fields' => array(
			array(
				'type' => 'preview',
			),
		),
	)
);

$display_shortcode = 'sp_lw_shortcode';

//
// Create a metabox.
//
SPLW::createMetabox(
	$display_shortcode,
	array(
		'title'     => __( 'How To Use', 'location-weather' ),
		'post_type' => 'location_weather',
		'context'   => 'side',
	)
);

//
// Create a section.
//
SPLW::createSection(
	$display_shortcode,
	array(
		'fields' => array(
			array(
				'type'      => 'shortcode',
				'class'     => 'splw-admin-sidebar',
				'shortcode' => 'shortcode',
			),
		),
	)
);

SPLW::createMetabox(
	'sp_lw_builder_option',
	array(
		'title'            => __( 'Page Builders', 'location-weather' ),
		'post_type'        => 'location_weather',
		'context'          => 'side',
		'show_restore'     => false,
		'sp_lcp_shortcode' => false,
	)
);

SPLW::createSection(
	'sp_lw_builder_option',
	array(
		'fields' => array(
			array(
				'type'      => 'shortcode',
				'shortcode' => false,
				'class'     => 'sp_tpro-admin-sidebar',
			),
		),
	)
);

SPLW::createMetabox(
	'sp_lw_pro_notice',
	array(
		'title'            => __( 'Unlock Pro Feature', 'location-weather' ),
		'post_type'        => 'location_weather',
		'context'          => 'side',
		'show_restore'     => false,
		'sp_lcp_shortcode' => false,
	)
);

SPLW::createSection(
	'sp_lw_pro_notice',
	array(
		'fields' => array(
			array(
				'type'      => 'shortcode',
				'shortcode' => 'pro_notice',
				'class'     => 'sp_tpro-admin-sidebar',
			),
		),
	)
);

/**
 * Create metabox.
 */
SPLW::createMetabox(
	$splw_opts_prefix,
	array(
		'title'        => __( 'Location Weather Generation Options', 'location-weather' ),
		'post_type'    => 'location_weather',
		'show_restore' => true,
		'class'        => 'splw-shortcode-options',
	)
);

/**
 * Weather setting section.
 */
SPLW::createSection(
	$splw_opts_prefix,
	array(
		'title'  => __( 'Weather Settings', 'location-weather' ),
		'icon'   => '<span><i class="splwp-icon-weather-settings"></i></span>',
		'class'  => 'splw-weather-settings-meta-box',
		'fields' => array(
			array(
				'id'         => 'get-weather-by',
				'type'       => 'button_set',
				'class'      => 'splw-first-fields',
				'title'      => __( 'Display Weather For Specific Location', 'location-weather' ),
				/* translators: %2$s: strong tag start, %3$s: strong tag end. */
				'title_info' => sprintf( '<div class="lw-info-label">%1$s</div><div class="lw-short-content">' . __( 'Choose an option from the City Name, City ID, ZIP Code, and Geo Coordinates to display the weather of a%2$s specific location.%3$s', 'location-weather' ) . '</div> <a class="lw-open-docs" href="https://locationweather.io/docs/how-to-display-weather-details-for-a-specific-location/" target="_blank">%4$s</a>', __( 'Display Weather For Specific Location', 'location-weather' ), '<strong>', '</strong>', __( 'Open Docs', 'location-weather' ) ),
				'options'    => array(
					'city_name' => __( 'City Name', 'location-weather' ),
					'city_id'   => __( 'City ID', 'location-weather' ),
					'zip'       => __( 'ZIP Code', 'location-weather' ),
					'latlong'   => __( 'Coordinates', 'location-weather' ),
				),
				'default'    => 'city_name',
			),
			array(
				'id'          => 'lw-city-name',
				'type'        => 'text',
				'class'       => 'splw-text-fields',
				'title'       => __( 'Enter City Name', 'location-weather' ),
				'placeholder' => __( 'London, GB', 'location-weather' ),
				'desc'        => __( 'Write your city name and country code only.', 'location-weather' ),
				'dependency'  => array( 'get-weather-by', '==', 'city_name' ),
			),
			array(
				'id'          => 'lw-city-id',
				'type'        => 'text',
				'class'       => 'splw-text-fields',
				'title'       => __( 'Enter City ID', 'location-weather' ),
				'title_info'  => sprintf( '<div class="lw-info-label">%1$s</div> <div class="lw-short-content">%2$s </div> <a class="lw-open-docs" href="https://locationweather.io/docs/how-to-display-weather-details-with-city-id/" target="_blank">%3$s</a>', __( 'City ID', 'location-weather' ), __( 'Set your city ID.', 'location-weather' ), __( 'Open Docs', 'location-weather' ) ),
				'placeholder' => __( '2643743', 'location-weather' ),
				/* translators: %1$s: anchor tag start, %2$s: first anchor tag end. */
				'desc'        => sprintf( __( 'Set your city ID. %1$sGet city ID%2$s', 'location-weather' ), '<a href="https://openweathermap.org/find" target="_blank">', '</a>' ),
				'dependency'  => array( 'get-weather-by|lw-location-from-custom-fields', '==|!=', 'city_id|true' ),
			),
			array(
				'id'          => 'lw-zip',
				'type'        => 'text',
				'class'       => 'splw_custom_fields splw-text-fields',
				'title'       => __( 'Enter ZIP Code', 'location-weather' ),
				'placeholder' => __( '77070, US', 'location-weather' ),
				/* translators: %1$s: anchor tag start, %2$s: first anchor tag end. */
				'desc'        => sprintf( __( 'Set your zip code and country code. See %1$s instructions %2$s', 'location-weather' ), '<a href="https://locationweather.io/docs/how-to-display-weather-details-with-zip-code/" target="_blank">', '</a>' ),
				'dependency'  => array( 'get-weather-by|lw-location-from-custom-fields', '==|!=', 'zip|true' ),
			),
			array(
				'id'          => 'lw-latlong',
				'type'        => 'text',
				'class'       => 'splw_custom_fields splw-text-fields',
				'title'       => __( 'Enter Geo Coordinates', 'location-weather' ),
				'placeholder' => __( '51.509865,-0.118092', 'location-weather' ),
				'desc'        => sprintf( '%s <a href="https://www.latlong.net/" target="_blank">%s</a>', __( 'Set coordinates (latitude & longitude).', 'location-weather' ), __( 'Get coordinates', 'location-weather' ) ),
				'dependency'  => array( 'get-weather-by|lw-location-from-custom-fields', '==|!=', 'latlong|true' ),
			),
			array(
				'id'         => 'lw-custom-name',
				'type'       => 'text',
				'title'      => __( 'Custom Location Name', 'location-weather' ),
				'title_info' => sprintf(
					/* translators: %3$s: strong tag start, %4$s: strong tag end. */
					'<div class="lw-info-label">%1$s</div><div class="lw-short-content">' . __( 'Set your own or custom location name that will be displayed in the weather widget if needed. This will %2$s replace or override%3$s the actual name of the city.', 'location-weather' ) . '</div> <a class="lw-open-docs" href="https://locationweather.io/docs/how-to-set-custom-location-name/" target="_blank">%4$s</a>',
					__( 'Custom Location Name', 'location-weather' ),
					'<strong>',
					'</strong>',
					__( 'Open Docs', 'location-weather' )
				),
			),
			array(
				'id'         => 'lw-location-from-custom-fields',
				'class'      => 'lw-location-from-custom-fields',
				'type'       => 'checkbox',
				'title'      => __( 'Location from Custom Fields', 'location-weather' ),
				'title_info' => sprintf( '<div class="lw-info-label">%1$s</div> <div class="lw-short-content">%2$s</div> <a class="lw-open-docs" href="https://locationweather.io/docs/how-to-display-weather-details-from-a-custom-field/" target="_blank">%3$s</a>', __( 'Location from Custom Fields (Pro)', 'location-weather' ), __( 'Check it to display the weather of a location from custom fields of any post type.', 'location-weather' ), __( 'Open Docs', 'location-weather' ) ),
				'default'    => false,
			),
			array(
				'id'         => 'lw-visitors-location',
				'type'       => 'switcher',
				'class'      => 'splw_show_hide auto-location',
				'title'      => __( 'Display Weather For Visitors Location (Auto Detect)', 'location-weather' ),
				'title_info' => sprintf( '<div class="lw-info-label">' . __( 'Display Weather For Visitors Location (Auto Detect)', 'location-weather' ) . '</div> <div class="lw-short-content">' . __( 'If you enable this option, the widget will automatically determine where your visitor is by their IP address and will display the correct weather of the visitors’ location.', 'location-weather' ) . '</div> <a class="lw-open-docs" href="https://locationweather.io/docs/how-to-display-weather-details-for-visitors-location/" target="_blank">' . __( 'Open Docs', 'location-weather' ) . '</a><a class="lw-open-live-demo" href="https://locationweather.io/demos/auto-detect-visitors-location/" target="_blank">' . __( 'Live Demo', 'location-weather' ) . '</a>' ),
				'text_on'    => __( 'Enabled', 'location-weather' ),
				'text_off'   => __( 'Disabled', 'location-weather' ),
				'text_width' => 99,
				'default'    => false,
			),
			array(
				'id'         => 'lw-custom-weather-search',
				'type'       => 'switcher',
				'title'      => __( 'Custom Weather Search', 'location-weather' ),
				'class'      => 'splw_show_hide auto-location',
				'title_info' => sprintf(
					'<div class="lw-info-label">%1$s</div> <div class="lw-short-content">%2$s</div> %3$s',
					__( 'Custom Weather Search', 'location-weather' ),
					__(
						'Help visitors search for weather data by entering the desired city or location name to view the latest weather conditions.',
						'location-weather'
					),
					'<a class="lw-open-live-demo" href="https://locationweather.io/demos/custom-weather-search/" target="_blank">' . __( 'Live Demo', 'location-weather' ) . '</a>'
				),
				'text_on'    => __( 'Enabled', 'location-weather' ),
				'text_off'   => __( 'Disabled', 'location-weather' ),
				'text_width' => 99,
				'default'    => false,
			),
			array(
				'id'    => 'lw-measurement-units-heading',
				'type'  => 'subheading',
				'title' => __( 'Measurement Units', 'location-weather' ),
			),
			array(
				'id'         => 'lw-units',
				'class'      => 'splw_custom_button_fields lw-units-desc',
				'type'       => 'select',
				'title'      => __( 'Display Temperature Unit', 'location-weather' ),
				'title_info' => sprintf( '<div class="lw-info-label">%1$s</div> <div class="lw-short-content">%2$s </div> <a class="lw-open-docs" href="https://locationweather.io/docs/how-to-configure-and-display-temperature-unit/" target="_blank">%3$s</a>', __( 'Display Temperature Unit', 'location-weather' ), __( 'Choose temperature unit(s) based on your visitor’s preferences.', 'location-weather' ), __( 'Open Docs', 'location-weather' ) ),
				'options'    => array(
					'metric'    => __( 'Celsius (°C)', 'location-weather' ),
					'imperial'  => __( 'Fahrenheit (°F) ', 'location-weather' ),
					'auto_temp' => __( 'Auto (°C or °F)', 'location-weather' ),
					'auto'      => __( 'Both (°C & °F)', 'location-weather' ),
					'none'      => __( 'Degree Symbol (°)', 'location-weather' ),
				),
				'default'    => 'metric',
				'desc'       => sprintf(
					/* translators: 1: start link tag, 2: close tag. */
					__( 'This is a %1$sPro feature!%2$s', 'location-weather' ),
					'<a class="lw-open-live-demo" href="https://locationweather.io/pricing/?ref=1" target="_blank">',
					'</a>'
				),
			),
			array(
				'id'         => 'active-lw-units',
				'type'       => 'button_set',
				'class'      => 'splw-active-lw-units',
				'title'      => __( 'Active Temperature Unit', 'location-weather' ),
				'title_info' => sprintf( '<div class="lw-info-label">%1$s</div> <div class="lw-short-content">%2$s <strong>%3$s</strong>, %4$s</div>', __( 'Active Temperature Unit', 'location-weather' ), __( 'Set an active temperature unit that will remain selected on the front end. If you select', 'location-weather' ), __( 'Auto', 'location-weather' ), __( 'it will detect the visitor’s location and show the preferred unit of that location selected automatically.', 'location-weather' ) ),
				'options'    => array(
					'metric'   => __( '°C ', 'location-weather' ),
					'imperial' => __( '°F ', 'location-weather' ),
					'auto'     => __( 'Auto', 'location-weather' ),
				),
				'desc'       => sprintf(
					/* translators: 1: start link tag, 2: close tag. */
					__( 'This is a %1$sPro feature!%2$s', 'location-weather' ),
					'<a class="lw-open-live-demo" href="https://locationweather.io/pricing/?ref=1" target="_blank">',
					'</a>'
				),
				'default'    => 'auto',
				'dependency' => array( 'lw-units', 'any', 'auto,none', true ),
			),
			array(
				'id'         => 'lw-pressure-unit',
				'class'      => 'splw_pressure_unit',
				'type'       => 'select',
				'title'      => __( 'Pressure Unit ', 'location-weather' ),
				'title_info' => sprintf( '<div class="lw-info-label">%1$s</div> <div class="lw-short-content">%2$s</div>', __( 'Atmospheric or Air Pressure Unit', 'location-weather' ), __( 'Select an atmospheric or air pressure unit.', 'location-weather' ) ),
				'options'    => array(
					'mb'  => __( 'Millibars (mb)', 'location-weather' ),
					'hpa' => __( 'Hectopascals (hPa)', 'location-weather' ),
					'1'   => __( 'kilopascal (kPa) (Pro)', 'location-weather' ),
					'2'   => __( 'Inches of Mercury (inHg) (Pro)', 'location-weather' ),
					'3'   => __( 'Pounds per Square Inch (psi) (Pro)', 'location-weather' ),
					'4'   => __( 'Millimeters of Mercury (mmHg / Torr) (Pro)', 'location-weather' ),
					'5'   => __( 'Kilogram per Square centimeter (kg/cm²) (Pro)', 'location-weather' ),
				),
				'default'    => 'mb',
			),
			array(
				'id'      => 'lw-precipitation-unit',
				'class'   => 'splw_precipitation_unit',
				'type'    => 'select',
				'title'   => __( ' Precipitation Unit ', 'location-weather' ),
				'options' => array(
					'mm'   => __( 'Millimeters (mm) (Pro)', 'location-weather' ),
					'inch' => __( 'Inches (inch) (Pro)', 'location-weather' ),
				),
				'default' => 'mm',
			),
			array(
				'id'      => 'lw-wind-speed-unit',
				'class'   => 'splw_wind_speed_unit',
				'type'    => 'select',
				'title'   => __( ' Wind Speed Unit ', 'location-weather' ),
				'options' => array(
					'mph' => __( 'Miles per hour (mph)', 'location-weather' ),
					'kmh' => __( 'Kilometer per hour (km/h)', 'location-weather' ),
					'3'   => __( 'Meter per second (m/s) (Pro)', 'location-weather' ),
					'4'   => __( 'Knot (kn) (Pro)', 'location-weather' ),

				),
				'default' => 'mph',
			),
			array(
				'id'      => 'lw-visibility-unit',
				'type'    => 'select',
				'title'   => __( ' Visibility Unit ', 'location-weather' ),
				'options' => array(
					'km' => __( 'Kilometers', 'location-weather' ),
					'mi' => __( 'Miles', 'location-weather' ),
				),
				'default' => 'km',
			),
			// Samples.
		),
	)
);
