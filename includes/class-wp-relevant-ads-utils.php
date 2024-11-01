<?php
/**
 * A simple utility class.
 *
 * @package WP Relevant Ads/Includes/Utils
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_Utils {

	/**
	 * Retrieves date formats to be used with jQuery UI.
	 *
	 * @since 1.0.0
	 *
	 * @param string $default (optional) The default date format.
	 * @param string $format (optional) The format to use: 'js' (javascript) or 'wp' WordPress.
	 * @return string The output format.
	 */
	public static function ui_date_format( $default = 'm/d/Y', $format = 'js' ) {

		$date_format = get_option( 'date_format', $default );
		switch ( $date_format ) {
			case 'd/m/Y':
			case 'j/n/Y':
				$ui_display_format = array(
					'js' => 'dd/mm/yy',
					'wp' => 'd/m/Y',
				);
			break;
			case 'Y/m/d':
			case 'Y/n/j':
				$ui_display_format = array(
					'js' => 'yy/mm/dd',
					'wp' => 'Y/m/d',
				);
			break;
			case 'm/d/Y':
			case 'n/j/Y':
			default:
				$ui_display_format = array(
					'js' => 'mm/dd/yy',
					'wp' => 'm/d/Y',
				);
			break;
		}
		return $ui_display_format[ $format ];
	}

	/**
	 * Retrieves the user IP.
	 *
	 * @since 1.0.0
	 *
	 * @return string The user IP.
	 */
	public static function get_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP']; // ip from share internet
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR']; // ip from proxy
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * Inserts a new term into a given taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param string $term The term to insert.
	 * @param string $taxonomy The taxonomy to add the terms.
	 * @return array|WP_Error An array containing the term_id and term_taxonomy_id, WP_Error otherwise.
	 */
	public static function maybe_insert_term( $term, $taxonomy ) {
		$term_arr = term_exists( $term, $taxonomy );
		if ( ! $term_arr ) {
			$term_arr = wp_insert_term( $term, $taxonomy );
		}
		return $term_arr;
	}

	/**
	 * Reduces a multi-dimensional array to an associative array.
	 *
	 * @param array $array The array being reduced.
	 * @return array The reduced array.
	 */
	public static function reduce_multi_array( $array ) {

		if ( ! $array ) {
			return array();
		}

		$reduced_array = array();

		foreach ( $array as $key => $values ) {
			$reduced_array = array_merge( $reduced_array, $values );
		}
		return $reduced_array;
	}

}
