<?php

/**
 * Input generator for DOM plugins
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package RFD\Core
 */

namespace RFD\Core;

class Input {
	protected static $options = [];

	public static function render( $args, $options = [] ) {
		$defaults = [
			'id'          => '',
			'field_name'  => '',
			'field_value' => '',
			'type'        => '',
			'title'       => '',
			'description' => '',
			'options'     => [],
			'editor'      => [
				'visual'        => true,
				'teeny'         => true,
				'textarea_rows' => 4,
			],
			'atts'        => [],
		];

		$configs = array_replace_recursive( $defaults, $args );
		extract( $configs, EXTR_OVERWRITE );
		/**
		 * @var $id
		 * @var $field_name
		 * @var $field_value
		 * @var $type
		 * @var $title
		 * @var $description
		 * @var $options
		 * @var $editor
		 * @var $atts
		 */

		if ( ( $type == 'select' || $type == 'cats' || $type == 'categories' ) && ! empty( $atts ) && array_key_exists( 'multiple', $atts ) ) {
			$multiple = true;
		} else {
			$multiple = false;
		}


		$value = $field_value;

		$editor['textarea_name'] = $field_name;

		$attributes = "";
		if ( isset( $atts ) and ! empty( $atts ) ) {
			foreach ( $atts as $attribute => $attr_value ) {
				$attributes .= $attribute . '="' . $attr_value . '"';
			}
		}

		switch ( $type ) {
			case "radio":
				$input = "<fieldset>";
				foreach ( $options as $key => $option ) {
					$input .= "<label title=\"" . $option . "\">";
					$input .= "<input type=\"radio\" name=\"" . $field_name . "\" value=\"" . $key . "\" " . ( $value == $key ? "checked=\"checked\"" : "" ) . " />";
					$input .= "<span>" . $option . "</span>";
					$input .= "</label><br />";
				}
				$input .= "</fieldset>";
				break;
			case "textarea":
				if ( $editor["visual"] === true ) {
					ob_start();
					wp_editor( $value, $id, $editor );
					$input = ob_get_contents();
					ob_end_clean();
				} else {
					$input = "<textarea name=\"" . $field_name . "\" id=\"" . $id . "\"" . $attributes . ">" . $value . "</textarea>";
				}
				break;
			case "select":
				$input = "<select name=\"" . $field_name . ( $multiple ? "[]" : "" ) . "\" id=\"" . $id . "\" " . $attributes . ">";
				$input .= "<option value=\"0\">&ndash; " . __( "Select", "dom-core" ) . " &ndash;</option>";
				foreach ( $options as $key => $option ) {
					if ( $multiple ) {
						$selected = ( in_array( $key, $value ) ? "selected=\"selected\"" : "" );
					} else {
						$selected = ( $value == $key ? "selected=\"selected\"" : "" );
					}
					$input .= "<option " . $selected . " value=\"" . $key . "\">" . $option . "</option>";
				}
				$input .= "</select>";
				break;
			case "categories":
			case "cats":
				$input = "<select name=\"" . $field_name . ( $multiple ? "[]" : "" ) . "\" id=\"" . $id . "\" " . $attributes . ">";
				$input .= "<option value=\"0\">&ndash; " . __( "Select", "dom-core" ) . " &ndash;</option>";
				foreach ( get_categories( [ "hide_empty" => false ] ) as $cat ) {
					if ( $multiple ) {
						$selected = ( in_array( $cat->cat_ID, $value ) ? "selected=\"selected\"" : "" );
					} else {
						$selected = ( $value == $cat->cat_ID ? "selected=\"selected\"" : "" );
					}
					$input .= "<option " . $selected . " value=\"" . $cat->cat_ID . "\">" . $cat->cat_name . "</option>";
				}
				$input .= "</select>";
				break;
			case "thumbnails":
				$input = "<select name=\"" . $field_name . "\" id=\"" . $id . "\" " . $attributes . ">";
				$input .= "<option value=\"0\">&ndash; " . __( "Select", "dom-core" ) . " &ndash;</option>";
				foreach ( self::get_image_sizes() as $thumbnail => $size ) {
					$input .= "<option " . ( $value == $thumbnail ? "selected=\"selected\"" : "" ) . " value=\"" . $thumbnail . "\">" . $thumbnail . " - " . $size["width"] . "x" . $size["height"] . "px</option>";
				}
				$input .= "</select>";
				break;
			case "image":
				$input = "<input id=\"" . $id . "\" type=\"text\" size=\"36\" name=\"" . $field_name . "\" placeholder=\"http://...\" value=\"" . $value . "\" />";
				$input .= "<input class=\"button image-upload\" data-field=\"#" . $id . "\" type=\"button\" value=\"" . __( "Upload Image", "dom-core" ) . "\" />";
				break;
			case "checkbox":
				$input = "<fieldset class=\"checkbox-label aus-label\">";
				$input .= "<label title=\"" . $id . "\">";
				$input .= "<input name=\"" . $field_name . "\" id=\"" . $id . "\" type=\"" . $type . "\" value=\"1\"" . $attributes . ( $value ? "checked=\"checked\"" : "" ) . " />";
				$input .= $title;
				$input .= "</label>";
				$input .= "<span class=\"checkbox" . ( $value ? " checked" : "" ) . "\"></span>";
				$input .= "</fieldset>";
				break;
			case "date":
				$input = "<input name=\"" . $field_name . "\" id=\"" . $id . "\" type=\"text\" value=\"" . $value . "\"" . $attributes . " />";
				break;
			default:
			case "email":
			case "text":
				$input = "<input name=\"" . $field_name . "\" id=\"" . $id . "\" type=\"" . $type . "\" value=\"" . $value . "\"" . $attributes . " />";
				break;
		}
		$html = "";
		$html .= $input;
		if ( ! empty( $description ) ) {
			$html .= "<p class=\"description\">" . $description . "</p>";
		}

		return $html;
	}

	private static function get_image_sizes( $size = '' ) {
		global $_wp_additional_image_sizes;
		$sizes                        = [];
		$get_intermediate_image_sizes = get_intermediate_image_sizes();
		// Create the full array with sizes and crop info
		foreach ( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, [ 'thumbnail', 'medium', 'large' ] ) ) {
				$sizes[ $_size ]['width']  = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop']   = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = [
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				];
			}
		}

		// Get only 1 size if found
		if ( $size ) {
			if ( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}

		return $sizes;
	}
}