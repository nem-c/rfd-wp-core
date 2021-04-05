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

/**
 * Class Input
 *
 * @package RFD\Core
 */
class Input {

	/**
	 * Options
	 *
	 * @var array
	 */
	protected static $options = array();

	/**
	 * Render input element.
	 *
	 * @param array $args Arguments.
	 * @param array $options Options.
	 *
	 * @return string
	 */
	public static function render( array $args, $options = array() ): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		$defaults = array(
			'id'          => '',
			'field_name'  => '',
			'field_value' => '',
			'type'        => '',
			'title'       => '',
			'description' => '',
			'options'     => array(),
			'editor'      => array(
				'visual'        => true,
				'teeny'         => true,
				'textarea_rows' => 4,
			),
			'atts'        => array(),
		);

		$configs = array_replace_recursive( $defaults, $args );
		extract( $configs, EXTR_OVERWRITE ); //phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		/**
		 * Extracted variables
		 *
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

		if ( true === empty( $type ) ) {
			return '<!-- Type it not defined. -->';
		}
		if ( true === empty( $field_name ) ) {
			return '<!-- Field Name it not defined. -->';
		}
		if ( true === empty( $id ) ) {
			return '<!-- ID it not defined. -->';
		}

		/**
		 * PHPStan Fixes
		 */
		if ( false === isset( $field_value ) ) {
			$field_value = '';
		}
		if ( false === isset( $title ) ) {
			$title = '';
		}

		if (
			true === in_array(
				$type,
				array(
					'select',
					'cats',
					'categories',
					'checkbox_group',
				),
				true
			)
			&& false === empty( $atts )
			&& true === array_key_exists( 'multiple', $atts )
		) {
			$multiple = true;
		} else {
			$multiple = false;
		}

		$editor['textarea_name'] = $field_name;

		$attributes = '';
		if ( isset( $atts ) && false === empty( $atts ) ) {
			foreach ( $atts as $attribute => $attr_value ) {
				$attributes .= $attribute . '="' . $attr_value . '"';
			}
		}

		$input = '';

		$method = 'render_' . $type;
		if ( true === method_exists( __CLASS__, $method ) ) {
			$input = self::$method( $id, $field_name, $field_value, $type, $title, $options, $editor, $attributes, $multiple );
		}

		$html = $input;
		if ( ! empty( $description ) ) {
			$html .= '<p class="description">' . $description . '</p>';
		}

