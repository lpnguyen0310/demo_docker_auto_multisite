<?php
/**
 * This is to plugin help page.
 *
 * @package location-weather
 */

namespace ShapedPlugin\Weather\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * The help page handler class.
 */
class Splw_Help {

	/**
	 * The instance of the class.
	 *
	 * @var object
	 */
	private static $_instance;

	/**
	 * Plugins Path variable.
	 *
	 * @var array
	 */
	protected static $plugins = array(
		'woo-product-slider'             => 'main.php',
		'gallery-slider-for-woocommerce' => 'woo-gallery-slider.php',
		'post-carousel'                  => 'main.php',
		'easy-accordion-free'            => 'plugin-main.php',
		'logo-carousel-free'             => 'main.php',
		'location-weather'               => 'main.php',
		'woo-quickview'                  => 'woo-quick-view.php',
		'wp-expand-tabs-free'            => 'plugin-main.php',

	);

	/**
	 * Welcome pages
	 *
	 * @var array
	 */
	public $pages = array(
		'splw_help',
	);

	/**
	 * Not show this plugin list.
	 *
	 * @var array
	 */
	protected static $not_show_plugin_list = array( 'aitasi-coming-soon', 'latest-posts', 'widget-post-slider', 'easy-lightbox-wp', 'location-weather' );

	/**
	 * The Constructor of the class.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'help_page' ), 80 );
		add_action( 'admin_print_scripts', array( $this, 'disable_admin_notices' ) );
	}

	/**
	 * The instance function of the class.
	 *
	 * @return object
	 */
	public static function getInstance() {
		if ( ! self::$_instance ) {
			self::$_instance = new Splw_Help();
		}

		return self::$_instance;
	}

	/**
	 * Add SubMenu Page
	 */
	public function help_page() {
		add_submenu_page(
			'edit.php?post_type=location_weather',
			__( 'Location Weather', 'location-weather' ),
			'Recommended',
			'manage_options',
			'edit.php?post_type=location_weather&page=splw_help#recommended'
		);
		add_submenu_page(
			'edit.php?post_type=location_weather',
			__( 'Location Weather', 'location-weather' ),
			'Lite vs Pro',
			'manage_options',
			'edit.php?post_type=location_weather&page=splw_help#lite-to-pro'
		);
		add_submenu_page( 'edit.php?post_type=location_weather', __( 'Location Weather Help', 'location-weather' ), __( 'Get Help', 'location-weather' ), 'manage_options', 'splw_help', array( $this, 'help_page_callback' ) );
	}

