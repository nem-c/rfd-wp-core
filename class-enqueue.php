<?php
/**
 * Enqueue library
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package RFD\Core
 */

namespace RFD\Core;

/**
 * Class Enqueue
 *
 * @package RFD\Core
 */
final class Enqueue {

	/**
	 * Registered admin assets
	 *
	 * @var array[] $admin_assets
	 */
	protected $admin_assets = array(
		'css' => array(),
		'js'  => array(),
	);

	/**
	 * Registered frontend assets
	 *
	 * @var array[] $frontend_assets
	 */
	protected $frontend_assets = array(
		'css' => array(),
		'js'  => array(),
	);

	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 */
	public static function init( Loader $loader ): void {
		$enqueue = new Enqueue();
		if ( true === is_admin() ) {
			$enqueue->load_admin_configuration();
			$loader->add_action( 'admin_enqueue_scripts', $enqueue, 'enqueue_admin_assets' );
		}
		$enqueue->load_frontend_configuration();
		$loader->add_action( 'wp_enqueue_scripts', $enqueue, 'enqueue_frontend_assets' );
	}

	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets(): void {
		$this->enqueue_assets( $this->admin_assets );
	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets(): void {
		$this->enqueue_assets( $this->frontend_assets );
	}

	/**
	 * Enqueue assets
	 *
	 * @param array $assets Assets array.
	 */
	public function enqueue_assets( array $assets ): void {
		foreach ( $assets['js'] as $js_asset ) {
			$this->enqueue_script( $js_asset );
		}

		foreach ( $assets['css'] as $css_asset ) {
			$this->enqueue_style( $css_asset );
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @param string|array $asset Script config array.
	 */
	protected function enqueue_script( $asset ): void {
		if ( false === is_array( $asset ) ) {
			wp_enqueue_script( $asset );
		} else {
			wp_enqueue_script(
				$asset['handle'],
				$asset['src'],
				$asset['deps'],
				$asset['ver'],
				$asset['in_footer']
			);
		}
	}

	/**
	 * Enqueue style
	 *
	 * @param array $asset Script config array.
	 */
	protected function enqueue_style( array $asset ): void {
		wp_enqueue_style(
			$asset['handle'],
			$asset['src'],
			$asset['deps'],
			$asset['ver']
		);
	}

	/**
	 * Load frontend configuration file
	 */
	protected function load_frontend_configuration(): void {
		$config_file_path = RFD_CORE_CONFIG_PATH . 'enqueue.php';
		$config_array     = $this->load_config_file( $config_file_path );
		$this->store_configuration( $config_array );
	}

	/**
	 * Load admin configuration file
	 */
	protected function load_admin_configuration(): void {
		$config_file_path = RFD_CORE_CONFIG_PATH . 'admin/enqueue.php';
		$config_array     = $this->load_config_file( $config_file_path );
		$this->store_configuration( $config_array, 'admin' );
	}

	/**
	 * Save configuration to class attributes
	 *
	 * @param array $config_array Configuration array for assets.
	 * @param string $type Configuration type (frontend or admin).
	 */
	protected function store_configuration( array $config_array, string $type = 'frontend' ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		$type_attribute = $type . '_assets';

		$js_config  = $config_array['js'] ?? array();
		$css_config = $config_array['css'] ?? array();

		foreach ( $js_config as $handle => $config_data ) {
			if ( true === $config_data ) {
				$this->{$type_attribute}['js'][] = $handle;
			} else {
				$src = $config_data['src'] ?? '';
				if ( true === empty( $src ) ) {
					continue;
				}
				$this->{$type_attribute}['js'][] = array(
					'handle'    => $handle,
					'src'       => $config_data['src'],
					'deps'      => $config_data['deps'] ?? array(),
					'ver'       => $config_data['version'] ?? 0,
					'in_footer' => $config_data['in_footer'] ?? true,
				);
			}
		}

		foreach ( $css_config as $handle => $config_data ) {
			$src = $config_data['src'] ?? '';
			if ( true === empty( $src ) ) {
				continue;
			}
			$this->{$type_attribute}['css'][] = array(
				'handle' => $handle,
				'src'    => $config_data['src'],
				'deps'   => $config_data['deps'] ?? array(),
				'ver'    => $config_data['version'] ?? 0,
			);
		}
	}

	/**
	 * Load config file if exists
	 *
	 * @param string $config_file_path Path to config file.
	 *
	 * @return array
	 */
	protected function load_config_file( string $config_file_path ): array {
		$config_array = array();

		if ( true === file_exists( $config_file_path ) ) {
			$config_array = include $config_file_path;
		}

		return $config_array;
	}
}
