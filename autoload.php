<?php
/**
 * Automatically locates and loads files based on their namespaces and their
 * file names whenever they are instantiated.
 *
 * @package RFD\Core
 * @subpackage RFD\Core\Autoloader
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once 'constants.php';

spl_autoload_register(
	function ( $filename ) {
		// First, separate the components of the incoming file.
		$file_path = explode( '\\', $filename );

		if ( true === empty( $file_path ) || count( $file_path ) < 3 ) {
			return;
		}
		if ( 'RFD' !== $file_path[0] ) {
			return;
		}

		/**
		 * - The first index will always be the namespace since it's part of the plugin.
		 * - All but the last index will be the path to the file.
		 * - The final index will be the filename. If it doesn't begin with 'I' then it's a class.
		 */
		// Get the last index of the array. This is the class we're loading.
		$file_name = '';
		if ( isset( $file_path[ count( $file_path ) - 1 ] ) ) {
			$file_name       = strtolower(
				$file_path[ count( $file_path ) - 1 ]
			);
			$file_name       = str_ireplace( '_', '-', $file_name );
			$file_name_parts = explode( '-', $file_name );
			// Interface support: handle both Interface_Foo or Foo_Interface.
			$interface_index = array_search( 'interface', $file_name_parts, true );
			if ( false !== $interface_index ) {
				// Remove the 'interface' part.
				unset( $file_name_parts[ $interface_index ] );
				$file_name = 'interface-' . implode( '-', $file_name_parts ) . '.php';
			} else {
				$file_name = 'class-' . $file_name . '.php';
			}
		}

		/**
		 * Find the fully qualified path to the class file by iterating through the $file_path array.
		 * We ignore the first index since it's always the top-level package. The last index is always
		 * the file so we append that at the end.
		 */

		if ( 'RFD' === $file_path[0] && 'Core' === $file_path[1] ) {
			$fully_qualified_path = trailingslashit( dirname( dirname( __FILE__ ) ) . '/core' );
		} elseif ( 'RFD' === $file_path[0] && 'Blocks' === $file_path[1] ) {
			$fully_qualified_path = trailingslashit( dirname( dirname( __FILE__ ) ) . '/blocks' );
		} else {
			$fully_qualified_path = trailingslashit( dirname( dirname( __FILE__ ) ) . '/includes' );
		}

		$file_path_count = count( $file_path );
		for ( $i = 2; $i < $file_path_count - 1; $i ++ ) {
			$dir                  = strtolower( $file_path[ $i ] );
			$fully_qualified_path = $fully_qualified_path . trailingslashit( $dir );
		}

		if ( 'RFD' === $file_path[0] && 'Blocks' === $file_path[1] && false === empty( $file_path[2] ) ) {
			$block_dir_name = $file_path[2];
			$block_dir_name = strtolower( $block_dir_name );
			$block_dir_name = preg_replace( '/[^0-9A-Za-z.-]/', '-', $block_dir_name );
			$block_dir_name = preg_replace( '/-{2,}/', '-', $block_dir_name ); // @phpstan-ignore-line $block_dir_name will not be null.

			$fully_qualified_path .= $block_dir_name . DIRECTORY_SEPARATOR;
		}

		$fully_qualified_path .= $file_name;
		if ( stream_resolve_include_path( $fully_qualified_path ) ) {
			include_once $fully_qualified_path;
		}
	}
);
