<?php
/**
 * Weather Header Template
 *
 * This template displays the weather title and date-time for a specific location.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$city_name             = ! empty( $custom_name ) ? $custom_name : $weather_data['city'] . ', ' . $weather_data['country'];
$separator             = ', ';
$show_location_address = isset( $splw_meta['lw-location-address'] ) ? $splw_meta['lw-location-address'] : true;
?>
<?php if ( is_array( $weather_data ) && ( $show_location_address || $show_time || $show_date ) ) : ?>
<div class="splw-lite-header">
	<div class="splw-lite-header-title-wrapper">
		<?php if ( $show_location_address ) : ?>
		<div class="splw-lite-header-title">
			<?php echo esc_html( $city_name ); ?>
		</div>
		<?php endif; ?><!-- area end -->
		<!-- Current Date Time area start -->
		<?php if ( $show_time || $show_date ) : ?>
			<div class="splw-lite-current-time">
				<?php if ( $show_time ) : ?>
				<span class="lw-time"><?php echo esc_html( $weather_data['time'] . $separator ); ?> </span>
				<?php endif; ?><!-- time area end -->
				<?php if ( $show_date ) : ?>
				<span class="lw-date"><?php echo esc_html( $weather_data['date'] ); ?></span>
				<?php endif; ?><!-- date area end -->
			</div>
		<?php endif; ?><!-- date time area end -->
	</div>
</div>
<?php endif; ?><!-- full area end -->
