<?php

namespace HelloBiz\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Utils_Adapter implements Utils_Interface {

	public function is_hello_plus_active(): bool {
		return Utils::is_hello_plus_active();
	}

	public function has_at_least_one_kit(): bool {
		return Utils::has_at_least_one_kit();
	}

	public function is_hello_plus_setup_wizard_done(): bool {
		return Utils::is_hello_plus_setup_wizard_done();
	}
}
