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

abstract class Taxonomy {
	protected string $name = 'custom_taxonomy';//phpcs:ignore Generic.PHP.Syntax.PHPSyntax

	protected array $objects = array();

	protected string $slug = 'custom-taxonomy';

	protected string $lang_domain = '';

	protected string $singular_label = 'Taxonomy';

	protected string $plural_label = 'Taxonomies';

	protected bool $hierarchical = true;

	protected bool $show_ui = true;

	protected bool $show_admin_column = true;

	protected bool $query_var = true;

	protected bool $show_in_rest = true;

	final public static function init( Loader $loader ) {
		$taxonomy = new static();
		$loader->add_action( 'init', $taxonomy, 'register' );
	}

	public function register() {
		register_taxonomy( $this->name, $this->objects, $this->get_args() );
	}

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

	public function get_labels(): array {
		return array(
			'name'              => _x( $this->plural_label, 'Custom taxonomy general name', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'singular_name'     => _x( $this->singular_label, 'Custom taxonomy singular name', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'search_items'      => __( 'Search ' . $this->plural_label, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'all_items'         => __( 'All ' . $this->plural_label, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'parent_item'       => __( 'Parent ' . $this->singular_label, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'parent_item_colon' => __( 'Parent :' . $this->singular_label, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'edit_item'         => __( 'Edit ' . $this->singular_label, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'update_item'       => __( 'Update ' . $this->singular_label, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'add_new_item'      => __( 'Add New ' . $this->singular_label, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'new_item_name'     => __( 'New ' . $this->singular_label . ' Name', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'menu_name'         => __( $this->plural_label, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
		);
	}
}
