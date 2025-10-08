<?php
/**
 * Daily Weather Details Template
 *
 * This template displays the daily weather details for a specific location.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Wind direction variables.
$active_additional_data_layout_class = 'vertical' === $layout ? ' lw-' . $active_additional_data_layout : '';

// Check if weather details should be displayed.
$show_weather_details = $show_humidity || $show_pressure || $show_wind || $show_wind_gusts || $show_visibility || $show_sunrise_sunset || $show_clouds;

if ( $show_weather_details ) :
	?>
<div
	class="splw-lite-daily-weather-details <?php echo esc_attr( $active_additional_data_layout_class ); ?> ">
	<div class="splw-weather-details splw-other-opt">
	<?php if ( $show_weather_icon_wrapper ) : ?>
		<div class="splw-weather-icons icons_splw">
		<?php endif; ?>
			<!-- humidity area start -->
			<?php if ( $show_humidity ) : ?>
			<div class="splw-icon-humidity">
				<span class="lw-title-wrapper">
					<?php echo $humidity_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output safely escaped ?>
					<span class="options-title"><?php echo esc_html( $humidity_title ); ?></span>
				</span>
				<span class="options-value">
					<?php echo esc_html( $weather_data['humidity'] ); ?>
				</span>
			</div>
			<?php endif; ?>
			<!-- humidity area end -->
			<!-- pressure area start -->
			<?php if ( $show_pressure ) : ?>
			<div class="splw-icon-pressure">
				<span class="lw-title-wrapper">
					<?php echo $pressure_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output safely escaped ?>
					<span class="options-title"><?php echo esc_html( $pressure_title ); ?></span>
				</span>
				<span class="options-value">
					<?php echo esc_html( $weather_data['pressure'] ); ?>
				</span>
			</div>
			<?php endif; ?>
			<!-- pressure area end -->
			<!-- wind area start -->
			<?php if ( $show_wind ) : ?>
			<div class="splw-icon-wind">
				<span class="lw-title-wrapper">
					<?php echo $wind_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output safely escaped ?>
					<span class="options-title"><?php echo esc_html( $wind_title ); ?></span>
				</span>
				<span class="options-value">
					<?php echo esc_html( $weather_data['wind'] ); ?>
				</span>
			</div>
			<?php endif; ?>
			<!-- wind area end -->
			<?php if ( $show_weather_icon_wrapper ) : ?>
				</div>
		<?php endif; ?>
		<!-- Wind guest area start -->
		<?php if ( $show_wind_gusts ) : ?>
		<div class="splw-gusts-wind">
			<span class="lw-title-wrapper">
				<?php echo $wind_gust_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output safely escaped ?>
				<span class="options-title"><?php echo esc_html( $wind_gust_title ); ?></span>
			</span>
			<span class="options-value">
				<?php echo esc_html( $weather_data['gust'] ); ?>
			</span>
		</div>
		<?php endif; ?>
		<!-- Wind guest area end -->
		<!-- Clouds area start -->
		<?php if ( $show_clouds ) : ?>
		<div class="splw-clouds">
			<span class="lw-title-wrapper">
				<?php echo $clouds_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output safely escaped ?>
				<span class="options-title"><?php echo esc_html( $clouds_title ); ?></span>
			</span>
			<span class="options-value"><?php echo esc_html( $weather_data['clouds'] ); ?></span>
		</div>
		<?php endif; ?>
		<!-- Clouds area end -->
		<!-- Visibility area start -->
		<?php if ( $show_visibility ) : ?>
		<div class="splw-visibility">
			<span class="lw-title-wrapper">
				<?php echo $visibility_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output safely escaped ?>
				<span class="options-title"><?php echo esc_html( $visibility_title ); ?></span>
			</span>
			<span class="options-value"><?php echo esc_html( $weather_data['visibility'] ); ?></span>
		</div>
		<?php endif; ?>
		<!-- Visibility area end -->
		<?php if ( $show_sunrise_sunset ) : ?>
		<div class="splw-sunrise">
			<span class="lw-title-wrapper">
				<?php echo $sunrise_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output safely escaped ?>

				<span class="options-title"><?php echo esc_html( $sunrise_title ); ?></span>
			</span>
			<span class="options-value"><?php echo esc_html( $weather_data['sunrise_time'] ); ?></span>
		</div>
		<div class="splw-sunset">
			<span class="lw-title-wrapper">
				<?php echo $sunset_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output safely escaped ?>
				<span class="options-title"><?php echo esc_html( $sunset_title ); ?></span>
			</span>
			<span class="options-value"><?php echo esc_html( $weather_data['sunset_time'] ); ?></span>
		</div>
		<?php endif; ?>
	</div>
</div>
	<?php
endif;
