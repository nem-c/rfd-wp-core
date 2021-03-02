<?php
/**
 * Post Type generator
 *
 * @link       https://rfd.rs/
 * @since      2.0.0
 *
 * @package    RFD\Core
 * @subpackage RFD\Core\Abstracts
 */

namespace RFD\Core\Abstracts;

use RFD\Core\Loader;

abstract class Post_Type {
	protected string $name = 'post_type'; //phpcs:ignore Generic.PHP.Syntax.PHPSyntax

	protected string $slug = 'post-type';

	protected string $menu_title = 'Posts';

	protected string $admin_bar_title = 'Posts';

	protected string $singular_label = 'Post';

	protected string $plural_label = 'Posts';

	protected string $description = 'Posts Custom Post Type';

	protected string $lang_domain = '';

	protected bool $with_front = true;

	protected bool $pages = true;

	protected bool $feeds = true;

	protected bool $hierarchical = true;

	protected bool $public = true;

	protected bool $show_ui = true;

	protected bool $show_in_menu = true;

	protected bool $remove_menu_item = false;

	protected int $menu_position = 50;

	protected bool $show_in_admin_bar = true;

	protected bool $show_in_nav_menus = true;

	protected bool $can_export = true;

	protected bool $has_archive = true;

	protected bool $exclude_from_search = false;

	protected bool $publicly_queryable = true;

	protected string $capability_type = 'page';

	protected array $supports = array();

	protected bool $show_in_rest = true;

	final public static function init( Loader $loader ) {
		$post_type = new static();

		$loader->add_action( 'init', $post_type, 'register' );
		$loader->add_action( 'admin_menu', $post_type, 'remove_menu_item' );
	}

	public function register() {
		register_post_type( $this->name, $this->get_args() );
	}

	public function remove_menu_item() {
		if ( true === $this->remove_menu_item ) {
			remove_menu_page( 'edit.php?post_type=' . $this->name );
		}
	}

	private function rewrite(): array {
		return array(
			'slug'       => $this->slug,
			'with_front' => $this->with_front,
			'pages'      => $this->pages,
			'feeds'      => $this->feeds,
		);
	}

	private function get_labels(): array {
		return array(
			'name'                  => _x( $this->plural_label, 'Post Type General Name', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'singular_name'         => _x( $this->singular_label, 'Post Type Singular Name', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'menu_name'             => __( $this->menu_title, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'name_admin_bar'        => __( $this->admin_bar_title, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'archives'              => __( 'Archives', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'attributes'            => __( 'Attributes', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'parent_item_colon'     => __( 'Parent Item:', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'all_items'             => __( 'All ' . ucfirst( $this->plural_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'add_new_item'          => __( 'Add New ' . ucfirst( $this->singular_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'add_new'               => __( 'Add New', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'new_item'              => __( 'New ' . ucfirst( $this->singular_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'edit_item'             => __( 'Edit ' . ucfirst( $this->singular_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'update_item'           => __( 'Update ' . ucfirst( $this->singular_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'view_item'             => __( 'View ' . ucfirst( $this->singular_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'view_items'            => __( 'View ' . ucfirst( $this->plural_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'search_items'          => __( 'Search ' . ucfirst( $this->plural_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'not_found'             => __( 'Not found', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'not_found_in_trash'    => __( 'Not found in Trash', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'featured_image'        => __( 'Featured Image', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'set_featured_image'    => __( 'Set featured image', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'remove_featured_image' => __( 'Remove featured image', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'use_featured_image'    => __( 'Use as featured image', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'insert_into_item'      => __( 'Insert into ' . ucfirst( $this->singular_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'uploaded_to_this_item' => __( 'Uploaded to this ' . ucfirst( $this->singular_label ), $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'items_list'            => __( ucfirst( $this->plural_label ) . ' list', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'items_list_navigation' => __( ucfirst( $this->singular_label ) . ' list navigation', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'filter_items_list'     => __( 'Filter ' . ucfirst( $this->plural_label ) . ' List', $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
		);
	}

	private function get_args(): array {
		return array(
			'label'               => __( $this->singular_label, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'description'         => __( $this->description, $this->lang_domain ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
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
		);
	}
}
