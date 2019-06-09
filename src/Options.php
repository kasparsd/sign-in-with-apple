<?php

namespace WPSH\SignInWithApplePlugin;

class Options {

	protected $prefix;

	public function __construct( $prefix ) {
		$this->prefix = $prefix;
	}

	public function key( $key ) {
		return sprintf( '%s_%s', $this->prefix, $key );
	}

	public function add( $key, $value = null ) {
		return add_option( $this->key( $key ), $value, null, false );
	}

	public function get( $key ) {
		return get_option( $this->key( $key ), null );
	}

	public function set( $key, $value ) {
		return update_option( $this->key( $key ), $value );
	}

}
