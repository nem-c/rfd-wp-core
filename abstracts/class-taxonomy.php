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
	protected $name = 'custom_taxonomy';
	protected $objects = [];
	protected $slug = 'custom-taxonomy';
	protected $lang_domain = '';

	protected $singular_label = 'Taxonomy';
	protected $plural_label = 'Taxonomies';

	protected $hierarchical = true;
	protected $show_ui = true;
	protected $show_admin_column = true;
	protected $query_var = true;
	protected $show_in_rest = true;

	final public static function init( Loader &$loader ) {
		$taxonomy = new static();
		$loader->add_action( 'init', $taxonomy, 'register' );
	}

	public function register() {
		register_taxonomy( $this->name, $this->objects, $this->get_args() );
	}

	public function get_args() {
		return [
			'hierarchical'      => $this->hierarchical,
			'labels'            => $this->get_labels(),
			'show_ui'           => $this->show_ui,
			'show_admin_column' => $this->show_admin_column,
			'query_var'         => $this->query_var,
			'rewrite'           => [
				'slug' => $this->slug,
			],
			'show_in_rest'      => $this->show_in_rest,
			'rest_base'         => $this->slug,
		];
	}

	public function get_labels() {
		return [
			'name'              => _x( $this->plural_label, 'Custom taxonomy general name', $this->lang_domain ),
			'singular_name'     => _x( $this->singular_label, 'Custom taxonomy singular name', $this->lang_domain ),
			'search_items'      => __( 'Search ' . $this->plural_label, $this->lang_domain ),
			'all_items'         => __( 'All ' . $this->plural_label, $this->lang_domain ),
			'parent_item'       => __( 'Parent ' . $this->singular_label, $this->lang_domain ),
			'parent_item_colon' => __( 'Parent :' . $this->singular_label, $this->lang_domain ),
			'edit_item'         => __( 'Edit ' . $this->singular_label, $this->lang_domain ),
			'update_item'       => __( 'Update ' . $this->singular_label, $this->lang_domain ),
			'add_new_item'      => __( 'Add New ' . $this->singular_label, $this->lang_domain ),
			'new_item_name'     => __( 'New ' . $this->singular_label . ' Name', $this->lang_domain ),
			'menu_name'         => __( $this->plural_label, $this->lang_domain ),
		];
	}
}