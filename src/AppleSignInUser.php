<?php

namespace WPSH\SignInWithApplePlugin;

class AppleSignInUser {

	const META_SUB_KEY = 'sign_in_with_apple_sub';

	protected $user;

	public function __construct( $user ) {
		$this->user = $user;
	}

	public function user() {
		return $this->user;
	}

	public function user_id() {
		return $this->user->ID;
	}

	public function id() {
		return $this->sanitize_id( get_user_meta( $this->user->ID, self::META_SUB_KEY, true ) );
	}

	public function has_id() {
		return ( '' !== $this->id() );
	}

	public function set_id( $id ) {
		return update_user_meta( $this->user->ID, self::META_SUB_KEY, $this->sanitize_id( $id ) );
	}

	protected static function sanitize_id( $id ) {
		return trim( (string) $id );
	}

	public static function by_id( $id ) {
		$users = get_users(
			[
				'meta_key' => self::META_SUB_KEY,
				'meta_value' => self::sanitize_id( $id ),
			]
		);

		if ( 1 === count( $users ) ) {
			return new self( $users[0] );
		}

		throw new \Exception( 'User not found.' );
	}

}
