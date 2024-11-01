<?php
/**
 * Widget class for Single and grid Ads.
 *
 * Extends scbWidget.
 *
 * @package WP Relevant Ads/Includes/Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_Single_Ad extends scbWidget {

	/**
	 * @since 1.0.0
	 */
	static function init( $class = '', $file = '', $base = '' ) {
		parent::init( get_class() );
	}

	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( 'wp_relevant_ads_ad', 'Relevant Ads', array( 'description' => __( 'Display single or multiple relevant Ads on the sidebar.', 'wp-relevant-ads' ) ) );
	}

	/**
	 * @since 1.0.0
	 */
	function content( $instance ) {
		$cat     = esc_attr( isset( $instance['category'] ) ? $instance['category']: '' );
		$min_ads = isset( $instance['min_ads'] ) ? (int) $instance['min_ads']: '';
		$limit   = isset( $instance['ads'] ) ? (int) $instance['ads']: '';
		$grid    = esc_attr( isset( $instance['grid'] ) ? true: false );

		if ( $cat ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $this->taxonomy,
					'field'    => 'slug',
					'terms'    => $cat,
				),
			);
		}

		$args['meta_query'] = array(
			array(
				'key'   => $this->field_prefix . 'trigger_rule',
				'value' => 'widget',
			),
		);

		if ( $limit > 0 ) {
			$args['posts_per_page'] = $limit;
		}

		$ads = wp_relevant_ads()->get_ads( $args, $min_ads );

		wp_relevant_ads()->output_widget_ads( $ads, $grid );
	}

	/**
	 * @since 1.0.0
	 */
	function form( $instance ) {
		$title    = esc_attr( isset( $instance['title'] ) ? $instance['title']: '' );
		$category = esc_attr( isset( $instance['category'] ) ? $instance['category']: 'widgets' );
		$limit    = isset( $instance['ads'] ) ? (int) $instance['ads']: '';
		$min_ads  = isset( $instance['min_ads'] ) ? (int) $instance['min_ads']: '';

		$args = array(
			'name'  => 'title',
			'desc'  => __( 'Title', 'wp-relevant-ads' ),
			'type'  => 'text',
			'value' => $title,
		);
		echo html( 'p', $this->input( $args ) );

		$args = array(
			'name'     => 'category',
			'desc'     => __( 'Category', 'wp-relevant-ads' ),
			'type'     => 'select',
			'choices'  => $this->get_terms(),
			'selected' => $category,
		);
		echo html( 'p', $this->input( $args ) );

		$args = array(
			'name'  => 'ads',
			'desc'  => __( 'Limit', 'wp-relevant-ads' ),
			'type'  => 'text',
			'extra' => array(
				'size' => 2,
			),
			'value' => $limit,
		);
		echo html( 'p', $this->input( $args ) );
		echo html( 'p', array( 'class' => 'wp_relevant_ads_widget_note' ), __( 'Leave empty to output all existing relevant Ads from the selected category.', 'wp-relevant-ads' ) );

		$args = array(
			'name'  => 'min_ads',
			'desc'  => __( 'Mininum', 'wp-relevant-ads' ),
			'type'  => 'text',
			'extra' => array(
				'size' => 2,
			),
			'value' => $min_ads,
		);

		echo html( 'p', $this->input( $args ) );
		echo html( 'p', array( 'class' => 'wp_relevant_ads_widget_note' ), __( 'Always show this minimum number of relevant Ads. Empty slots will be replaced by existing \'Call to Action\' Ads from the selected category.', 'wp-relevant-ads' ) );

		$args = array(
			'name'     => 'grid',
			'desc'     => __( 'Display as Grid?', 'wp-relevant-ads' ),
			'type'     => 'checkbox',
			'desc_pos' => 'before',
			'checked'  => ! empty( $instance['grid'] ),
		);

		echo html( 'p', $this->input( $args ) );
		echo html( 'p', array( 'class' => 'wp_relevant_ads_widget_note' ), __( 'The grid will only be applicable if you have more then one Ad to display.', 'wp-relevant-ads' ) );
	}

	/**
	 * @since 1.0.0
	 */
	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}


	// __Helpers.
	/**
	 * Retrieves all the terms for the plugin taxonomy for use as choices on the single Ad page.
	 *
	 * @since 1.0.0
	 *
	 * @return Associative array of slug->terms.
	 */
	public function get_terms() {
		$args = array(
			'get' => 'all',
		);
		$terms = get_terms( $this->taxonomy, $args );

		$count = count( $terms );

		$choices = array();
		if ( $count > 0 ) {
			foreach ( $terms as $term ) {
				$choices[ $term->slug ] = $term->name;
			}
		}
		return $choices;
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
