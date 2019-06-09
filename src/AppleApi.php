<?php

namespace WPSH\SignInWithApplePlugin;

class AppleApi {

	const AUTHORIZE_URL = 'https://appleid.apple.com/auth/authorize';

	const TOKEN_URL = 'https://appleid.apple.com/auth/token';

	const AUDIENCE_KEY = 'https://appleid.apple.com';

	const STATE_NONCE_ACTION = 'sign-in-with-apple';

	/**
	 * Setup the API.
	 *
	 * @param string $keyidentifier 10-character key identifier obtained from your developer account.
	 * @param string $issuer The issuer registered claim key, which has the value of your 10-character Team ID, obtained from your developer account.
	 * @param string $subject The application identifier for your app.
	 */
	public function __construct( $keyidentifier, $issuer, $subject, $redirect_url ) {
		$this->keyidentifier = $keyidentifier;
		$this->issuer = $issuer;
		$this->subject = $subject;
		$this->redirect_url = $redirect_url;
	}

	public function scopes() {
		return [
			'name',
			'email',
		];
	}

	public function authorize_url( $action = 'login' ) {
		return add_query_arg(
			[
				'response_type' => 'code id_token',
				'client_id' => $this->subject,
				'redirect_uri' => $this->redirect_url,
				'state' => $this->state( $action ),
				'scope' => implode( ' ', $this->scopes() ),
			],
			self::AUTHORIZE_URL
		);
	}

	protected function nonce_action( $action ) {
		return self::STATE_NONCE_ACTION . $action;
	}

	public function state( $action ) {
		return sprintf(
			'%s-%s',
			wp_create_nonce( $this->nonce_action( $action ) ),
			$action
		);
	}

	protected function parse_state( $state ) {
		list( $nonce, $action ) = explode( '-', $state, 2 );

		return [
			$nonce,
			$action,
		];
	}

	public function state_action( $state ) {
		return $this->parse_state( $state )[1];
	}

	public function verify_state( $state ) {
		$nonce = $this->parse_state( $state )[0];
		$action = $this->nonce_action( $this->parse_state( $state )[1] );

		return ( 1 === wp_verify_nonce( $nonce, $action ) );
	}

}
