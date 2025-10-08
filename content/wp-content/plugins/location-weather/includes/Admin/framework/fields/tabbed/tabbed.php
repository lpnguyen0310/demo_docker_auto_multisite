<?php
/**
 *
 * Field: tabbed
 *
 * @package Location_weather
 * @since 1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: tabbed
 */
if ( ! class_exists( 'SPLWT_Field_tabbed' ) ) {

	class SPLWT_Field_tabbed extends SPLWT_Fields {

		/**
		 * Field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render field
		 *
		 * @return void
		 */
		public function render() {

			$unallows = array( 'tabbed' );

			echo wp_kses_post( $this->field_before() );

			echo '<div class="splwt-lite-tabbed-nav">';
			foreach ( $this->field['tabs'] as $key => $tab ) {

				$tabbed_icon   = ( ! empty( $tab['icon'] ) ) ? $tab['icon'] : '';
				$tabbed_active = ( empty( $key ) ) ? ' class="splwt-lite-tabbed-active"' : '';

				echo '<a href="#"' .wp_kses_post( $tabbed_active ) . '>' . $tabbed_icon . esc_html( $tab['title'] ) . '</a>'; // phpcs:ignore -- intentionally ignore escaped to support html.

			}
			echo '</div>';

			echo '<div class="splwt-lite-tabbed-sections">';
			foreach ( $this->field['tabs'] as $key => $tab ) {

				$tabbed_hidden = ( ! empty( $key ) ) ? ' hidden' : '';

				echo '<div class="splwt-lite-tabbed-section' . esc_attr( $tabbed_hidden ) . '">';

				foreach ( $tab['fields'] as $field ) {
					if ( in_array( $field['type'], $unallows ) ) {
						$field['_notice'] = true; }
					$field_id      = ( isset( $field['id'] ) ) ? $field['id'] : '';
					$field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';
					$field_value   = ( isset( $this->value[ $field_id ] ) ) ? $this->value[ $field_id ] : $field_default;
					$unique_id     = ( ! empty( $this->unique ) ) ? $this->unique : '';

					SPLW::field( $field, $field_value, $unique_id, 'field/tabbed' );

				}

				echo '</div>';

			}
			echo '</div>';

			echo wp_kses_post( $this->field_after() );
		}
	}
}
