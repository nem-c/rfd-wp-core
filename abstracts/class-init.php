<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Core
 * @subpackage RFD\Core\Abstracts
 */

namespace RFD\Core\Abstracts;

use RFD\Core\Admin\Menu;
use RFD\Core\Enqueue;
use RFD\Core\Loader;

/**
 * Class Init
 *
 * @package RFD\Core\Abstracts
 */
abstract class Init {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.9.0
	 * @access   protected
	 * @var      Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.9.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.9.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Taxonomies to be registered.
	 *
	 * @var array
	 */
	protected $taxonomies = array();

	/**
	 * Post types to be registered.
	 *
	 * @var array
	 */
	protected $post_types = array();

	/**
	 * Meta boxes to be registered.
	 *
	 * @var array
	 */
	protected $meta_boxes = array();

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @return $this Init
	 */
	public function prepare(): self {
		$this->loader = new Loader();

		$this->prepare_general();

		$this->set_locale();

		$this->register_post_statuses();
		$this->register_post_types();
		$this->register_taxonomies();
		$this->schedule_jobs();
		Enqueue::init( $this->loader );

		if ( is_admin() === true ) {
			$this->prepare_admin();

			Menu::init( $this->loader );
			$this->register_meta_boxes();
		} else {
			$this->prepare_frontend();
		}

		return $this;
	}

	/**
	 * Used to register general hooks for both admin and frontend
	 */
	protected function prepare_general(): void {

	}

	/**
	 * Admin-only hooks
	 */
	protected function prepare_admin(): void {
	}

	/**
	 * Frontend hooks only
	 */
	protected function prepare_frontend(): void {
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Dom_Woo_Customize_Login_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function set_locale(): void {
	}

	/**
	 * Register Custom Post Statuses
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function register_post_statuses(): void {
	}

	/**
	 * Register Custom Post Types
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function register_post_types(): void {
		foreach ( $this->post_types as $post_type ) {
			$post_type::init( $this->loader );
		}
	}

	/**
	 * Register Custom Meta Boxes
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function register_meta_boxes(): void {
		foreach ( $this->meta_boxes as $key => $meta_box ) {
			$priority = $key + 10;
			$meta_box::init( $this->loader, $priority );
		}
	}

	/**
	 * Register Custom Taxonomies
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function register_taxonomies(): void {
		foreach ( $this->taxonomies as $taxonomy ) {
			$taxonomy::init( $this->loader );
		}
	}

	/**
	 * Register Scheduled Jobs
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function schedule_jobs(): void {
		$jobs = array();

		if ( true === empty( $jobs ) ) {
			return;
		}

		foreach ( $jobs as $job ) {
			$this->loader->add_action( 'init', $job, 'schedule' );
			$this->loader->add_action( $job->get_schedule_name(), $job, 'execute' );
		}
	}

	/**
	 * Add Custom Post Type Class
	 *
	 * @param string $post_type_class Post_Type class path.
	 *
	 * @return Init
	 * @since 2.0.0
	 * @access protected
	 */
	protected function add_post_type( string $post_type_class ): self {
		$this->post_types[] = $post_type_class;

		return $this;
	}

	/**
	 * Add Custom Taxonomy Class
	 *
	 * @param string $taxonomy_class Taxonomy class path.
	 *
	 * @return Init
	 * @since 2.0.0
	 * @access protected
	 */
	protected function add_taxonomy( string $taxonomy_class ): self {
		$this->taxonomies[] = $taxonomy_class;

		return $this;
	}

	/**
	 * Add Meta Box Class
	 *
	 * @param string $meta_box_class Meta_Box class path.
	 *
	 * @return Init
	 * @since 2.0.0
	 * @access protected
	 */
	protected function add_meta_box( string $meta_box_class ): self {
		$this->meta_boxes[] = $meta_box_class;

		return $this;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 2.0.0
	 */
	public function run(): void {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return string The name of the plugin.
	 * @since 2.0.0
	 */
	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 * @since     0.9.0
	 */
	public function get_loader(): Loader {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     0.9.0
	 */
	public function get_version(): string {
		return $this->version;
	}
}
