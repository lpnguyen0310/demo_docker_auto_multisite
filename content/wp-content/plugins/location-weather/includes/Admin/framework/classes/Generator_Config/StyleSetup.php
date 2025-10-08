<?php
/**
 * The style setup configuration.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

SPLW::createSection(
	'sp_location_weather_generator',
	array(
		'title'  => __( 'Style Settings', 'location-weather' ),
		'icon'   => '<span><i class="splwp-icon-style"></i></span>',
		'class'  => 'splw-weather-settings-meta-box',
		'fields' => array(
			array(
				'id'         => 'lw-background-type',
				'class'      => 'splw_background_type',
				'type'       => 'button_set',
				'title_info' => sprintf( '<div class="lw-info-label">%s</div><div class="lw-short-content">%s</div><a class="lw-open-docs" href="https://locationweather.io/docs/how-to-configure-weather-details-background-type/" target="_blank">%s</a><a class="lw-open-live-demo" href="https://locationweather.io/demos/weather-background-type/" target="_blank">%s</a>', __( 'Background Type', 'location-weather' ), __( 'Customize color or add video for your weather forecast background. You can also set an automated image based on weather conditions. See how it works.', 'location-weather' ), __( 'Open Docs', 'location-weather' ), __( 'Live Demo', 'location-weather' ) ),
				'title'      => __( ' Background Type ', 'location-weather' ),
				'options'    => array(
					'solid' => __( 'Color', 'location-weather' ),
					'2'     => __( 'Weather-based Image', 'location-weather' ),
					'3'     => __( 'Video', 'location-weather' ),
				),
				'default'    => 'solid',
			),
			array(
				'id'         => 'lw-background-color-type',
				'type'       => 'button_set',
				'title'      => __( ' Color Type', 'location-weather' ),
				'class'      => 'lw-background-color-type splw-first-fields',
				'options'    => array(
					'solid'    => __( 'Solid', 'location-weather' ),
					'gradient' => __( 'Gradient', 'location-weather' ),
				),
				'title_info' => sprintf( '<div class="lw-info-label">%s</div><div class="lw-short-content">%s</div>', __( 'Color Type', 'location-weather' ), __( 'Choose a  color styles for customizing the appearance of your weather display. Solid provides a single color, while Gradient allows a blend of colors for a visually dynamic effect.', 'location-weather' ) ),
				'default'    => 'solid',
				'dependency' => array( 'lw-background-type', '==', 'solid', true ),
			),
			array(
				'id'         => 'lw-bg-solid',
				'type'       => 'color',
				'title'      => __( 'Solid Color', 'location-weather' ),
				'default'    => '#F05800',
				'dependency' => array( 'lw-background-type|lw-background-color-type', '==|==', 'solid|solid', true ),
			),
			array(
				'id'         => 'lw_content_padding',
				'type'       => 'spacing',
				'class'      => 'lw_content_padding',
				'title'      => __( 'Content Padding ', 'location-weather' ),
				'all'        => false,
				'min'        => 0,
				'max'        => 100,
				'units'      => array( 'px', '%' ),
				'default'    => array(
					'top'    => '16',
					'right'  => '20',
					'bottom' => '10',
					'left'   => '20',
				),
				'title_info' => '<div class="lw-img-tag"><img src="' . SPLW::include_plugin_url( 'assets/images/weather-content-padding.webp' ) . '" alt="weather-content-padding"></div><div class="lw-info-label img">' . __( 'Weather Content Padding', 'location-weather' ) . '</div>',
			),
			array(
				'id'      => 'lw_bg_border',
				'type'    => 'border',
				'title'   => __( 'Border', 'location-weather' ),
				'all'     => true,
				'default' => array(
					'all'   => '0',
					'style' => 'solid',
					'color' => '#e2e2e2',
				),
			),
			array(
				'id'        => 'lw_bg_border_radius',
				'type'      => 'spacing',
				'title'     => __( 'Radius', 'location-weather' ),
				'all'       => true,
				'all_title' => __( 'Radius', 'location-weather' ),
				'min'       => 0,
				'max'       => 100,
				'units'     => array( 'px', '%' ),
				'default'   => array(
					'all' => '8',
				),
			),
			array(
				'id'         => 'lw_box_shadow_type',
				'type'       => 'button_set',
				'title'      => __( 'Box-Shadow', 'location-weather' ),
				'options'    => array(
					'none'   => __( 'None', 'location-weather' ),
					'outset' => __( 'Outset', 'location-weather' ),
					'inset'  => __( 'Inset', 'location-weather' ),
				),
				'default'    => 'none',
				'dependency' => array( 'weather-view', 'any', 'vertical,horizontal', true ),
			),
			array(
				'id'         => 'weather_box_shadow',
				'type'       => 'box_shadow',
				'title'      => __( 'Box-Shadow Values', 'location-weather' ),
				'style'      => false,
				'default'    => array(
					'vertical'   => '4',
					'horizontal' => '4',
					'blur'       => '16',
					'spread'     => '0',
					'color'      => 'rgba(0,0,0,0.30)',
				),
				'dependency' => array( 'weather-view|lw_box_shadow_type', 'any|!=', 'vertical,horizontal|none', true ),
			),
			array(
				'id'         => 'lw_max_width',
				'class'      => 'lw_max_width',
				'type'       => 'spacing',
				'title'      => __( 'Weather Maximum Width', 'location-weather' ),
				'all'        => true,
				'all_icon'   => '<i class="fas fa-arrows-alt-h"></i>',
				'all_title'  => __( 'Width', 'location-weather' ),
				'min'        => 0,
				'max'        => 1920,
				'units'      => array( 'px', '%' ),
				'default'    => array(
					'all' => '320',
				),
				'title_info' => sprintf( '<div class="lw-info-label">%s</div><div class="lw-short-content">%s</div><a class="lw-open-docs" href="https://locationweather.io/docs/how-to-configure-weather-view-maximum-width/" target="_blank">%s</a>', __( 'Weather Maximum Width', 'location-weather' ), __( 'You can customize the weather widget’s maximum width to align it with your website content area perfectly.', 'location-weather' ), __( 'Open Docs', 'location-weather' ) ),
			),
			array(
				'type'    => 'notice',
				'style'   => 'normal',
				'class'   => 'notice-padding',
				/* translators: %1$s: anchor tag start, %2$s: first anchor tag end,%3$s: second anchor tag start, %4$s: second anchor tag end. */
				'content' => sprintf( __( 'To craft your desired %1$sWeather View%2$s with advanced customizations, %3$sUpgrade to Pro!%4$s', 'location-weather' ), '<a class="lw-open-live-demo" href="https://locationweather.io/demos/weather-background-type/" target="_blank"><strong>', '</strong></a>', '<a class="lw-open-live-demo" href="https://locationweather.io/pricing/?ref=1" target="_blank"><strong>', '</strong></a>' ),
			),
		),
	)
);
