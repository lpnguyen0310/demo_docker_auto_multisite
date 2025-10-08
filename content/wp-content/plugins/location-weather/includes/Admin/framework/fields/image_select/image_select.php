<?php
/**
 * Framework image select fields.
 *
 * @link       https://shapedplugin.com/
 * @since      2.1.1
 *
 * @package    Location_Weather_Pro
 * @subpackage Location_Weather_Pro/Includes/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'SPLWT_Field_image_select' ) ) {

	/**
	 *
	 * Field: image_select
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class SPLWT_Field_image_select extends SPLWT_Fields {
		/**
		 * Constructor function.
		 *
		 * @param array  $field field.
		 * @param string $value field value.
		 * @param string $unique field unique.
		 * @param string $where field where.
		 * @param string $parent field parent.
		 * @since 2.0
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}
		/**
		 * Render
		 *
		 * @return void
		 */
		public function render() {

			$args = wp_parse_args(
				$this->field,
				array(
					'multiple' => false,
					'inline'   => false,
					'options'  => array(),
				)
			);

			$inline = ( $args['inline'] ) ? ' splwt-lite--inline-list' : '';

			$value = ( is_array( $this->value ) ) ? $this->value : array_filter( (array) $this->value );

			echo wp_kses_post( $this->field_before() );

			if ( ! empty( $args['options'] ) ) {

				echo '<div class="splwt-lite-siblings splwt-lite--image-group' . esc_attr( $inline ) . '" data-multiple="' . esc_attr( $args['multiple'] ) . '">';

				$num = 1;

				foreach ( $args['options'] as $key => $option ) {

					$type          = ( $args['multiple'] ) ? 'checkbox' : 'radio';
					$extra         = ( $args['multiple'] ) ? '[]' : '';
					$active        = ( in_array( $key, $value ) ) ? ' splwt-lite--active' : '';
					$pro_only      = ( isset( $option['pro_only'] ) && $option['pro_only'] ) ? ' splwt-lite-pro-only' : '';
					$checked       = ( in_array( $key, $value ) ) ? ' checked' : '';
					$disable_field = ( isset( $option['pro_only'] ) && $option['pro_only'] ) ? ' disabled' : '';

					echo '<div class="splwt-lite--sibling splwt-lite--image' . esc_attr( $active . $pro_only ) . '">';
					echo '<figure>';
					if ( isset( $option['name'] ) ) {
						echo '<img src="' . esc_url( $option['image'] ) . '" alt="img-' . esc_attr( $num++ ) . '" />';
					} else {
						echo '<img src="' . esc_url( $option ) . '" alt="img-' . esc_attr( $num++ ) . '" />';
					}
					echo '<input ' . $disable_field . ' type="' . esc_attr( $type ) . '" name="' . esc_attr( $this->field_name( $extra ) ) . '" value="' . esc_attr( $key ) . '"' . $this->field_attributes() . esc_attr( $checked ) . '/>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '</figure>';
					if ( isset( $option['name'] ) && ! isset( $option['option_demo_url'] ) ) {
						echo '<p class="text-center">' . esc_attr( $option['name'] ) . '</p>';
					}
					if ( isset( $option['option_demo_url'] ) ) {
						echo '<p class="text-center">' . esc_html( $option['name'] ) . '<a href="' . esc_url( $option['option_demo_url'] ) . '" tooltip="Demo" class="splw-live-demo-icon" target="_blank"><i class="splwp-icon-external-link"></i></a></p>';
					}
					echo '</div>';

				}

				echo '</div>';

			}

			echo wp_kses_post( $this->field_after() );
		}
	}
}
