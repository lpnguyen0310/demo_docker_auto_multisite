<?php
namespace HelloBiz\Modules\Theme\Classes;

use HelloBiz\Includes\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Customizer_Action_Links extends \WP_Customize_Control {

	// Whitelist content parameter
	public $content = '';

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overridden without having to rewrite the wrapper.
	 *
	 * @return void
	 */
	public function render_content() {
		$this->print_customizer_action_links();

		if ( isset( $this->description ) ) {
			echo '<span class="description customize-control-description">' . wp_kses_post( $this->description ) . '</span>';
		}
	}

	private function is_header_footer_experiment_active(): bool {
		if ( ! Utils::is_elementor_active() ) {
			return false;
		}

		return (bool) ( \Elementor\Plugin::$instance->experiments->is_feature_active( 'hello-theme-header-footer' ) );
	}

	/**
	 * Print customizer action links.
	 *
	 * @return void
	 */
	private function print_customizer_action_links() {
		$action_link_data = [];

		$button_text = __( 'Begin Setup', 'hello-biz' );
		$button_link = Utils::get_hello_plus_activation_link();
		$show_text = true;

		if ( Utils::is_hello_plus_active() && ! Utils::is_hello_plus_setup_wizard_done() ) {
			$button_link = self_admin_url( 'admin.php?page=hello-plus-setup-wizard' );
			$show_text = false;

			if ( Utils::is_elementor_active() ) {
				$button_text = __( 'Finish Setup', 'hello-biz' );
			}
		}

		$action_link_data = [
			'image' => HELLO_BIZ_IMAGES_URL . 'elementor.svg',
			'alt' => esc_attr__( 'Elementor', 'hello-biz' ),
			'title' => esc_html__( 'Style your header and footer', 'hello-biz' ),
			'message' => esc_html__( 'Customize your header and footer across your website.', 'hello-biz' ),
			'button' => $button_text,
			'link' => $button_link,
			'underButton' => $show_text ? esc_html__( 'By clicking “Begin setup” I agree to install and activate the Hello+ plugin.', 'hello-biz' ) : '',
		];

		$action_link_data = apply_filters( 'hello-plus-theme/customizer/action-links', $action_link_data );

		$customizer_content = $this->get_customizer_action_links_html( $action_link_data );

		echo wp_kses_post( $customizer_content );
	}

	/**
	 * Get the customizer action links HTML.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	private function get_customizer_action_links_html( $data ) {
		if (
			empty( $data )
			|| ! isset( $data['image'] )
			|| ! isset( $data['alt'] )
			|| ! isset( $data['title'] )
			|| ! isset( $data['message'] )
			|| ! isset( $data['link'] )
			|| ! isset( $data['button'] )
		) {
			return '';
		}

		return sprintf(
			'<div class="ehp-action-links">
				<img src="%1$s" alt="%2$s">
				<p class="ehp-action-links-title">%3$s</p>
				<p class="ehp-action-links-message">%4$s</p>
				<a class="button button-primary" id="ehp-begin-setup" target="_blank" href="%5$s">%6$s</a>
				<p class="ehp-action-links-under-button">%7$s</p>
			</div>',
			$data['image'],
			$data['alt'],
			$data['title'],
			$data['message'],
			$data['link'],
			$data['button'],
			$data['underButton'] ?? '',
		);
	}
}
