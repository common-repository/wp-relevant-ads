<?php
/**
 * The class responsible for monitoring and handling Ad's statuses.
 *
 * @package WP Relevant Ads/Includes/Status
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_Status_Monitor {

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->define_hooks();
	}

	/**
	 * Register additional hooks related with Ad statuses.
	 *
	 * @since 1.0.0
	 */
	private function define_hooks() {

		$loader = wp_relevant_ads()->get_loader();

		// Actions.
		$loader->add_action( 'wp_relevant_ads_check_expired', $this, 'maybe_expire_ads', 10 );
		$loader->add_action( 'wp_relevant_ads_ad_expired', $this, 'update_status' );

		// Filters.
		$loader->add_filter( 'posts_clauses', $this, 'expired_ads_sql', 10, 2 );
	}

	/**
	 * Query the DB for expired Ads and set them as expired as necessary.
	 *
	 * @since 1.0.0
	 */
	public function maybe_expire_ads() {

		// Expired.
		$expired_ads = new WP_Query( array(
			'post_type'    => $this->post_type,
			'post_status'  => 'publish',
			'expiring_ads' => true,
			'nopaging'     => true,
		) );

		foreach ( $expired_ads->posts as $ad ) {
			$this->expire( $ad->ID );
		}

	}

	/**
	 * Triggers actions when a specific Ad is expired.
	 *
	 * @uses do_action() Calls 'wp_relevant_ads_ad_expired'
	 *
	 * @since 1.0.0
	 *
	 * @param int $ad_id The Ad ID.
	 */
	protected function expire( $ad_id ) {

		if ( ! $ad_id ) {
			return;
		}

		do_action( 'wp_relevant_ads_ad_expired', $ad_id );
	}

	/**
	 * Updates a specific Ad status to 'Expired'.
	 *
	 * @since 1.0.0
	 *
	 * @param int $ad_id The Ad ID.
	 */
	public function update_status( $ad_id, $status = '' ) {
		if ( ! $status ) {
			$status = $this->status_expired;
		}

		wp_update_post( array(
			'ID'          => $ad_id,
			'post_status' => $status,
		) );
	}

	/**
	 * Retrieves clauses to be used on a WP_Query object to help looking for expired Ads.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $clauses Existing WP_Query clauses.
	 * @param WP_Query $wp_query The WP_Query object.
	 *
	 * @return array The updated list of WP_Query clauses.
	 */
	public function expired_ads_sql( $clauses, $wp_query ) {
		global $wpdb;

		if ( $wp_query->get( 'expiring_ads' ) ) {
			$clauses['join'] .= ' INNER JOIN ' . $wpdb->postmeta . ' AS exp1 ON (' . $wpdb->posts . '.ID = exp1.post_id)';

			if ( $wp_query->get( 'expire_days' ) ) {
				$days = $wp_query->get( 'expire_days' );
			}

			if ( ! empty( $days ) ) {
				$clauses['where'] .= " AND ( exp1.meta_key = '" . $this->field_prefix . 'expire_date' . "' AND FROM_UNIXTIME( exp1.meta_value ) < DATE_ADD('" . current_time( 'mysql' ) . "', INTERVAL $days DAY) AND exp1.meta_value > 0 )";
			} else {
				$clauses['where'] .= " AND ( exp1.meta_key = '" . $this->field_prefix . 'expire_date' . "' AND FROM_UNIXTIME( exp1.meta_value ) < '" . current_time( 'mysql' ) . "' AND exp1.meta_value > 0 )";
			}
		}

		return $clauses;
	}

	/**
	 * Interceptor for retrieving properties from the main plugin object.
	 *
	 * @since	1.0.0
	 *
	 * @param	string $name The property to retrieve.
	 * @return	mixed The property value.
	 */
	public function __get( $name ) {
		return wp_relevant_ads()->$name;
	}

}
