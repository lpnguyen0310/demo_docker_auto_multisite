<?php

/**
 * The Export import file of Location Weather.
 *
 * @package Location_Weather
 * @author  ShapedPlugin <support@shapedplugin.com>
 */

namespace ShapedPlugin\Weather\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Location_Weather_Import_Export' ) ) {
	/**
	 * Custom import export.
	 */
	class Location_Weather_Import_Export {

		/**
		 * Export
		 *
		 * @param  mixed $shortcode_ids Export Wp carousel shortcode ids.
		 * @return object
		 */
		public function export( $shortcode_ids ) {
			$export = array();
			if ( ! empty( $shortcode_ids ) ) {

				$post_in    = 'all_shortcodes' === $shortcode_ids ? '' : $shortcode_ids;
				$args       = array(
					'post_type'        => 'location_weather',
					'post_status'      => array( 'inherit', 'publish' ),
					'orderby'          => 'modified',
					'suppress_filters' => apply_filters( 'splw_wpml_suppress_filters', 1 ), // wpml, ignore language filter.
					'posts_per_page'   => -1,
					'post__in'         => $post_in,
				);
				$shortcodes = get_posts( $args );
				if ( ! empty( $shortcodes ) ) {
					foreach ( $shortcodes as $shortcode ) {
						$shortcode_export = array(
							'title'       => sanitize_text_field( $shortcode->post_title ),
							'original_id' => absint( $shortcode->ID ),
							'meta'        => array(),
						);
						foreach ( get_post_meta( $shortcode->ID ) as $metakey => $value ) {
							$meta_key                              = sanitize_key( $metakey );
							$meta_value                            = is_serialized( $value[0] ) ? $value[0] : sanitize_text_field( $value[0] );
							$shortcode_export['meta'][ $meta_key ] = $meta_value;
							// $shortcode_export['meta'][ $metakey ] = $value[0];
						}
						$export['shortcode'][] = $shortcode_export;
						unset( $shortcode_export );
					}
					$export['metadata'] = array(
						'version' => LOCATION_WEATHER_VERSION,
						'date'    => gmdate( 'Y/m/d' ),
					);
				}
				return $export;
			}
		}

		/**
		 * Export Wp_carousel by ajax.
		 *
		 * @return void
		 */
		public function export_shortcodes() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, 'splwt_options_nonce' ) ) {
				wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'location-weather' ) ), 401 );
			}

			// Check user capabilities.
			$_capability = apply_filters( 'splw_import_export_user_capability', 'manage_options' );
			if ( ! current_user_can( $_capability ) ) {
				wp_send_json_error( array( 'error' => esc_html__( 'You do not have permission to export.', 'location-weather' ) ) );
			}

			$shortcode_ids = '';
			if ( isset( $_POST['splw_ids'] ) ) {
				$shortcode_ids = is_array( $_POST['splw_ids'] ) ? wp_unslash( array_map( 'absint', $_POST['splw_ids'] ) ) : sanitize_text_field( wp_unslash( $_POST['splw_ids'] ) );
			}
			$export = $this->export( $shortcode_ids );
			if ( is_wp_error( $export ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html( $export->get_error_message() ),
					),
					400
				);
			}

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				// @codingStandardsIgnoreLine
				echo wp_json_encode($export, JSON_PRETTY_PRINT);
				die;
			}

			wp_send_json( $export, 200 );
		}

		/**
		 * Import Image and shortcode.
		 *
		 * @param  mixed $shortcodes Import logo and carousel shortcode array.
		 *
		 * @throws \Exception Error message.
		 * @return object
		 */
		public function import( $shortcodes ) {
			$errors = array();
			foreach ( $shortcodes as $index => $shortcode ) {
				$errors[ $index ] = array();
				$new_shortcode_id = 0;
				try {
					$new_shortcode_id = wp_insert_post(
						array(
							'post_title'  => isset( $shortcode['title'] ) ? sanitize_text_field( $shortcode['title'] ) : '',
							'post_status' => 'publish',
							'post_type'   => 'location_weather',
						),
						true
					);

					if ( is_wp_error( $new_shortcode_id ) ) {
						throw new \Exception( $new_shortcode_id->get_error_message() );
					}

					if ( isset( $shortcode['meta'] ) && is_array( $shortcode['meta'] ) ) {
						foreach ( $shortcode['meta'] as $key => $value ) {
							update_post_meta(
								$new_shortcode_id,
								$key,
								maybe_unserialize( str_replace( '{#ID#}', $new_shortcode_id, $value ) )
							);
						}
					}
				} catch ( \Exception $e ) {
					array_push( $errors[ $index ], $e->getMessage() );

					// If there was a failure somewhere, clean up.
					wp_trash_post( $new_shortcode_id );
				}

				// If no errors, remove the index.
				if ( ! count( $errors[ $index ] ) ) {
					unset( $errors[ $index ] );
				}

				// External modules manipulate data here.
				do_action( 'sp_location_weather_shortcode_imported', $new_shortcode_id );
			}

			$errors = reset( $errors );
			return isset( $errors[0] ) ? new \WP_Error( 'import_location_weather_error', $errors[0] ) : '';
		}

		/**
		 * Import WP-Carousel by ajax.
		 *
		 * @return void
		 */
		public function import_shortcodes() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, 'splwt_options_nonce' ) ) {
				wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'location-weather' ) ), 401 );
			}

			// Check user capabilities.
			$_capability = apply_filters( 'splw_import_export_user_capability', 'manage_options' );
			if ( ! current_user_can( $_capability ) ) {
				wp_send_json_error( array( 'error' => esc_html__( 'You do not have permission to import.', 'location-weather' ) ) );
			}

			// Don't worry sanitize after JSON decode below.
			// phpcs:ignore
			$data       = isset( $_POST['shortcode'] ) ? wp_kses_data( wp_unslash( $_POST['shortcode'] ) ) : '';
			if ( ! $data ) {
				wp_send_json_error(
					array(
						'message' => __( 'Nothing to import.', 'location-weather' ),
					),
					400
				);
			}

			// Decode JSON with error checking.
			$decoded_data = json_decode( $data, true );
			if ( is_string( $decoded_data ) ) {
				$decoded_data = json_decode( $decoded_data, true );
			}
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Invalid JSON data.', 'location-weather' ),
					),
					400
				);
			}

			// Check if shortcode key exists and is valid.
			if ( ! isset( $decoded_data['shortcode'] ) || ! is_array( $decoded_data['shortcode'] ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Invalid shortcode data structure.', 'location-weather' ),
					),
					400
				);
			}

			$import_value = apply_filters( 'sp_location_weather_allow_import_tags', false );
			$shortcodes   = $import_value ? $decoded_data['shortcode'] : wp_kses_post_deep( $decoded_data['shortcode'] );
			$status       = $this->import( $shortcodes );

			if ( is_wp_error( $status ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html( $status->get_error_message() ),
					),
					400
				);
			}

			wp_send_json_success( $status, 200 );
		}
	}
}
