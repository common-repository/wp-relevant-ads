<?php
/**
 * The class responsible for parsing and reading files.
 *
 * @package WP Relevant Ads/Includes/Parser
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_Parser {

	/**
	 * Retrieves trigger data from a XML file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content_dir The directory path to search for files.
	 * @return array A list of hooks and selectors.
	 */
	public static function get_triggers( $content_dir ) {
		$hooks = $selectors = array();

		$directories = scandir( $content_dir );

		$directories = array_diff( $directories, array( '..', '.' ) );
		$directories = apply_filters( 'wp_relevant_ads_content_directories', array_unique( $directories ) );

		$files_cached_fs = get_transient( 'wp-relevant-ads-filesizes' );

		foreach ( $directories as $directory ) {
			$hooks_file = "$content_dir/$directory/hooks.xml";

			if ( file_exists( $hooks_file ) ) {

				$filesize = filesize( $hooks_file );
				$fs_index = basename( dirname( $hooks_file ) ) . '-' . basename( $hooks_file );

				// Skip loading the file if there are not changes - read from cache as needed.
				if ( ! empty( $files_cached_fs[ $fs_index ] ) && $filesize == $files_cached_fs[ $fs_index ] ) {
					// Skip - get cached values.
					$hooks[ $directory ] = WP_Relevant_Ads_Register_Content::get_content( 'hook', $directory );
				}

				if ( empty( $hooks[ $directory ] ) ) {
					$hooks[ $directory ] = array();

					$files_cached_fs[ $fs_index ] = $filesize;
					$hooks[ $directory ] = array_merge( $hooks[ $directory ], self::load_content_xml( $hooks_file, 'hooks' ) );
				}
			}

			$selectors_file = "$content_dir/$directory/selectors.xml";

			if ( file_exists( $selectors_file ) ) {

				$filesize = filesize( $selectors_file );
				$fs_index = basename( dirname( $selectors_file ) ) . '-' . basename( $selectors_file );

				// Skip loading the file if there are not changes - read from cache as needed.
				if ( ! empty( $files_cached_fs[ $fs_index ] ) && $filesize == $files_cached_fs[ $fs_index ] ) {
					// Skip - get cached values.
					$selectors[ $directory ] = WP_Relevant_Ads_Register_Content::get_content( 'selector', $directory );
				}

				if ( empty( $selectors[ $directory ] ) ) {
					$selectors[ $directory ] = array();

					$files_cached_fs[ $fs_index ] = $filesize;
					$selectors[ $directory ] = array_merge( $selectors[ $directory ], self::load_content_xml( $selectors_file, 'selectors' ) );
				}
			}
		}

		if ( ! empty( $files_cached_fs ) ) {
			set_transient( 'wp-relevant-ads-filesizes', $files_cached_fs );
		}

		return array( 'hooks' => $hooks, 'selectors' => $selectors );
	}

	/**
	 * Loads and parses a XML file and retrieves content in key/value pairs format.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file The XML file name.
	 * @param string $type The XML content data type: 'hooks' or 'selectors'.
	 * @return array An associative array with the content name and description.
	 */
	public static function load_content_xml( $file, $type ) {
		$xml = simplexml_load_file( $file );

		foreach ( $xml->$type->children() as $child ) {
			$content[ (string) $child->name ] = (string) $child->description ;
		}

		return $content;
	}

	/**
	 * Reads a given list of files, parses them and retrieves a list of all available hooks.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dir (optional) The files directory path. Defaults to the current template directory.
	 * @param type   $files (optional) The list of files to search for hooks. Defaults to the current theme directory files.
	 * @return array The list of hooks found.
	 */
	public static function get_hooks( $dir = '', $files = '' ) {

		$hooks = array();

		$theme_dir = ! $dir ? get_template_directory() : $dir;
		$theme_files = ! $files ? self::read_dir( $theme_dir ) : (array) $files;

		foreach ( $theme_files as $file ) {
			$contents = file_get_contents( trailingslashit( $theme_dir ) . $file );
			preg_match_all( '/do_action\(([^\)]+)\)/i', $contents, $matches );

			if ( empty( $matches[1] ) ) {
				continue;
			}

			foreach ( $matches[1] as $match ) {
				if ( false !== strpos( $match, '$' ) ) {
					continue;
				}

				$hook = explode( ',', $match );
				$hook = trim( str_replace( array( "'", '"' ), '', $hook[0] ) );
				$hooks[ $hook ] = array(
					'file' => $file,
				);
			 }
		}
		return $hooks;
	}

	/**
	 * Reads a specific directory path to retrieve the files.
	 *
	 * @param string $dir The directory path to read.
	 * @return array The directory list of files.
	 */
	private static function read_dir( $dir ) {

		$files = array();
		if ( $handle = opendir( $dir ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file != '.' && $file != '..' && strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) == 'php' ) {
					$files[] = $file;
				}
			}
			closedir( $handle );
		}
		return $files;
	}
}
