<?php
/**
 * Weather Forecast Template
 *
 * This template displays the weather forecast header for a specific location.
 *
 * This template can be overridden by copying it to yourtheme/location-weather/templates/template-parts/forecast-header-section.php
 *
 * @since      2.4.0
 * @version    2.0.18
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$labels = array(
	'temperature'   => __( 'Temperature', 'location-weather' ),
	'humidity'      => __( 'Humidity', 'location-weather' ),
	'pressure'      => __( 'Pressure', 'location-weather' ),
	'wind'          => __( 'Wind', 'location-weather' ),
	'precipitation' => __( 'Precipitation', 'location-weather' ),
	'rainchance'    => __( 'Rain Chance', 'location-weather' ),
	'snow'          => __( 'Snow', 'location-weather' ),
);
?>

<div class="splw-forecast-header-area">
	<?php if ( 'horizontal' === $layout && ! wp_is_mobile() ) : ?>
		<ul class="splw-tabs">
			<?php
			$first_iteration = true;
			foreach ( $forecast_data_sortable as $key => $value ) :
				if ( $value ) :
					$label = $labels[ $key ] ?? ucfirst( $key );
					?>
					<li data-tab-target="#<?php echo esc_attr( $key ); ?>" class="splw-tab<?php echo $first_iteration ? ' active' : ''; ?>">
						<?php echo esc_html( $label ); ?>
					</li>
					<?php $first_iteration = false; ?>
					<?php
				endif;
			endforeach;
			?>
		</ul>
	<?php else : ?>
		<div class="days">
			<span class="hourly-forecast-title"><?php echo esc_html( $hourly_forecast_section_title ); ?></span>
		</div>
		<div class="splw-forecast-weather">
			<div class="lw-select-arrow"><i class="splwp-icon-chevron"></i></div>
			<select id="forecast-select">
				<?php
				foreach ( $forecast_data_sortable as $key => $value ) :
					if ( $value ) :
						$label           = $labels[ $key ] ?? ucfirst( $key );
						$value_attribute = 'temperature' === $key ? 'temp' : $key;
						?>
						<option value="<?php echo esc_attr( $value_attribute ); ?>">
							<?php echo esc_html( $label ); ?>
						</option>
						<?php
					endif;
				endforeach;
				?>
			</select>
		</div>
	<?php endif; ?>
</div>