		return $html;
	}

	/**
	 * Render radio field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_radio( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		$input = '<fieldset>';
		foreach ( $options as $key => $option ) {
			$input .= '<label title="' . $option . '">';
			$input .= '<input type="radio" name="' . $field_name . '" value="' . $key . '" ' . ( $value === $key ? 'checked="checked"' : '' ) . ' />';
			$input .= '<span>' . $option . '</span>';
			$input .= '</label><br />';
		}
		$input .= '</fieldset>';

		return $input;
	}

	/**
	 * Render textarea field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_textarea( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		$value = self::cast_to_string( $value );
		if ( true === $editor['visual'] ) {
			ob_start();
			wp_editor( $value, $id, $editor );
			$input = ob_get_clean();
		} else {
			$input = '<textarea name="' . $field_name . '" id="' . $id . '" ' . $attributes . '>' . $value . '</textarea>';
		}

		// cast to string explicitly in case wp_editor returns false.
		$input = (string) $input;

		return $input;
	}

	/**
	 * Render select field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_select( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		$input = '<select name="' . $field_name . ( $multiple ? '[]' : '' ) . '" id="' . $id . '" ' . $attributes . '>';

		$input .= '<option value="0">&ndash; ' . __( 'Select', 'rfd-wp-core' ) . ' &ndash;</option>';
		foreach ( $options as $key => $option ) {
			if ( true === $multiple ) {
				// cast possible string to array.
				$value    = self::cast_to_array( $value );
				$selected = ( in_array( $key, $value, true ) ? 'selected="selected"' : '' );
			} else {
				$value    = self::cast_to_string( $value );
				$selected = ( $value === $key ? 'selected="selected"' : '' );
			}
			$input .= '<option ' . $selected . ' value="' . $key . '">' . $option . '</option>';
		}
		$input .= '</select>';

		return $input;
	}

	/**
	 * Render categories field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_categories( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		$input = '<select name="' . $field_name . ( $multiple ? '[]' : '' ) . '" id="' . $id . '" ' . $attributes . '>';

		$input .= '<option value="0">&ndash; ' . __( 'Select', 'rfd-wp-core' ) . ' &ndash;</option>';
		foreach ( get_categories( array( 'hide_empty' => false ) ) as $cat ) {
			if ( $multiple ) {
				$value    = self::cast_to_array( $value );
				$selected = ( true === in_array( $cat->cat_ID, $value, true ) ? 'selected="selected"' : '' );
			} else {
				$value    = self::cast_to_string( $value );
				$selected = ( $value === $cat->cat_ID ? 'selected="selected"' : '' );
			}
			$input .= '<option ' . $selected . ' value="' . $cat->cat_ID . '">' . $cat->cat_name . '</option>';
		}
		$input .= '</select>';

		return $input;
	}

	/**
	 * Render cats field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_cats( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		return self::render_categories( $id, $field_name, $value, $type, $title, $options, $editor, $attributes, $multiple );
	}

	/**
	 * Render thumbnails field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_thumbnails( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		$input = '<select name="' . $field_name . '" id="' . $id . '" "' . $attributes . '">';

		$input .= '<option value="0">&ndash; ' . __( 'Select', 'rfd-wp-core' ) . ' &ndash;</option>';
		foreach ( self::get_image_sizes() as $thumbnail => $size ) {
			$input .= '<option ' . ( $value === $thumbnail ? 'selected="selected"' : '' ) . ' value="' . $thumbnail . '>' . $thumbnail . ' - ' . $size['width'] . 'x' . $size['height'] . 'px</option>';
		}
		$input .= '</select>';

		return $input;
	}

	/**
	 * Render image field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_image( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		$value = self::cast_to_string( $value );
		$input = '<input id="' . $id . '" type="text" size="36" name="' . $field_name . '" placeholder="http://..." value="' . $value . '" />';

		$input .= '<input class="button image-upload" data-field="#' . $id . '" type="button" value="' . __( 'Upload Image', 'rfd-wp-core' ) . '" />';

		return $input;
	}

	/**
	 * Render checkbox field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_checkbox( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		if ( true === $multiple ) {
			$input = self::render_checkbox_group( $id, $field_name, $value, $type, $title, $options, $editor, $attributes, $multiple );
		} else {
			$input = '<fieldset class="checkbox-label aus-label">';

			$input .= '<label title="' . $id . '">';
			$input .= '<input name="' . $field_name . '" id="' . $id . '" type="' . $type . '" value="1"' . $attributes . ( $value ? 'checked = "checked"' : '' ) . ' />';
			$input .= $title;
			$input .= '</label > ';
			$input .= '<span class="checkbox ' . ( $value ? 'checked' : '' ) . '"></span>';
			$input .= '</fieldset>';
		}

		return $input;
	}

	/**
	 * Render checkbox group.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_checkbox_group( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		$value = self::cast_to_array( $value );
		$input = '<fieldset class="checkbox-label aus-label">';
		foreach ( $options as $option_value => $option_label ) {
			$unique_id = sanitize_title_with_dashes( $id . '-' . $option_value );
			$checked   = in_array( $option_value, $value, true );

			$input .= '<label for="' . $unique_id . '" title="' . $option_label . '">';
			$input .= '<input name="' . $field_name . '[]" id="' . $unique_id . '" type="checkbox" value="' . $option_value . '" ' . $attributes . ' ' . ( $checked ? 'checked="checked"' : '' ) . '/>';
			$input .= $option_label;
			$input .= '</label > ';
			$input .= '<span class="checkbox ' . ( $checked ? 'checked' : '' ) . '"></span>';
		}
		$input .= '</fieldset>';

		return $input;
	}

	/**
	 * Render text field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_date( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		$value = self::cast_to_string( $value );

		return '<input name="' . $field_name . '" id="' . $id . '" type="' . $type . '" value="' . $value . '"' . $attributes . ' />';
	}

	/**
	 * Render text field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_text( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		$value = self::cast_to_string( $value );

		return '<input name="' . $field_name . '" id="' . $id . '" type="' . $type . '" value="' . $value . '"' . $attributes . ' />';
	}

	/**
	 * Render email field.
	 *
	 * @param string $id id attribute.
	 * @param string $field_name field attribute.
	 * @param string|array $value input value.
	 * @param string $type input type.
	 * @param string $title title.
	 * @param array $options additional options.
	 * @param array $editor wp-editor.
	 * @param string $attributes additional attributes.
	 * @param bool $multiple Multiple selection.
	 *
	 * @return string
	 */
	protected static function render_email( string $id, string $field_name, $value, string $type, string $title, array $options, array $editor, string $attributes, bool $multiple ): string {
		return self::render_text( $id, $field_name, $value, $type, $title, $options, $editor, $attributes, $multiple );
	}

	/**
	 * Get image sizes.
	 *
	 * @param string $size Default image size.
	 *
	 * @return array|false|mixed
	 */
	private static function get_image_sizes( $size = '' ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		global $_wp_additional_image_sizes;
		$sizes = array();

		// Create the full array with sizes and crop info.
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ), true ) ) {
				$sizes[ $_size ]['width']  = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop']   = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}

		// Get only 1 size if found.
		if ( $size ) {
			if ( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}

		return $sizes;
	}

	/**
	 * Cast possible string to array.
	 *
	 * @param string|array $val Value.
	 *
	 * @return array
	 */
	private static function cast_to_array( $val ): array {
		if ( false === is_array( $val ) ) {
			$val = array( $val );
		}

		return $val;
	}

	/**
	 * Cast possible array to string by using first element only.
	 *
	 * @param string|array $val Value.
	 *
	 * @return string
	 */
	private static function cast_to_string( $val ): string {
		if ( true === is_array( $val ) ) {
			$val = current( $val );
		}

		return $val;
	}
}
