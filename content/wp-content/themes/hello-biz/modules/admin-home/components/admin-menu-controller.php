<?php

namespace HelloBiz\Modules\AdminHome\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HelloBiz\Modules\AdminHome\Module;
use HelloBiz\Includes\Utils;
use HelloBiz\Includes\Banner_State_Provider;
use HelloBiz\Includes\Welcome_Banner_Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Admin_Menu_Controller {

	const MENU_PAGE_ICON = 'dashicons-plus-alt';
	const MENU_PAGE_POSITION = 59.9;

	const THEME_BUILDER_SLUG = '-theme-builder';
	const SETUP_SUBMENU_SLUG = 'hello-biz-setup';

	public function admin_menu(): void {
		add_menu_page(
			__( 'Hello Biz', 'hello-biz' ),
			__( 'Hello Biz', 'hello-biz' ),
			'manage_options',
			Module::MENU_PAGE_SLUG,
			[ $this, 'render' ],
			self::MENU_PAGE_ICON,
			self::MENU_PAGE_POSITION
		);

		add_submenu_page(
			Module::MENU_PAGE_SLUG,
			__( 'Home', 'hello-biz' ),
			__( 'Home', 'hello-biz' ),
			'manage_options',
			Module::MENU_PAGE_SLUG,
			[ $this, 'render' ]
		);

		$this->add_setup_submenu();

		do_action( 'hello-plus-theme/admin-menu', Module::MENU_PAGE_SLUG );

		if ( Utils::is_elementor_active() ) {
			$theme_builder_slug = Utils::get_theme_builder_slug();
			add_submenu_page(
				Module::MENU_PAGE_SLUG,
				__( 'Theme Builder', 'hello-biz' ),
				__( 'Theme Builder', 'hello-biz' ),
				'manage_options',
				empty( $theme_builder_slug ) ? EHP_THEME_SLUG . self::THEME_BUILDER_SLUG : $theme_builder_slug,
				[ $this, 'render_home' ]
			);
		}
	}

	public function render(): void {
		echo '<div id="ehp-admin-home"></div>';
	}

	private function add_setup_submenu(): void {
		if ( Utils::is_hello_plus_active() ) {
			return;
		}

		$state_provider = new Banner_State_Provider();
		$state = $state_provider->get_current_state();

		$button_text = $this->get_setup_button_text( $state );

		add_submenu_page(
			Module::MENU_PAGE_SLUG,
			$button_text,
			$button_text,
			'manage_options',
			self::SETUP_SUBMENU_SLUG,
			[ $this, 'render' ]
		);
	}

	private function get_setup_button_text( string $state ): string {
		if ( in_array( $state, [ Banner_State_Provider::STATE_ONE_KIT_INSTALLED, Banner_State_Provider::STATE_NOT_SETUP ], true ) ) {
			return __( 'Finish setup', 'hello-biz' );
		}

		return __( 'Begin setup', 'hello-biz' );
	}

	public function redirect_menus(): void {
		$page = sanitize_key( filter_input( INPUT_GET, 'page', FILTER_UNSAFE_RAW ) );

		switch ( $page ) {
			case EHP_THEME_SLUG . self::THEME_BUILDER_SLUG:
				wp_safe_redirect(
					Utils::get_theme_builder_options()['link']
				);
				exit;

			case self::SETUP_SUBMENU_SLUG:
				wp_safe_redirect(
					admin_url( 'admin.php?page=hello-biz&start-install=true' )
				);
				exit;

			default:
				break;
		}
	}

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'redirect_menus' ] );
	}
}
