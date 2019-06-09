<?php

namespace WPSH\SignInWithApplePlugin;

/**
 * WordPress plugin abstraction.
 */
class Plugin {

	/**
	 * Absolute path to the main plugin file.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Absolute path to the root directory of this plugin.
	 *
	 * @var string
	 */
	protected $dir;

	/**
	 * Store the WP uploads dir object.
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_upload_dir/
	 * @var array
	 */
	protected $uploads_dir;

	/**
	 * Meta data of the plugin from the bootstrap header.
	 *
	 * @var array
	 */
	protected $meta;

	/**
	 * Setup the plugin.
	 *
	 * @param string $plugin_file_path Absolute path to the main plugin file.
	 */
	public function __construct( $plugin_file_path ) {
		$this->file = $plugin_file_path;
		$this->dir = dirname( $plugin_file_path );
		$this->uploads_dir = wp_upload_dir( null, false ); // Don't create the time-based directory.
	}

	/**
	 * Return the absolute path to the plugin directory.
	 *
	 * @return string
	 */
	public function dir() {
		return $this->dir;
	}

	/**
	 * Return the absolute path to the plugin file.
	 *
	 * @return string
	 */
	public function file() {
		return $this->file;
	}

	/**
	 * Get the file path relative to the WordPress plugin directory.
	 *
	 * @param  string $file_path Absolute path to any plugin file.
	 *
	 * @return string
	 */
	public function basename( $file_path = null ) {
		if ( ! isset( $file_path ) ) {
			$file_path = $this->file();
		}

		return plugin_basename( $file_path );
	}

	/**
	 * Get the public URL to the asset file.
	 *
	 * @param string $asset_path_relative Relative path to the asset file.
	 */
	public function asset_url( $asset_path_relative ) {
		static $plugin_basename;

		// Do this only once per every request to save some processing time.
		if ( ! isset( $plugin_basename ) ) {
			$plugin_basename = $this->basename( $this->dir() );
		}

		$file_path = sprintf(
			'%s/%s',
			$plugin_basename,
			ltrim( $asset_path_relative, '/' )
		);

		return plugins_url( $file_path );
	}

	/**
	 * Get absolute path to a file in the uploads directory.
	 *
	 * @param  strign $path_relative File path relative to the root of the WordPress uploads directory.
	 *
	 * @return string
	 */
	public function uploads_dir( $path_relative = null ) {
		if ( isset( $path_relative ) ) {
			return sprintf( '%s/%s', $this->uploads_dir['basedir'], $path_relative );
		}

		return $this->uploads_dir['basedir'];
	}

	/**
	 * Get URL to a file in the uploads directory.
	 *
	 * @param  string $path_relative Path to the file relative to the root of the WordPress uploads directory.
	 *
	 * @return string
	 */
	public function uploads_dir_url( $path_relative = null ) {
		if ( isset( $path_relative ) ) {
			return sprintf( '%s/%s', $this->uploads_dir['baseurl'], $path_relative );
		}

		return $this->uploads_dir['baseurl'];
	}

	/**
	 * Return the current version of the plugin.
	 *
	 * @return mixed
	 */
	public function version() {
		return $this->meta( 'Version' );
	}

	/**
	 * Return the plugin name from the plugin header.
	 *
	 * @return string
	 */
	public function name() {
		return $this->meta( 'Plugin Name' );
	}

	/**
	 * Return the plugin URI from the plugin header.
	 *
	 * @return string
	 */
	public function uri() {
		return $this->meta( 'Plugin URI' );
	}

	/**
	 * Get plugin meta data.
	 *
	 * @param  string $field Optional field key.
	 *
	 * @return array|string|null
	 */
	public function meta( $field = null ) {
		if ( ! isset( $this->meta ) ) {
			$this->meta = get_plugin_data( $this->file );
		}

		if ( isset( $field ) ) {
			if ( isset( $this->meta[ $field ] ) ) {
				return $this->meta[ $field ];
			}

			return null;
		}

		return $this->meta;
	}
}
