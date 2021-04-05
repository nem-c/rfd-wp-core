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

/**
 * Class Post_Status
 *
 * @package RFD\Core\Abstracts
 */
abstract class Post_Status {

	/**
	 * Loader object.
	 *
	 * @var Loader
	 */
	protected $loader;

	/**
	 * Post status name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Post status label.
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Post status label count.
	 *
	 * @var string
	 */
	protected $label_count = '';

	/**
	 * Exclude post status from search.
	 *
	 * @var bool
	 */
	protected $exclude_from_search = false;

	/**
	 * Post status is public.
	 *
	 * @var bool
	 */
	protected $public = true;

	/**
	 * Post status is internal only.
	 *
	 * @var bool
	 */
	protected $internal = false;

	/**
	 * Post status is private.
	 *
	 * @var bool
	 */
	protected $private = false;

	/**
	 * Post status is protected.
	 *
	 * @var bool
	 */
	protected $protected = false;

	/**
	 * Post status can be queried publicly.
	 *
	 * @var bool
	 */
	protected $publicly_queryable = true;

	/**
	 * Show post status in admin list.
	 *
	 * @var bool
	 */
	protected $show_in_admin_status_list = true;

	/**
	 * Show post status in admin filters.
	 *
	 * @var bool
	 */
	protected $show_in_admin_all_list = true;

	/**
	 * Post status is date floating.
	 *
	 * @var bool
	 */
	protected $date_floating = false;

	/**
	 * Init post status
	 *
	 * @param Loader $loader Loader object.
	 */
	final public static function init( Loader $loader ): void {
		$post_status = new static(); // @phpstan-ignore-line

		$loader->add_action( 'init', $post_status, 'register' );
	}

	/**
	 * Register post status
	 */
	public function register(): void {
		register_post_status( $this->name, $this->get_args() );
	}

	/**
	 * Generate arguments array from class attributes.
	 *
	 * @return array
	 */
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
