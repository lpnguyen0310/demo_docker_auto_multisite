<?php

namespace HelloBiz\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Welcome_Banner_Config {

	private Banner_State_Provider $state_provider;

	public function __construct( Banner_State_Provider $state_provider ) {
		$this->state_provider = $state_provider;
	}

	public function get_title(): string {
		$state = $this->state_provider->get_current_state();

		switch ( $state ) {
			case Banner_State_Provider::STATE_ONE_KIT_INSTALLED:
				return __( 'Complete your website faster with Hello+', 'hello-biz' );
			case Banner_State_Provider::STATE_NOT_SETUP:
				return __( 'Almost there', 'hello-biz' );
			case Banner_State_Provider::STATE_SETUP_COMPLETE:
				return __( 'Hello Biz Home', 'hello-biz' );
			default:
				return __( 'Welcome to Hello Biz', 'hello-biz' );
		}
	}

	public function get_description(): string {
		$state = $this->state_provider->get_current_state();

		switch ( $state ) {
			case Banner_State_Provider::STATE_ONE_KIT_INSTALLED:
				return __( 'Hello+ is a free plugin that gives your website a header, footer, and extra widgets designed to help you build a business-ready site.', 'hello-biz' );
			case Banner_State_Provider::STATE_NOT_SETUP:
				return __( 'Choose a website template and start customizing your site.', 'hello-biz' );
			case Banner_State_Provider::STATE_SETUP_COMPLETE:
				return __( 'Access everything you need to set up and manage your website.', 'hello-biz' );
			default:
				return __( 'Access the full suite of features, including website templates, header and footer templates, and extra widgets.', 'hello-biz' );
		}
	}

	public function get_primary_button_text(): string {
		$state = $this->state_provider->get_current_state();

		switch ( $state ) {
			case Banner_State_Provider::STATE_ONE_KIT_INSTALLED:
			case Banner_State_Provider::STATE_NOT_SETUP:
				return __( 'Finish setup', 'hello-biz' );
			case Banner_State_Provider::STATE_SETUP_COMPLETE:
				return __( 'Edit home page', 'hello-biz' );
			default:
				return __( 'Begin setup', 'hello-biz' );
		}
	}

	public function get_primary_button_link(): string {
		$state = $this->state_provider->get_current_state();

		switch ( $state ) {
			case Banner_State_Provider::STATE_NOT_SETUP:
				return self_admin_url( 'admin.php?page=hello-plus-setup-wizard' );
			case Banner_State_Provider::STATE_SETUP_COMPLETE:
				return get_edit_post_link( get_option( 'page_on_front' ), 'admin' ) . '&action=elementor';
			default:
				return Utils::is_hello_plus_installed() ? Utils::get_hello_plus_activation_link() : 'install';
		}
	}

	public function get_agreement_text(): string {
		$state = $this->state_provider->get_current_state();

		if ( ! in_array( $state, [ Banner_State_Provider::STATE_NOT_INSTALLED, Banner_State_Provider::STATE_ONE_KIT_INSTALLED ], true ) ) {
			return '';
		}

		$button_text = $this->get_primary_button_text();
		return sprintf( __( 'By clicking "%s" I agree to install and activate the Hello+ plugin.', 'hello-biz' ), $button_text );
	}

	public function get_image_config(): array {
		$state = $this->state_provider->get_current_state();
		$cta_text = $this->get_primary_button_text();

		if ( Banner_State_Provider::STATE_SETUP_COMPLETE === $state ) {
			return [
				'src' => '',
				'alt' => '',
			];
		}

		$is_one_kit_installed = Banner_State_Provider::STATE_ONE_KIT_INSTALLED === $state;

		$image_extension = $is_one_kit_installed ? 'jpeg' : 'png';
		$image_file      = 'banner.' . $image_extension;
		$image_src       = HELLO_BIZ_IMAGES_URL . $image_file;

		return [
			'src' => $image_src,
			'alt' => $cta_text,
		];
	}

	public function get_video_config(): string {
		$state = $this->state_provider->get_current_state();

		if ( Banner_State_Provider::STATE_SETUP_COMPLETE === $state ) {
			return 'https://www.youtube.com/embed/bjoG0sU9K0M';
		}

		return '';
	}

	public function get_secondary_button_config(): array {
		$state = $this->state_provider->get_current_state();

		if ( Banner_State_Provider::STATE_SETUP_COMPLETE === $state ) {
			return [
				'title'   => __( 'View site', 'hello-biz' ),
				'variant' => 'outlined',
				'link'    => get_site_url(),
				'color'   => 'secondary',
			];
		}

		return [];
	}

	public function get_complete_config(): array {
		$image_config = $this->get_image_config();
		$secondary_button = $this->get_secondary_button_config();
		$agreement_text = $this->get_agreement_text();

		$config = [
			'title'   => $this->get_title(),
			'text'    => $this->get_description(),
			'image'   => $image_config,
			'video'   => $this->get_video_config(),
			'buttons' => [
				[
					'title'   => $this->get_primary_button_text(),
					'variant' => 'contained',
					'link'    => $this->get_primary_button_link(),
					'color'   => 'primary',
				],
			],
		];

		if ( ! empty( $secondary_button ) ) {
			$config['buttons'][] = $secondary_button;
		}

		if ( ! empty( $agreement_text ) ) {
			$config['agreementText'] = $agreement_text;
		}

		return $config;
	}
}
