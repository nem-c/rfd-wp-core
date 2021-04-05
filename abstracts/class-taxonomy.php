<?php
/**
 * Taxonomy generator
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core\Abstracts;

use RFD\Core\Loader;

/**
 * Class Taxonomy
 *
 * @package RFD\Core\Abstracts
 */
abstract class Taxonomy {

	/**
	 * Taxonomy name.
	 *
	 * @var string
	 */
	protected $name = 'custom_taxonomy';

	/**
	 * Taxonomy belongs to post types.
	 *
	 * @var array
	 */
	protected $objects = array();

	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	protected $slug = 'custom-taxonomy';

	/**
	 * Lang domain to be used.
	 *
	 * @var string
	 */
	protected $lang_domain = '';

	/**
	 * Taxonomy singular label.
	 *
	 * @var string
	 */
	protected $singular_label = 'Taxonomy';

	/**
	 * Taxonomy plural label
	 *
	 * @var string
	 */
	protected $plural_label = 'Taxonomies';

	/**
	 * Taxonomy is hierarchical.
	 *
	 * @var bool
	 */
	protected $hierarchical = true;

	/**
	 * Taxonomy show in UI.
	 *
	 * @var bool
	 */
	protected $show_ui = true;

	/**
	 * Taxonomy show admin column.
	 *
	 * @var bool
	 */
	protected $show_admin_column = true;

	/**
	 * Taxonomy add query var.
	 *
	 * @var bool
	 */
	protected $query_var = true;

	/**
	 * Taxonomy show in REST.
	 *
	 * @var bool
	 */
	protected $show_in_rest = true;

	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 */
	final public static function init( Loader $loader ): void {
		$taxonomy = new static(); // @phpstan-ignore-line
		$loader->add_action( 'init', $taxonomy, 'register' );
	}

	/**
	 * Register taxonomy.
	 */
	public function register(): void {
		register_taxonomy( $this->name, $this->objects, $this->get_args() );
	}

	/**
	 * Taxonomy arguments.
	 *
	 * @return array
	 */
	public function get_args(): array {
		return array(
			'hierarchical'      => $this->hierarchical,
			'labels'            => $this->get_labels(),
			'show_ui'           => $this->show_ui,
			'show_admin_column' => $this->show_admin_column,
			'query_var'         => $this->query_var,
			'rewrite'           => array(
				'slug' => $this->slug,
			),
			'show_in_rest'      => $this->show_in_rest,
			'rest_base'         => $this->slug,
		);
	}

	/**
	 * Get taxonomy labels.
	 *
	 * @return array
	 */
	public function get_labels(): array {
		return array(
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'name'              => _x( $this->plural_label, 'Custom taxonomy general name', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'singular_name'     => _x( $this->singular_label, 'Custom taxonomy singular name', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'search_items'      => __( 'Search ' . $this->plural_label, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'all_items'         => __( 'All ' . $this->plural_label, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'parent_item'       => __( 'Parent ' . $this->singular_label, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'parent_item_colon' => __( 'Parent :' . $this->singular_label, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'edit_item'         => __( 'Edit ' . $this->singular_label, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'update_item'       => __( 'Update ' . $this->singular_label, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'add_new_item'      => __( 'Add New ' . $this->singular_label, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'new_item_name'     => __( 'New ' . $this->singular_label . ' Name', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'menu_name'         => __( $this->plural_label, $this->lang_domain ),
		);
	}
}
