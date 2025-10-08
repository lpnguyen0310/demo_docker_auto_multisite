<?php
/**
 * The admin preview.
 *
 * @link        http://shapedplugin.com/
 * @since      2.1.1
 *
 * @package    Location_Weather
 * @subpackage Location_Weather/Admin
 */

namespace ShapedPlugin\Weather\Admin\Preview;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ShapedPlugin\Weather\Frontend\Shortcode;
use ShapedPlugin\Weather\Frontend\Scripts;

/**
 * The admin preview.
 */
class LW_Preview {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.1.1
	 */
	public function __construct() {
		$this->preview_action();
	}

	/**
	 * Public Action
	 *
	 * @return void
	 */
	private function preview_action() {
		// Admin Preview.
		add_action( 'wp_ajax_sp_location_weather_preview_meta_box', array( $this, 'backend_preview' ) );
	}

	/**
	 * Function Backed preview.
	 *
	 * @since 2.1.1
	 */
	public function backend_preview() {
		$nonce = isset( $_POST['ajax_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['ajax_nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'splwt_metabox_nonce' ) ) {
			return;
		}

		$setting = array();
		$data = ! empty( $_POST['data'] ) ?  wp_unslash( $_POST['data'] )  : ''; // phpcs:ignore
		parse_str( $data, $setting );
		// Shortcode id.
		$post_id                     = $setting['post_ID'];
		$splw_meta                   = $setting['sp_location_weather_generator'];
		$layout_meta                 = $setting['sp_location_weather_layout'];
		$title                       = $setting['post_title'];
		$splw_option                 = get_option( 'location_weather_settings', true );
		$layouts                     = get_post_meta( $post_id, 'sp_location_weather_layout', true );
		$layout_meta['weather-view'] = isset( $layout_meta['weather-view'] ) && in_array( $layout_meta['weather-view'], array( 'vertical', 'horizontal' ), true ) ? $layout_meta['weather-view'] : 'vertical';
		/* Load dynamic style in the header based on found shortcode on the page. */
		$dynamic_style = Scripts::load_dynamic_style( $post_id, $splw_meta );
		echo '<style>';
		echo wp_strip_all_tags( $dynamic_style['dynamic_css'] );//phpcs:ignore
		echo '</style>';
		Shortcode::splw_html_show( $post_id, $splw_option, $splw_meta, $layout_meta );
		die();
	}
}
new LW_Preview();
