<?php
/**
 * Post Status generator
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core;
/**
 * Class Plugin_Dependencies
 */
abstract class Post_Status {

	protected $loader;

	protected $name;
	protected $label = '';
	protected $label_count = '';
	protected $exclude_from_search = false;
	private $_builtin = false;
	protected $public = true;
	protected $internal = true;
	protected $private = false;
	protected $protected = false;
	protected $publicly_queryable = true;
	protected $show_in_admin_status_list = true;
	protected $show_in_admin_all_list = true;
	protected $date_floating = false;

	public function __construct( Loader $loader ) {
		$this->loader = $loader;
	}

	public function init() {
		$this->loader->add_action( 'init', $this, 'register' );
	}

	public function register() {
		register_post_status( $this->name, $this->get_args() );
	}

	protected function get_args(): array {
		return [
			'label'                     => $this->label,
			'label_count'               => $this->label_count,
			'exclude_from_search'       => $this->exclude_from_search,
			'_builtin'                  => $this->_builtin,
			'public'                    => $this->public,
			'internal'                  => $this->internal,
			'protected'                 => $this->protected,
			'private'                   => $this->private,
			'publicly_queryable'        => $this->publicly_queryable,
			'show_in_admin_status_list' => $this->show_in_admin_status_list,
			'show_in_admin_all_list'    => $this->show_in_admin_all_list,
			'date_floating'             => $this->date_floating,
		];
	}
}