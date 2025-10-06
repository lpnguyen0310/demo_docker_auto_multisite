<?php

namespace HelloBiz\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Utils_Interface {

	public function is_hello_plus_active(): bool;

	public function has_at_least_one_kit(): bool;

	public function is_hello_plus_setup_wizard_done(): bool;
}
