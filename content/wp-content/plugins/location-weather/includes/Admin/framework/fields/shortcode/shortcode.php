<?php
/**
 * Framework shortcode field file.
 *
 * @link       https://shapedplugin.com/
 *
 * @package    Location_Weather
 * @subpackage Location_Weather/Includes/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'SPLWT_Field_shortcode' ) ) {
	/**
	 * SP_EAP_Field_shortcode
	 */
	class SPLWT_Field_shortcode extends SPLWT_Fields {

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
		 * Render
		 *
		 * @return void
		 */
		public function render() {

			// Get the Post ID.
			$post_id = get_the_ID();
			if ( ! empty( $this->field['shortcode'] ) && 'shortcode' === $this->field['shortcode'] ) {
				echo ( ! empty( $post_id ) ) ? '<div class="splw_shortcode-area"><p>To display the Location Weather, copy and paste this shortcode into your post, page, custom post, block editor, or page builder. <a href="https://locationweather.io/docs/docs/how-to-use-location-weather-shortcode-to-your-theme-files-or-php-templates/" target="_blank">Learn how</a> to include it in your template file.</p><span class="splw-code selectable">[location-weather id="' . esc_attr( $post_id ) . '"]</span></div><div class="splw-after-copy-text text-center"><i class="fa fa-check-circle"></i> Shortcode Copied to Clipboard! </div>' : '';
			} elseif ( ! empty( $this->field['shortcode'] ) && 'pro_notice' === $this->field['shortcode'] ) {
				if ( ! empty( $post_id ) ) {
					echo '<div class="splw_shortcode-area lw-pro-notice-wrapper">';
					echo '<div class="lw-pro-notice-heading">' . sprintf(
						/* translators: 1: start span tag, 2: close tag. */
						esc_html__( 'Unlock More Power with %1$sPRO%2$s', 'location-weather' ),
						'<span>',
						'</span>'
					) . '</div>';

					echo '<p class="lw-pro-notice-desc">' . sprintf(
						/* translators: 1: start bold tag, 2: close tag. */
						esc_html__( 'Help visitors with advanced weather data by %1$sLocation Weather Pro!%2$s', 'location-weather' ),
						'<b>',
						'</b>'
					) . '</p>';

					echo '<ul>';
					echo '<li><i class="splwp-icon-check-icon"></i> <a class="splwp-feature-link" href="https://locationweather.io/#weather-showcase" target="_blank">' . esc_html__( '25+ Premium Weather Templates', 'location-weather' ) . '</a></li>';
					echo '<li><i class="splwp-icon-check-icon"></i> ' . esc_html__( 'Daily & Hourly Forecasts', 'location-weather' ) . '</li>';
					echo '<li><i class="splwp-icon-check-icon"></i> ' . esc_html__( 'Interactive Radar & Weather Maps', 'location-weather' ) . '</li>';
					echo '<li><i class="splwp-icon-check-icon"></i> <a class="splwp-feature-link" href="https://locationweather.io/demos/weather-graph-chart/" target="_blank">' . esc_html__( 'Weather Graph Charts', 'location-weather' ) . '</a></li>';
					echo '<li><i class="splwp-icon-check-icon"></i> ' . esc_html__( 'Weather Data Carousel', 'location-weather' ) . '</li>';
					echo '<li><i class="splwp-icon-check-icon"></i> <a class="splwp-feature-link" href="https://locationweather.io/demos/custom-weather-search/" target="_blank">' . esc_html__( 'Custom Weather Search', 'location-weather' ) . '</a></li>';
					echo '<li><i class="splwp-icon-check-icon"></i> ' . esc_html__( '18+ Detailed Weather Metrics', 'location-weather' ) . '</li>';
					echo '<li><i class="splwp-icon-check-icon"></i> ' . esc_html__( '120+ Advanced Customizations', 'location-weather' ) . '</li>';
					echo '</ul>';

					echo '<div class="lw-pro-notice-button">';
					echo '<a class="lw-open-live-demo" href="https://locationweather.io/pricing/?ref=1" target="_blank">';
					echo esc_html__( 'Upgrade to Pro Now', 'location-weather' ) . ' <i class="splwp-icon-shuttle_2285485-1"></i>';
					echo '</a>';
					echo '</div>';
					echo '</div>';
				}
			} else {
				echo ( ! empty( $post_id ) ) ? '
				<div class="splw_shortcode-area">
					<p>
						' .
							sprintf(
								/* translators: 1: start strong tag, 2: close tag. */
								esc_html__( 'Location Weather has seamless integration with Gutenberg, Classic Editor, %1$sElementor%2$s, Divi, Bricks, Beaver, Oxygen, WPBakery Builder, etc.', 'location-weather' ),
								'<strong>',
								'</strong>'
							)
						. '
					</p>
				</div>
				' : '';
			}
		}
	}
}
