<?php

namespace HelloBiz\Modules\ConversionBanner\Components;

use HelloBiz\Includes\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Ajax_Handler {

	public function __construct() {
		add_action( 'wp_ajax_hello_biz_install_hp', [ $this, 'install_hello_plus' ] );
	}

	public function install_hello_plus() {
		if ( Utils::is_hello_plus_installed() ) {

			$plugin = 'hello-plus/hello-plus.php';
			$nonce = wp_create_nonce( 'activate-plugin_' . $plugin );
			$activation_url = admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $plugin ) . '&_wpnonce=' . $nonce );

			wp_send_json_success([
				'activateUrl' => $activation_url,
				'message' => __( 'Click the link to activate Hello Plus.', 'hello-biz' ),
			]);

		} else {
			wp_ajax_install_plugin();
		}
	}
}
