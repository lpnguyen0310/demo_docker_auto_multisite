<?php

namespace HelloBiz\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Banner_State_Provider {

	const STATE_NOT_INSTALLED = 'not_installed';
	const STATE_ONE_KIT_INSTALLED = 'one_kit_installed';
	const STATE_NOT_SETUP = 'not_setup';
	const STATE_SETUP_COMPLETE = 'setup_complete';

	private Utils_Interface $utils;

	public function __construct( Utils_Interface $utils = null ) {
		$this->utils = $utils ?? new Utils_Adapter();
	}

	public function get_current_state(): string {
		if ( ! $this->utils->is_hello_plus_active() ) {
			return $this->utils->has_at_least_one_kit() ?
				self::STATE_ONE_KIT_INSTALLED :
				self::STATE_NOT_INSTALLED;
		}

		if ( $this->utils->has_at_least_one_kit() ) {
			return self::STATE_SETUP_COMPLETE;
		}

		if ( ! $this->utils->is_hello_plus_setup_wizard_done() ) {
			return self::STATE_NOT_SETUP;
		}

		return self::STATE_SETUP_COMPLETE;
	}
}
