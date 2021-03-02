<?php
/**
 * Post Type generator
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
abstract class Post_Type {
	protected $name = 'post_type';
	protected $slug = 'post-type';
	protected $menu_title = 'Posts';
	protected $admin_bar_title = 'Posts';
	protected $singular_label = 'Post';
	protected $plural_label = 'Posts';
	protected $description = 'Posts Custom Post Type';
	protected $lang_domain = '';

	protected $with_front = true;
	protected $pages = true;
	protected $feeds = true;

	protected $hierarchical = true;
	protected $public = true;
	protected $show_ui = true;
	protected $show_in_menu = true;
	protected $menu_position = 50;
	protected $show_in_admin_bar = true;
	protected $show_in_nav_menus = true;
	protected $can_export = true;
	protected $has_archive = true;
	protected $exclude_from_search = false;
	protected $publicly_queryable = true;

	protected $capability_type = 'page';
	protected $supports = [];

	protected $show_in_rest = true;

	public function register() {
		register_post_type( $this->name, $this->get_args() );
	}

	private function rewrite() {
		return [
			'slug'       => $this->slug,
			'with_front' => $this->with_front,
			'pages'      => $this->pages,
			'feeds'      => $this->feeds,
		];
	}

	private function get_labels() {
		return [
			'name'                  => _x( $this->plural_label, 'Post Type General Name', $this->lang_domain ),
			'singular_name'         => _x( $this->singular_label, 'Post Type Singular Name', $this->lang_domain ),
			'menu_name'             => __( $this->menu_title, $this->lang_domain ),
			'name_admin_bar'        => __( $this->admin_bar_title, $this->lang_domain ),
			'archives'              => __( 'Archives', $this->lang_domain ),
			'attributes'            => __( 'Attributes', $this->lang_domain ),
			'parent_item_colon'     => __( 'Parent Item:', $this->lang_domain ),
			'all_items'             => __( 'All ' . ucfirst( $this->plural_label ), $this->lang_domain ),
			'add_new_item'          => __( 'Add New ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			'add_new'               => __( 'Add New', $this->lang_domain ),
			'new_item'              => __( 'New ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			'edit_item'             => __( 'Edit ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			'update_item'           => __( 'Update ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			'view_item'             => __( 'View ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			'view_items'            => __( 'View ' . ucfirst( $this->plural_label ), $this->lang_domain ),
			'search_items'          => __( 'Search ' . ucfirst( $this->plural_label ), $this->lang_domain ),
			'not_found'             => __( 'Not found', $this->lang_domain ),
			'not_found_in_trash'    => __( 'Not found in Trash', $this->lang_domain ),
			'featured_image'        => __( 'Featured Image', $this->lang_domain ),
			'set_featured_image'    => __( 'Set featured image', $this->lang_domain ),
			'remove_featured_image' => __( 'Remove featured image', $this->lang_domain ),
			'use_featured_image'    => __( 'Use as featured image', $this->lang_domain ),
			'insert_into_item'      => __( 'Insert into ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			'uploaded_to_this_item' => __( 'Uploaded to this ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			'items_list'            => __( ucfirst( $this->plural_label ) . ' list', $this->lang_domain ),
			'items_list_navigation' => __( ucfirst( $this->singular_label ) . ' list navigation', $this->lang_domain ),
			'filter_items_list'     => __( 'Filter ' . ucfirst( $this->plural_label ) . ' List', $this->lang_domain ),
		];
	}

	private function get_args() {
		return [
			'label'               => __( $this->singular_label, $this->lang_domain ),
			'description'         => __( $this->description, $this->lang_domain ),
			'labels'              => $this->get_labels(),
			'supports'            => $this->supports,
			'hierarchical'        => $this->hierarchical,
			'public'              => $this->public,
			'show_ui'             => $this->show_ui,
			'show_in_menu'        => $this->show_in_menu,
			'menu_position'       => $this->menu_position,
			'show_in_admin_bar'   => $this->show_in_admin_bar,
			'show_in_nav_menus'   => $this->show_in_nav_menus,
			'can_export'          => $this->can_export,
			'has_archive'         => $this->has_archive,
			'exclude_from_search' => $this->exclude_from_search,
			'publicly_queryable'  => $this->publicly_queryable,
			'rewrite'             => $this->rewrite(),
			'capability_type'     => $this->capability_type,
			'show_in_rest'        => $this->show_in_rest,
		];
	}
}