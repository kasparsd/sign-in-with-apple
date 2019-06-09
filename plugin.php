<?php
/**
 * Plugin Name: Sign In with Apple
 * Description: Enable Sign in with Apple JS.
 * Plugin URI: https://github.com/kasparsd/signin-with-apple
 * Author: Kaspars Dambis
 * Author URI: https://kaspars.net
 * Version: 0.1.0
 * License: GPL2
 * Text Domain: signinwithapple
 */

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

add_action(
	'plugins_loaded',
	function() {
		$plugin = new \WPSH\SignInWithApplePlugin\Plugin( __FILE__ );
		$signinwithapple = new \WPSH\SignInWithApplePlugin\PluginController( $plugin );
		$signinwithapple->init();
	}
);
