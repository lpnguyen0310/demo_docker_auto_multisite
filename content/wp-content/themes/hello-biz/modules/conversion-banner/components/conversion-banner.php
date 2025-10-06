<?php

namespace HelloBiz\Modules\ConversionBanner\Components;

use HelloBiz\Includes\Utils;
use HelloBiz\Includes\Welcome_Banner_Config;
use HelloBiz\Includes\Banner_State_Provider;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Conversion_Banner {

	private function render_conversion_banner() {
		?>
		<div id="ehp-admin-cb" style="width: calc(100% - 48px)">
		</div>
		<?php
	}

	private function is_conversion_banner_active(): bool {
		if ( get_user_meta( get_current_user_id(), 'ehp_cb_dismissed', true ) ) {
			return false;
		}

		if ( Utils::has_at_least_one_kit() && Utils::is_hello_plus_active() ) {
			return false;
		}

		if ( Utils::is_hello_plus_setup_wizard_done() ) {
			return false;
		}

		$current_screen = get_current_screen();
		if ( ! $current_screen ) {
			return false;
		}

		$allowed_screens = [
			'dashboard',
			'edit-post',
			'edit-page',
			'edit-elementor_library',
			'themes',
			'plugins',
			'plugin-install',
		];

		return in_array( $current_screen->id, $allowed_screens, true );
	}

	private function enqueue_scripts() {
		$handle = 'hello-biz-conversion-banner';
		$asset_path = HELLO_BIZ_SCRIPTS_PATH . 'hello-biz-conversion-banner.asset.php';
		$asset_url = HELLO_BIZ_SCRIPTS_URL;

		if ( ! file_exists( $asset_path ) ) {
			return;
		}

		$asset = require $asset_path;

		wp_enqueue_script(
			$handle,
			$asset_url . 'hello-biz-conversion-banner.js',
			array_merge( $asset['dependencies'], [ 'wp-util' ] ),
			$asset['version'],
			true
		);

		wp_set_script_translations( $handle, 'hello-biz' );

		$state_provider = new Banner_State_Provider();
		$banner_config = new Welcome_Banner_Config( $state_provider );

		$title = $banner_config->get_title();
		$description = $banner_config->get_description();
		$button_text = $banner_config->get_primary_button_text();
		$button_link = Utils::is_hello_plus_active() ?
			$banner_config->get_primary_button_link() :
			admin_url( 'admin.php?page=hello-biz&start-install=true' );
		$agreement_text = $banner_config->get_agreement_text();
		$show_text = ! empty( $agreement_text );

		$is_installing_plugin_with_uploader = 'upload-plugin' === filter_input( INPUT_GET, 'action', FILTER_UNSAFE_RAW );

		wp_localize_script(
			$handle,
			'ehp_cb',
			[
				'imageUrl' => $banner_config->get_image_config()['src'],
				'buttonText' => $button_text,
				'buttonUrl' => $button_link,
				'showText' => $show_text,
				'agreementText' => $agreement_text,
				'beforeWrap' => $is_installing_plugin_with_uploader,
				'title' => $title,
				'description' => $description,
			]
		);
	}

	public function dismiss_theme_notice() {
		check_ajax_referer( 'ehp_cb_nonce', 'nonce' );

		update_user_meta( get_current_user_id(), 'ehp_cb_dismissed', true );

		wp_send_json_success( [ 'message' => __( 'Notice dismissed.', 'hello-biz' ) ] );
	}

	public function __construct() {

		add_action( 'wp_ajax_ehp_dismiss_theme_notice', [ $this, 'dismiss_theme_notice' ] );

		add_action( 'current_screen', function () {
			if ( ! $this->is_conversion_banner_active() ) {
				return;
			}

			add_action( 'in_admin_header', function () {
				$this->render_conversion_banner();
			}, 11 );

			add_action( 'admin_enqueue_scripts', function () {
				$this->enqueue_scripts();
			} );
		} );
	}
}
