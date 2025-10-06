<?php

namespace HelloBiz\Modules\AdminHome\Components;

use HelloBiz\Modules\AdminHome\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Bar_Controller {

	const ADMIN_BAR_ID = 'hello-biz-home';
	public function add_admin_bar_menu( $wp_admin_bar ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( $this->is_admin_area() ) {
			return;
		}

		$wp_admin_bar->add_node( [
			'id'    => self::ADMIN_BAR_ID,
			'title' => $this->get_menu_title(),
			'href'  => admin_url( 'admin.php?page=' . Module::MENU_PAGE_SLUG ),
		] );
	}

	protected function is_admin_area(): bool {
		return is_admin();
	}

	protected function get_menu_title(): string {
		return sprintf(
			'<span class="ab-item">%s</span>',
			__( 'Manage Hello Biz theme', 'hello-biz' )
		);
	}

	public function __construct() {
		add_action( 'admin_bar_menu', [ $this, 'add_admin_bar_menu' ], 100 );
	}
}
