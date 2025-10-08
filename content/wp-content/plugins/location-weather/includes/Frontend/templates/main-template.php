<?php
/**
 * Weather Main Template File
 *
 * This template displays the weather for a specific location.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// check the weather data existence.
if ( ! isset( $weather_data['temp'] ) ) {
	return;
}

require self::lw_locate_template( 'additional-icon.php' );
?>
<div id="splw-location-weather-<?php echo esc_attr( $shortcode_id ); ?>" class="splw-main-wrapper <?php echo esc_attr( $layout ); ?>" data-shortcode-id="<?php echo esc_attr( $shortcode_id ); ?>">
<?php require self::lw_locate_template( 'preloader.php' ); ?>
	<?php require self::lw_locate_template( 'section-title.php' ); ?>
	<div class="splw-lite-wrapper <?php echo esc_attr( $preloader_class ); ?>">
		<div class="splw-lite-templates-body">
			<?php
			if ( 'horizontal' === $layout ) {
				echo '<div class="lw-current-data-area">';
			}
			require self::lw_locate_template( 'template-parts/header.php' );
			require self::lw_locate_template( 'template-parts/current-weather.php' );
			if ( 'horizontal' === $layout ) {
				echo '</div>';
			}
			require self::lw_locate_template( 'template-parts/additional-data.php' );
			?>
		</div>
		<?php if ( $show_forecast && $forecast_data ) : ?>
		<div class="splw-adv-forecast-days">
			<?php
			require self::lw_locate_template( 'template-parts/forecast-header-section.php' );
			require self::lw_locate_template( 'template-parts/forecast-data.php' );
			?>
		</div>
		<?php endif; ?>
		<?php require self::lw_locate_template( 'template-parts/footer.php' ); ?>
	</div>
</div>