	/**
	 * Splw_ajax_help_page function.
	 *
	 * @return void
	 */
	public function splw_plugins_info_api_help_page() {
		$plugins_arr = get_transient( 'splw_plugins' );
		if ( false === $plugins_arr ) {
			$args    = (object) array(
				'author'   => 'shapedplugin',
				'per_page' => '120',
				'page'     => '1',
				'fields'   => array(
					'slug',
					'name',
					'version',
					'downloaded',
					'active_installs',
					'last_updated',
					'rating',
					'num_ratings',
					'short_description',
					'author',
					'icons',
				),
			);
			$request = array(
				'action'  => 'query_plugins',
				'timeout' => 30,
				'request' => serialize( $args ),
			);
			// https://codex.wordpress.org/WordPress.org_API.
			$url      = 'http://api.wordpress.org/plugins/info/1.0/';
			$response = wp_remote_post( $url, array( 'body' => $request ) );

			if ( ! is_wp_error( $response ) ) {

				$plugins_arr = array();
				$plugins     = unserialize( $response['body'] );

				if ( isset( $plugins->plugins ) && ( count( $plugins->plugins ) > 0 ) ) {
					foreach ( $plugins->plugins as $pl ) {
						if ( ! in_array( $pl->slug, self::$not_show_plugin_list, true ) ) {
							$plugins_arr[] = array(
								'slug'              => $pl->slug,
								'name'              => $pl->name,
								'version'           => $pl->version,
								'downloaded'        => $pl->downloaded,
								'active_installs'   => $pl->active_installs,
								'last_updated'      => strtotime( $pl->last_updated ),
								'rating'            => $pl->rating,
								'num_ratings'       => $pl->num_ratings,
								'short_description' => $pl->short_description,
								'icons'             => $pl->icons['2x'],
							);
						}
					}
				}

				set_transient( 'splw_plugins', $plugins_arr, 24 * HOUR_IN_SECONDS );
			}
		}

		if ( is_array( $plugins_arr ) && ( count( $plugins_arr ) > 0 ) ) {
			array_multisort( array_column( $plugins_arr, 'active_installs' ), SORT_DESC, $plugins_arr );

			foreach ( $plugins_arr as $plugin ) {
				$plugin_slug = $plugin['slug'];
				$plugin_icon = $plugin['icons'];
				if ( isset( self::$plugins[ $plugin_slug ] ) ) {
					$plugin_file = self::$plugins[ $plugin_slug ];
				} else {
					$plugin_file = $plugin_slug . '.php';
				}
				// Skip the plugin if it is not installed.
				if ( 'location-weather' === $plugin_slug ) {
					continue;
				}

				$details_link = network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] . '&amp;TB_iframe=true&amp;width=745&amp;height=550' );
				?>
				<div class="plugin-card <?php echo esc_attr( $plugin_slug ); ?>" id="<?php echo esc_attr( $plugin_slug ); ?>">
					<div class="plugin-card-top">
						<div class="name column-name">
							<h3>
								<a class="thickbox" title="<?php echo esc_attr( $plugin['name'] ); ?>" href="<?php echo esc_url( $details_link ); ?>">
									<?php echo esc_html( $plugin['name'] ); ?>
									<img src="<?php echo esc_url( $plugin_icon ); ?>" class="plugin-icon"/>
								</a>
							</h3>
						</div>
						<div class="action-links">
							<ul class="plugin-action-buttons">
								<li>
						<?php
						if ( $this->is_plugin_installed( $plugin_slug, $plugin_file ) ) {
							if ( $this->is_plugin_active( $plugin_slug, $plugin_file ) ) {
								?>
										<button type="button" class="button button-disabled" disabled="disabled">Active</button>
									<?php
							} else {
								?>
											<a href="<?php echo esc_url( $this->activate_plugin_link( $plugin_slug, $plugin_file ) ); ?>" class="button button-primary activate-now">
									<?php esc_html_e( 'Activate', 'location-weather' ); ?>
											</a>
									<?php
							}
						} else {
							?>
										<a href="<?php echo esc_url( $this->install_plugin_link( $plugin_slug ) ); ?>" class="button install-now">
								<?php esc_html_e( 'Install Now', 'location-weather' ); ?>
										</a>
								<?php } ?>
								</li>
								<li>
									<?php /* translators: %s: plugin name */ ?>
									<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal" aria-label="<?php echo esc_attr( sprintf( esc_html__( 'More information about %s', 'location-weather' ), $plugin['name'] ) ); ?>" title="<?php echo esc_attr( $plugin['name'] ); ?>">
								<?php esc_html_e( 'More Details', 'location-weather' ); ?>
									</a>
								</li>
							</ul>
						</div>
						<div class="desc column-description">
							<p><?php echo esc_html( isset( $plugin['short_description'] ) ? $plugin['short_description'] : '' ); ?></p>
						</div>
					</div>
					<?php
					echo '<div class="plugin-card-bottom">';

					if ( isset( $plugin['rating'], $plugin['num_ratings'] ) ) {
						?>
						<div class="vers column-rating">
							<?php
							wp_star_rating(
								array(
									'rating' => $plugin['rating'],
									'type'   => 'percent',
									'number' => $plugin['num_ratings'],
								)
							);
							?>
							<span class="num-ratings">(<?php echo esc_html( number_format_i18n( $plugin['num_ratings'] ) ); ?>)</span>
						</div>
						<?php
					}
					if ( isset( $plugin['version'] ) ) {
						?>
						<div class="column-updated">
							<strong><?php esc_html_e( 'Version:', 'location-weather' ); ?></strong>
							<span><?php echo esc_html( $plugin['version'] ); ?></span>
						</div>
							<?php
					}

					if ( isset( $plugin['active_installs'] ) ) {
						?>
						<div class="column-downloaded">
						<?php echo esc_html( number_format_i18n( $plugin['active_installs'] ) ) . esc_html__( '+ Active Installations', 'location-weather' ); ?>
						</div>
									<?php
					}

					if ( isset( $plugin['last_updated'] ) ) {
						?>
						<div class="column-compatibility">
							<strong><?php esc_html_e( 'Last Updated:', 'location-weather' ); ?></strong>
							<span>
							<?php
							printf(
								/* translators: %s: time ago */
								esc_html__( '%s ago', 'location-weather' ),
								esc_html( human_time_diff( $plugin['last_updated'] ) )
							);
							?>
						</span>
						</div>
									<?php
					}

					echo '</div>';
					?>
				</div>
				<?php
			}
		}
	}

	/**
	 * Check plugins installed function.
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return boolean
	 */
	public function is_plugin_installed( $plugin_slug, $plugin_file ) {
		return file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Check active plugin function
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return boolean
	 */
	public function is_plugin_active( $plugin_slug, $plugin_file ) {
		return is_plugin_active( $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Install plugin link.
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @return string
	 */
	public function install_plugin_link( $plugin_slug ) {
		return wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
	}

	/**
	 * Active Plugin Link function
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return string
	 */
	public function activate_plugin_link( $plugin_slug, $plugin_file ) {
		return wp_nonce_url( admin_url( 'edit.php?post_type=location_weather&page=splw_help&action=activate&plugin=' . $plugin_slug . '/' . $plugin_file . '#recommended' ), 'activate-plugin_' . $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Making page as clean as possible
	 */
	public function disable_admin_notices() {

		global $wp_filter;

		if ( isset( $_GET['post_type'] ) && isset( $_GET['page'] ) && 'location_weather' === wp_unslash( $_GET['post_type'] ) && in_array( wp_unslash( $_GET['page'] ), $this->pages ) ) { // @codingStandardsIgnoreLine

			if ( isset( $wp_filter['user_admin_notices'] ) ) {
				unset( $wp_filter['user_admin_notices'] );
			}
			if ( isset( $wp_filter['admin_notices'] ) ) {
				unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
				unset( $wp_filter['all_admin_notices'] );
			}
		}
	}

	/**
	 * Help Page Callback
	 */
	public function help_page_callback() {
		add_thickbox();

		$action   = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		$plugin   = isset( $_GET['plugin'] ) ? sanitize_text_field( wp_unslash( $_GET['plugin'] ) ) : '';
		$_wpnonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( isset( $action, $plugin ) && ( 'activate' === $action ) && wp_verify_nonce( $_wpnonce, 'activate-plugin_' . $plugin ) ) {
			activate_plugin( $plugin, '', false, true );
		}

		if ( isset( $action, $plugin ) && ( 'deactivate' === $action ) && wp_verify_nonce( $_wpnonce, 'deactivate-plugin_' . $plugin ) ) {
			deactivate_plugins( $plugin, '', false, true );
		}

		?>
		<div class="sp-location-weather-help">
			<!-- Header section start -->
			<section class="splw__help header">
				<div class="splw-header-area-top">
					<p>You’re currently using <b>Location Weather Lite</b>. To access additional features, consider <a target="_blank" href="https://locationweather.io/pricing/" ><b>upgrading to Pro!</b></a> 🚀</p>
				</div>
				<div class="splw-header-area">
					<div class="splw-container">
						<div class="splw-header-logo">
							<img src="<?php echo esc_url( LOCATION_WEATHER_ASSETS . '/images/location-weather.svg' ); ?>" alt="">
							<span><?php echo esc_html( LOCATION_WEATHER_VERSION ); ?></span>
						</div>
					</div>
					<div class="splw-header-logo-shape">
						<img src="<?php echo esc_url( LOCATION_WEATHER_ASSETS . '/images/lw-icon-logo.svg' ); ?>" alt="">
					</div>
				</div>
				<div class="splw-header-nav">
					<div class="splw-container">
						<div class="splw-header-nav-menu">
							<ul>
								<li><a class="active" data-id="get-start-tab"  href="<?php echo esc_url( home_url( '' ) . '/wp-admin/edit.php?post_type=location_weather&page=splw_help#get-start' ); ?>"><i class="splw-icon-play"></i> Get Started</a></li>
								<li><a href="<?php echo esc_url( home_url( '' ) . '/wp-admin/edit.php?post_type=location_weather&page=splw_help#recommended' ); ?>" data-id="recommended-tab"><i class="splw-icon-recommended"></i> Recommended</a></li>
								<li><a href="<?php echo esc_url( home_url( '' ) . '/wp-admin/edit.php?post_type=location_weather&page=splw_help#lite-to-pro' ); ?>" data-id="lite-to-pro-tab"><i class="splw-icon-lite-to-pro-icon"></i> Lite Vs Pro</a></li>
								<li><a href="<?php echo esc_url( home_url( '' ) . '/wp-admin/edit.php?post_type=location_weather&page=splw_help#about-us' ); ?>" data-id="about-us-tab"><i class="splw-icon-info-circled-alt"></i> About Us</a></li>
							</ul>
						</div>
					</div>
				</div>
			</section>
			<!-- Header section end -->

			<!-- Start Page -->
			<section class="splw__help start-page" id="get-start-tab">
				<div class="splw-container">
					<div class="splw-start-page-wrap">
						<div class="splw-video-area">
							<h2 class='splw-section-title'>Welcome to Location Weather!</h2>
							<span class='splw-normal-paragraph'>Thank you for installing Location Weather! This video will help you get started with the plugin. Enjoy!</span>
							<iframe width="724" height="405" src="https://www.youtube.com/embed/OpfcigkrtDE?list=PLoUb-7uG-5jO40tUXGTe8cyGrbvMzZBqc" frameborder="0" title="location-weather" allowfullscreen=""></iframe>
							<ul>
								<li><a class='splw-medium-btn' href="<?php echo esc_url( home_url( '/' ) . 'wp-admin/post-new.php?post_type=location_weather' ); ?>">Create a Weather</a></li>
								<li><a target="_blank" class='splw-medium-btn' href="https://locationweather.io/demos/lite-version-demo/">Live Demo</a></li>
								<li><a target="_blank" class='splw-medium-btn arrow-btn' href="https://locationweather.io">Explore Location Weather <i class="splw-icon-button-arrow-icon"></i></a></li>
							</ul>
						</div>
						<div class="splw-start-page-sidebar">
							<div class="splw-start-page-sidebar-info-box">
								<div class="splw-info-box-title">
									<h4><i class="splw-icon-doc-icon"></i> Documentation</h4>
								</div>
								<span class='splw-normal-paragraph'>Explore Location Weather plugin capabilities in our enriched documentation.</span>
								<a target="_blank" class='splw-small-btn' href="https://locationweather.io/docs/">Browse Now</a>
							</div>
							<div class="splw-start-page-sidebar-info-box">
								<div class="splw-info-box-title">
									<h4><i class="splw-icon-support"></i> Technical Support</h4>
								</div>
								<span class='splw-normal-paragraph'>For personalized assistance, reach out to our skilled support team for prompt help.</span>
								<a target="_blank" class='splw-small-btn' href="https://shapedplugin.com/create-new-ticket/">Ask Now</a>
							</div>
							<div class="splw-start-page-sidebar-info-box">
								<div class="splw-info-box-title">
									<h4><i class="splw-icon-team-icon"></i> Join The Community</h4>
								</div>
								<span class='splw-normal-paragraph'>Join the official ShapedPlugin Facebook group to share your experiences, thoughts, and ideas.</span>
								<a target="_blank" class='splw-small-btn' href="https://www.facebook.com/groups/ShapedPlugin/">Join Now</a>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Lite To Pro Page -->
			<section class="splw__help lite-to-pro-page" id="lite-to-pro-tab">
				<div class="splw-container">
					<div class="splw-call-to-action-top">
						<h2 class="splw-section-title">Lite vs Pro Comparison</h2>
						<a target="_blank" href="https://locationweather.io/pricing/?ref=1" class='splw-big-btn'>Upgrade to Pro Now!</a>
					</div>
					<div class="splw-lite-to-pro-wrap">
						<div class="splw-features">
							<ul>
								<li class='splw-header'>
									<span class='splw-title'>FEATURES</span>
									<span class='splw-free'>Lite</span>
									<span class='splw-pro'><i class='splw-icon-pro'></i> PRO</span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>All Free Version Features</span>
									<span class='splw-free splw-check-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Weather Layouts & Templates (Vertical & Horizontal Card, Tabs, Table, Accordion, Grid, and more...)</span>
									<span class='splw-free'><b>2</b></span>
									<span class='splw-pro'><b>25+</b></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Combined Weather (Weather Map, Radar Map, Graph Chart,  Detailed Forecast, etc.) <i class='splw-new'>new</i><i class='splw-hot'>hot</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Display Daily Forecast Up to 16 Days <i class='splw-hot'>hot</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Display Hourly (1h & 3h) Forecast Up to 120 Hours <i class='splw-hot'>hot</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Display Weather Map <i class='splw-hot'>hot</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Radar Map by Windy <i class='splw-new'>new</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Weather Graph Chart <i class='splw-new'>new</i><i class='splw-hot'>hot</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Dedicated Customization Options for Weather Map</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Effortlessly Display Weather information from Custom Fields</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Automatically Detect the Visitor’s Location using their IP Address </span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Show High and Low Temperature</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Show Real Feel Statement</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Show Precipitation Unit</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Display Probability of Rain</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Toggle Visibility of Snow, Dew Point, and Air Quality Information <i class='splw-new'>New</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Show UV Index</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Toggle Visibility of Moon Phase, Moonrise, and Moonset Times</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Show National Weather Alerts <i class='splw-new'>New</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Additional Weather Data Carousel Feature <i class='splw-hot'>hot</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>18+ Essential Weather Forecast Data Features <i class='splw-hot'>hot</i></span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Current, Forecast, and Additional Data Icon Packs (Both Animated and Static) <i class='splw-new'>New</i></span>
									<span class='splw-free'><b>3</b></span>
									<span class='splw-pro'><b>13</b></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Display Gradient Color, Weather Based Image, & Video Backgrounds</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Adjust Weather Layout Maximum Width</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Stylize your Weather View Typography with 1500+ Google Fonts</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>All Premium Features, Security Enhancements, and Compatibility</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
								<li class='splw-body'>
									<span class='splw-title'>Priority Top-notch Support</span>
									<span class='splw-free splw-close-icon'></span>
									<span class='splw-pro splw-check-icon'></span>
								</li>
							</ul>
						</div>
						<div class="splw-upgrade-to-pro">
							<h2 class='splw-section-title'>Upgrade To PRO & Enjoy Advanced Features!</h2>
							<span class='splw-section-subtitle'>Already, <b>16000+</b> people are using Location Weather on their websites to create beautiful weather showcase, why won’t you!</span>
							<div class="splw-upgrade-to-pro-btn">
								<div class="splw-action-btn">
									<a target="_blank" href="https://locationweather.io/pricing/?ref=1" class='splw-big-btn'>Upgrade to Pro Now!</a>
									<span class='splw-small-paragraph'>14-Day No-Questions-Asked <a target="_blank" href="https://shapedplugin.com/refund-policy/">Refund Policy</a></span>
								</div>
								<a target="_blank" href="https://locationweather.io/" class='splw-big-btn-border'>See All Features</a>
								<a target="_blank" class='splw-big-btn-border splw-pro-live' href="https://locationweather.io/demos/vertical-card/">Pro Live Demo</a>
							</div>
						</div>
					</div>
					<div class="splw-testimonial">
						<div class="splw-testimonial-title-section">
							<span class='splw-testimonial-subtitle'>NO NEED TO TAKE OUR WORD FOR IT</span>
							<h2 class="splw-section-title">Our Users Love Location Weather Pro!</h2>
						</div>
						<div class="splw-testimonial-wrap">
							<div class="splw-testimonial-area">
								<div class="splw-testimonial-content">
									<p>The free trial worked great upon testing, but needed the advanced features and upgraded. At first the advanced features (i.e. auto location weather and multiple day forecast) did not work as advertised....</p>
								</div>
								<div class="splw-testimonial-info">
									<div class="splw-img">
										<img src="<?php echo esc_url( LOCATION_WEATHER_ASSETS . '/images/Dawie-Hanekom-min.png' ); ?>" alt="">
									</div>
									<div class="splw-info">
										<h3>Dawie Hanekom</h3>
										<p>Managing Director, Newbe Marketing</p>
										<div class="splw-star">
											<i>★★★★★</i>
										</div>
									</div>
								</div>
							</div>
							<div class="splw-testimonial-area">
								<div class="splw-testimonial-content">
									<p>Awesome guys and Awesome plugin for geting different city weather updates easily. The plugin works great and is a simple weather app that does exactly what it is suppo....</p>
								</div>
								<div class="splw-testimonial-info">
									<div class="splw-img">
										<img src="<?php echo esc_url( LOCATION_WEATHER_ASSETS . '/images/Jeffrey-DiFilippo-min.jpeg' ); ?>" alt="">
									</div>
									<div class="splw-info">
										<h3>Jeffrey DiFilippo</h3>
										<p>Web Developer</p>
										<div class="splw-star">
											<i>★★★★★</i>
										</div>
									</div>
								</div>
							</div>
							<div class="splw-testimonial-area">
								<div class="splw-testimonial-content">
									<p>A clean and attractive widget that works without any problems. Amazingly helpful customer support who gave me the custom CSS code that I needed without hesitation, 5..</p>
								</div>
								<div class="splw-testimonial-info">
									<div class="splw-img">
										<img src="<?php echo esc_url( LOCATION_WEATHER_ASSETS . '/images/swan.svg' ); ?>" alt="">
									</div>
									<div class="splw-info">
										<h3>Swan</h3>
										<p>Freelancer, Upwork</p>
										<div class="splw-star">
											<i>★★★★★</i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Recommended Page -->
			<section id="recommended-tab" class="splw-recommended-page">
				<div class="splw-container">
					<h2 class="splw-section-title">Enhance your Website with our Free Robust Plugins</h2>
					<div class="splw-wp-list-table plugin-install-php">
						<div class="splw-recommended-plugins" id="the-list">
							<?php
								$this->splw_plugins_info_api_help_page();
							?>
						</div>
					</div>
				</div>
			</section>

			<!-- About Page -->
			<section id="about-us-tab" class="splw__help about-page">
				<div class="splw-container">
					<div class="splw-about-box">
						<div class="splw-about-info">
							<h3>The Most Powerful Weather Forecast plugin for WordPress from the Location Weather Team, ShapedPlugin, LLC</h3>
							<p>At <b>ShapedPlugin LLC</b>, we have searched for the best way to display live weather updates and forecasts on WordPress sites. Unfortunately, we couldn't find any suitable plugin that met our needs. Therefore, we set out with a simple goal: to develop a powerful WordPress weather plugin that is both user-friendly and efficient.</p>
							<p>We aim to provide the easiest and most convenient way to create unlimited and visually appealing weather forecasts for your WordPress websites. We are confident that you will love the experience!</p>
							<div class="splw-about-btn">
								<a target="_blank" href="https://locationweather.io/" class='splw-medium-btn'>Explore Location Weather</a>
								<a target="_blank" href="https://shapedplugin.com/about-us/" class='splw-medium-btn splw-arrow-btn'>More About Us <i class="splw-icon-button-arrow-icon"></i></a>
							</div>
						</div>
						<div class="splw-about-img">
							<img src="<?php echo esc_url( LOCATION_WEATHER_ASSETS . '/images/shapedplugin-team.jpg' ); ?>" alt="ShapedPlugin Team">
							<span>Team ShapedPlugin LLC at WordCamp Sylhet</span>
						</div>
					</div>
					<?php
					$plugins_arr = get_transient( 'splw_plugins' );
					$plugin_icon = array();
					if ( is_array( $plugins_arr ) && ( count( $plugins_arr ) > 0 ) ) {
						foreach ( $plugins_arr as $plugin ) {
							$plugin_icon[ $plugin['slug'] ] = $plugin['icons'];
						}
					}
					?>
					<div class="splw-our-plugin-list">
						<h3 class="splw-section-title">Upgrade your Website with our High-quality Plugins!</h3>
						<div class="splw-our-plugin-list-wrap">
							<a target="_blank" class="splw-our-plugin-list-box" href="https://wordpresscarousel.com/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['wp-carousel-free'] ); ?>" alt="WP Carousel">
								<h4>WP Carousel</h4>
								<p>The most powerful and user-friendly multi-purpose carousel, slider, & gallery plugin for WordPress.</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://realtestimonials.io/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['testimonial-free'] ); ?>" alt="Real Testimonials">
								<h4>Real Testimonials</h4>
								<p>Simply collect, manage, and display Testimonials on your website and boost conversions.</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://smartpostshow.com/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['post-carousel'] ); ?>" alt="Smart Post Show">
								<h4>Smart Post Show</h4>
								<p>Filter and display posts (any post types), pages, taxonomy, custom taxonomy, and custom field, in beautiful layouts.</p>
							</a>
							<a target="_blank" href="https://wooproductslider.io/?ref=1" class="splw-our-plugin-list-box">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['woo-product-slider'] ); ?>" alt="Product Slider for WooCommerce">
								<h4>Product Slider for WooCommerce</h4>
								<p>Boost sales by interactive product Slider, Grid, and Table in your WooCommerce website or store.</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://shapedplugin.com/plugin/woocommerce-gallery-slider-pro/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['gallery-slider-for-woocommerce'] ); ?>" alt="WooGallery">
								<h4>WooGallery</h4>
								<p>Product gallery slider and additional variation images gallery for WooCommerce and boost your sales.</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://getwpteam.com/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['team-free'] ); ?>" alt="WP Team">
								<h4>WP Team</h4>
								<p>Display your team members smartly who are at the heart of your company or organization!</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://logocarousel.com/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['logo-carousel-free'] ); ?>" alt="Logo Carousel">
								<h4>Logo Carousel</h4>
								<p>Showcase a group of logo images with Title, Description, Tooltips, Links, and Popup as a grid or in a carousel.</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://easyaccordion.io/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['easy-accordion-free'] ); ?>" alt="Easy Accordion">								
								<h4>Easy Accordion</h4>
								<p>Minimize customer support by offering comprehensive FAQs and increasing conversions.</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://shapedplugin.com/plugin/woocommerce-category-slider-pro/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['woo-category-slider-grid'] ); ?>" alt="WooCategory">
								<h4>WooCategory</h4>
								<p>Display by filtering the list of categories aesthetically and boosting sales.</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://wptabs.com/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['wp-expand-tabs-free'] ); ?>" alt="WP Tabs">
								<h4>WP Tabs</h4>
								<p>Display tabbed content smartly & quickly on your WordPress site without coding skills.</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://shapedplugin.com/plugin/woocommerce-quick-view-pro/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['woo-quickview'] ); ?>" alt="Quick View for WooCommerce">
								<h4>Quick View for WooCommerce</h4>
								<p>Quickly view product information with smooth animation via AJAX in a nice Modal without opening the product page.</p>
							</a>
							<a target="_blank" class="splw-our-plugin-list-box" href="https://shapedplugin.com/plugin/smart-brands-for-woocommerce/?ref=1">
								<i class="splw-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['smart-brands-for-woocommerce'] ); ?>" alt="Smart Brands for WooCommerce">
								<h4>Smart Brands for WooCommerce</h4>
								<p>Smart Brands for WooCommerce Pro helps you display product brands in an attractive way on your online store.</p>
							</a>
						</div>
					</div>
				</div>
			</section>

			<!-- Footer Section -->
			<section class="splw-footer">
				<div class="splw-footer-top">
					<p><span>Made With <i class="splw-icon-heart"></i> </span> By the <a target="_blank" href="https://shapedplugin.com/">ShapedPlugin LLC</a> Team</p>
					<p>Get connected with</p>
					<ul>
						<li><a target="_blank" href="https://www.facebook.com/ShapedPlugin/"><i class="splw-icon-fb"></i></a></li>
						<li><a target="_blank" href="https://twitter.com/intent/follow?screen_name=ShapedPlugin"><i class="splw-icon-x"></i></a></li>
						<li><a target="_blank" href="https://profiles.wordpress.org/shapedplugin/#content-plugins"><i class="splw-icon-wp-icon"></i></a></li>
						<li><a target="_blank" href="https://youtube.com/@ShapedPlugin?sub_confirmation=1"><i class="splw-icon-youtube-play"></i></a></li>
					</ul>
				</div>
			</section>
		</div>
		<?php
	}
}
