<?php

namespace WPSH\SignInWithApplePlugin;

use Lcobucci\JWT\Parser;

class PluginController {

	const OPTION_PREFIX = 'siwa_plugin';

	const OPTION_KEY_SERVICE_ID = 'service_id';

	const OPTION_KEY_TEAM_ID = 'team_id';

	const OPTION_KEY_KEY_ID = 'key_id';

	/**
	 * Instance of the current plugin.
	 *
	 * @var \WPSH\SignInWithApplePlugin\Plugin
	 */
	protected $plugin;

	protected $settings;

	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->settings = new Options( self::OPTION_PREFIX );
	}

	public function init() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );

		if ( $this->is_configured() ) {
			add_action( 'login_footer', [ $this, 'print_signin_button' ] );
			add_action( 'login_init', [ $this, 'login_init' ] );
		}
	}

	public function is_configured() {
		return ( ! empty( $this->signin_service_id() ) && ! empty( $this->signin_team_id() ) && ! empty( $this->signin_key_id() ) );
	}

	public function signin_redirect_uri() {
		return wp_login_url();
	}

	public function signin_service_id() {
		return $this->sanitize_setting( $this->settings->get( self::OPTION_KEY_SERVICE_ID ) );
	}

	public function signin_team_id() {
		return $this->sanitize_setting( $this->settings->get( self::OPTION_KEY_TEAM_ID ) );
	}

	public function signin_key_id() {
		return $this->sanitize_setting( $this->settings->get( self::OPTION_KEY_KEY_ID ) );
	}

	protected function sanitize_setting( $value ) {
		return preg_replace( '#[^a-zA-Z0-9]+\.#', '', (string) $value );
	}

	public function apple_api() {
		return new AppleApi(
			$this->signin_key_id(),
			$this->signin_team_id(),
			$this->signin_service_id(),
			$this->signin_redirect_uri()
		);
	}

	public function print_signin_button() {
		printf(
			'<a href="%s">Sign In with Apple</a>',
			esc_url( $this->apple_api()->authorize_url( 'login' ) )
		);
	}

	public function login_init() {
		if ( isset( $_GET['id_token'], $_GET['state'] ) ) {
			$id_token = filter_input( INPUT_GET, 'id_token', FILTER_SANITIZE_STRING );
			$state = filter_input( INPUT_GET, 'state', FILTER_SANITIZE_STRING );

			if ( $this->apple_api()->verify_state( $state ) ) {
				$token_parser = new Parser();
				$token = $token_parser->parse( $id_token );

				if ( $token->hasClaim( 'sub' ) ) {
					$sub = $token->getClaim( 'sub' );
					$action = $this->apple_api()->state_action( $state );

					if ( 'register' === $action && is_user_logged_in() ) {
						// Store the user identifier for the current user.
						$user = new AppleSignInUser( wp_get_current_user() );
						$user->set_id( $sub );
						wp_redirect( get_edit_profile_url( $user->user_id() ) );
						exit;
					} elseif ( 'login' === $action && ! is_user_logged_in() ) {
						// Log-in user by the Apple ID.
						try {
							$user = AppleSignInUser::by_id( $sub );
							wp_set_auth_cookie( $user->user_id() );
							wp_redirect( admin_url() );
							exit;
						} catch ( \Exception $exception ) {
							wp_die( $exception->getMessage() );
						}
					}
				}
			}
		}
	}

	public function admin_init() {
		$this->settings->add( self::OPTION_KEY_SERVICE_ID );
		$this->settings->add( self::OPTION_KEY_TEAM_ID );
		$this->settings->add( self::OPTION_KEY_KEY_ID );

		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'show_user_profile', [ $this, 'admin_user_profile' ] );
		}
	}

	public function admin_user_profile( $user ) {
		$apple_user = new AppleSignInUser( wp_get_current_user() );

		?>
		<table class="form-table">
			<tr>
				<th>
					<?php esc_html_e( 'Log In with Apple' ); ?>
				</th>
				<td>
					<?php

					if ( $this->is_configured() ) {
						if ( $apple_user->has_id() ) {
							printf(
								'<p>Your Apple user ID: <code>%s</code></p>',
								esc_html( $apple_user->id() )
							);
						}

						printf(
							'<p>
								Associate this account with your Apple ID:
								<a href="%s" class="button">Sign In with Apple</a>
							</p>',
							esc_url( $this->apple_api()->authorize_url( 'register' ) )
						);
					} else {
						printf(
							'<p>
								Please specify
								<code>%s</code>,
								<code>%s</code> and
								<code>%s</code>
								in <a href="%s">the settings</a>.
							</p>',
							esc_html( $this->settings->key( self::OPTION_KEY_SERVICE_ID ) ),
							esc_html( $this->settings->key( self::OPTION_KEY_TEAM_ID ) ),
							esc_html( $this->settings->key( self::OPTION_KEY_KEY_ID ) ),
							esc_url( admin_url( 'options.php' ) )
						);
					}

					?>
				</td>
			</tr>
		</table>
		<?php
	}

}
