<?php
/**
 * Post Status generator
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core\Abstracts;

use RFD\Core\Loader;

abstract class Post_Status {

	protected Loader $loader; //phpcs:ignore Generic.PHP.Syntax.PHPSyntax

	protected string $name = '';

	protected string $label = '';

	protected string $label_count = '';

	protected bool $exclude_from_search = false;

	protected bool $public = true;

	protected bool $internal = true;

	protected bool $private = false;

	protected bool $protected = false;

	protected bool $publicly_queryable = true;

	protected bool $show_in_admin_status_list = true;

	protected bool $show_in_admin_all_list = true;

	protected bool $date_floating = false;

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
		return array(
			'label'                     => $this->label,
			'label_count'               => $this->label_count,
			'exclude_from_search'       => $this->exclude_from_search,
			'_builtin'                  => false,
			'public'                    => $this->public,
			'internal'                  => $this->internal,
			'protected'                 => $this->protected,
			'private'                   => $this->private,
			'publicly_queryable'        => $this->publicly_queryable,
			'show_in_admin_status_list' => $this->show_in_admin_status_list,
			'show_in_admin_all_list'    => $this->show_in_admin_all_list,
			'date_floating'             => $this->date_floating,
		);
	}
}
