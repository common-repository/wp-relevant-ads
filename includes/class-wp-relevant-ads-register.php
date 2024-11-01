<?php
/**
 * Content registration.
 *
 * @package WP Relevant Ads/Includes/Register
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'class-wp-relevant-ads-parser.php';

/**
 * The class responsible for registering all dynamic content used to setup the Ads.
 */
class WP_Relevant_Ads_Register_Content {

	/**
	 * The registered content that will be used during a page session life.
	 *
	 * @since 1.0.0
	 *
	 * @var array $content The registered content types.
	 */
	private static $content = array();

	/**
	 * The registered themes that will be used during a page session life.
	 *
	 * @since 1.0.0
	 *
	 * @var array $themes The registered themes for use with the plugin.
	 */
	private static $themes = array();

	/**
	 * Registers a valid theme.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id The theme unique ID.
	 */
	public static function register_theme( $id ) {
		self::$themes[ $id ] = $id;
	}

	/**
	 * Init base hooks.
	 */
	public static function register_base_hooks( $hooks = array() ) {
		$hooks = apply_filters( 'wp_relevant_ads_wp_hooks', $hooks );
		self::register( 'base', 'hook', $hooks );
	}

	/**
	 * Registers triggers in XML files by storing them on the options table.
	 */
	public static function register_triggers( $path ) {
		$triggers = WP_Relevant_Ads_Parser::get_triggers( $path . '/triggers' );

		if ( ! empty( $triggers['selectors'] ) ) {
			foreach ( $triggers['selectors'] as $id => $selectors ) {
				self::register( $id, 'selector', $selectors );
			}
		}

		if ( ! empty( $triggers['hooks'] ) ) {
			foreach ( $triggers['hooks'] as $id => $hooks ) {
				self::register( $id, 'hook', $hooks );
			}
		}

	}

	/**
	 * Retrieves one or all registered themes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id The theme ID to retrieve.
	 * @return boolean True if the theme is registered, False otherwise.
	 */
	public static function get_registered_themes( $id = '' ) {
		if ( empty( $id ) ) {
			return array_keys( self::$themes );
		} elseif ( ! empty( self::$themes[ $id ] ) ) {
			return self::$themes[ $id ];
		} else {
			return false;
		}
	}

	/**
	 * Registers dynamic content to be used on the Ads setup page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id The unique ID of the content being registered..
	 * @param string $type The content type to be registered. Must be a valid key.
	 * @param mixed  $values The value(s) to be registered for this content type.
	 * @param bool   $cache (optional) Whether the values should be cached or not.
	 */
	public static function register( $id, $type, $values, $cache = true ) {

		if ( empty( self::$content[ $type ][ $id ] ) ) {
			self::$content[ $type ][ $id ] = array();
		}

		if ( empty( $values ) ) {
			return;
		}

		self::$content[ $type ][ $id ] = array_merge( self::$content[ $type ][ $id ], (array) $values );

		if ( $cache ) {
			set_transient( 'wp-relevant-ads-content', self::$content );
			set_transient( 'wp-relevant-ads-content-' . $type, self::$content[ $type ] );
		}

	}

	/**
	 * Retrieves verified registered content.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $type (optional) The content type to retrieve values from. Retrieves all content by default.
	 * @param boolean $cached (optional) Should the values be retrieved from the cache or not. Retrieves cached values by default.
	 * @return mixed The registered values or false if no values are found.
	 */
	public static function get_content( $type = '', $cached = true ) {

		if ( $cached ) {
			$content = get_transient( 'wp-relevant-ads-content' . ( $type ? '-' . $type : '' ) );
		} else {

			if ( ! $type ) {
				$content = self::$content;
			} elseif ( ! empty( self::$content[ $type ] ) ) {
				$content = self::$content[ $type ];
			} else {
				return false;
			}
}

		if ( ! $content ) {
			return false;
		}
		return $content;
	}

}
