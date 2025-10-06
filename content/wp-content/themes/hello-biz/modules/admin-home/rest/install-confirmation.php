<?php

namespace HelloBiz\Modules\AdminHome\Rest;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Install_Confirmation extends Rest_Base {

	public function register_routes() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/install-confirmation/dismiss',
			[
				'methods' => 'POST',
				'callback' => [ $this, 'dismiss_confirmation' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			]
		);
	}

	public function dismiss_confirmation( $request ) {
		$user_id = $this->get_current_user_id();

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$update_result = $this->update_user_dismissal_meta( $user_id );

		if ( is_wp_error( $update_result ) ) {
			return $update_result;
		}

		return $this->create_success_response();
	}

	private function get_current_user_id() {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return new \WP_Error( 'no_user', __( 'User not found.', 'hello-biz' ), [ 'status' => 401 ] );
		}

		return $user_id;
	}

	private function update_user_dismissal_meta( $user_id ) {
		$result = update_user_meta( $user_id, 'ehp_install_confirmation_dismissed', true );

		if ( false === $result ) {
			return new \WP_Error( 'update_failed', __( 'Failed to update user meta.', 'hello-biz' ), [ 'status' => 500 ] );
		}

		return true;
	}

	private function create_success_response() {
		return rest_ensure_response( [
			'success' => true,
			'message' => __( 'Install confirmation dialog dismissed.', 'hello-biz' ),
		] );
	}
}
