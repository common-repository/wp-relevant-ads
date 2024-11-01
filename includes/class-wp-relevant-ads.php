<?php
/**
 * The main class responsible for all the "relevant" Ads functionality.
 *
 * @package WP Relevant Ads/Includes/Ads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads extends WP_Relevant_Ads_Core {


	// Store the instance locally to avoid private static replication.
	protected static $instance = null;

	public static function instance( $file ) {

		// Check for previous instance.
		if ( ! self::$instance ) {
			self::$instance = new WP_Relevant_Ads( $file );
			self::$instance->init( $file );
		}
		// Always return the instance.
		return self::$instance;
	}

	/**
	 * Extends the core functionality.
	 *
	 * @since 1.0.0
	 */
	public function init( $file ) {

		parent::init( $file );

		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		$this->define_hooks();
		$this->define_shortcodes();
	}

	/**
	 * Register additional hooks and also user specified hooks set up on the single Ad page.
	 *
	 * @uses do_action() Calls 'wp_ajax_nopriv_wp_relevant_ads_count_clicks'
	 * @uses do_action() Calls 'wp_ajax_wp_relevant_ads_count_clicks'
	 *
	 * @since 1.0.0
	 */
	private function define_hooks() {

		if ( isset( $_POST['wp_relevant_ads_count_clicks'] ) ) {
			do_action( 'wp_ajax_nopriv_wp_relevant_ads_count_clicks' );
			do_action( 'wp_ajax_wp_relevant_ads_count_clicks' );
		}

		$this->loader->add_filter( 'wp_relevant_ads_is_relevant', $this, 'filter_by_taxonomy', 10, 2 );
		$this->loader->add_action( 'wp_ajax_nopriv_wp_relevant_ads_count_clicks', $this, 'count_clicks' );
		$this->loader->add_action( 'wp_ajax_wp_relevant_ads_count_clicks', $this, 'count_clicks' );

		// loop through all the Hooked Ads.
		$query_args = array(
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
			'meta_query'  => array(
				array(
					'key'   => $this->field_prefix . 'trigger_rule',
					'value' => 'hook',
				),
			),
			'orderby'       => 'menu_order',
			'order'	        => 'ASC',
			'nopaging'      => true,
			'fields'        => 'ids',
			'no_found_rows' => true,
		);

		$query = new WP_Query( $query_args );
		if ( empty( $query->posts ) ) {
			return;
		}

		foreach ( $query->posts as $ad_id ) {

			$ad_hooks = get_post_meta( $ad_id, $this->field_prefix . 'hooks', true );
			$custom   = get_post_meta( $ad_id, $this->field_prefix . 'hooks_custom', true );

			if ( ! empty( $custom ) ) {
				$custom_hooks = array_map( 'trim', explode( ',', $custom ) );
				$ad_hooks     = array_merge( (array) $ad_hooks, $custom_hooks );
			}

			// attach the actions to the Hooks.
			if ( ! empty( $ad_hooks ) ) {

				foreach ( $ad_hooks as $ad_hook ) {
					$this->attach_to_hook( $ad_hook );
				}
			}
		}

	}

	/**
	 * Register shortcodes.
	 *
	 * @since 1.0.0
	 */
	private function define_shortcodes() {
		add_filter( 'widget_text', 'do_shortcode' );
		add_shortcode( 'wp_relevant_ads', array( $this, 'sc_show_ad' ) );
	}


	// Callbacks
	/**
	 * Trigger actions for all Ads configured to be triggered by hooks.
	 *§
	 * @since 1.0.0
	 */
	public function do_action( $output = '' ) {
		$current_action = current_action();

		$args['meta_query'] = array(
			array(
				'key'   => $this->field_prefix . 'trigger_rule',
				'value' => 'hook',
			),
		);

		$ads = $this->get_ads( $args );

		$output = '';

		foreach ( (array) $ads as $ad ) {
			$hooks        = get_post_meta( $ad->ID, $this->field_prefix . 'hooks', true );
			$hooks_custom = get_post_meta( $ad->ID, $this->field_prefix . 'hooks_custom', true );

			if ( ! empty( $hooks_custom ) ) {
				$custom_hooks = array_map( 'trim', explode( ',', $hooks_custom ) );
				$hooks        = array_merge( (array) $hooks, $custom_hooks );
			}

			if ( in_array( $current_action, $hooks ) ) {
				$output .= $this->generate_ad( $ad );
			}
}

		echo apply_filters( 'wp_relevant_ads_do_action', $this->ad_output( $output ), $ads );
	}

	/**
	 * Retrieve Ads triggered by Shortcodes.
	 *
	 * usage [wp_relevant_ads id="" category="" call_action="" class="" style=""]
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Array with the Ad shortcode attributes
	 * @return string The Ad HTML output.
	 */
	public function sc_show_ad( $atts ) {

		$args = array();

		extract( shortcode_atts(
			// Defaults.
			array(
				'id'          => '0',
				'category'    => '',
				'call_action' => '',
				'class'       => '',
				'css'         => '',
		), $atts ) );

		$id              = "{$id}";
		$category        = "{$category}";
		$min_call_action = ( 'yes' === $call_action ? 1 : 0 );

		$attrs['class'] = "{$class}";
		$attrs['css']   = "{$css}";

		if ( $id > 0 ) {

			$args = array(
				'p' => $id,
			);

		}

		if ( $category ) {

			$args['tax_query'] = array(
				array(
					'taxonomy' => $this->taxonomy,
					'field'    => 'slug',
					'terms'    => $category,
				),
			);

		}

		$args['meta_query'] = array(
			array(
				'key'   => $this->field_prefix . 'trigger_rule',
				'value' => 'shortcode',
			),
		);

		$ads = $this->get_ads( $args, $min_call_action );

		$output = '';

		foreach ( $ads as $ad ) {
			$output .= $this->generate_ad( $ad, $attrs );
		}

		return apply_filters( 'wp_relevant_ads_shortcode', $this->ad_output( $output ), $ads );
	}

	/**
	 * Retrieve Ads triggered by jQuery selectors.
	 *
	 * @since 1.0.0
	 *
	 * @return string The Ad HTML output.
	 */
	public function get_dom_ads() {

		$args['meta_query'] = array(
			array(
				'key'   => $this->field_prefix . 'trigger_rule',
				'value' => 'dom',
			),
		);

		$ads = (array) $this->get_ads( $args );

		// reverses the array so that jQuery displays the Ads in the correct order.
		$ads = array_reverse( $ads );

		$output = array();

		foreach ( $ads as $ad ) {
			$dom = get_post_meta( $ad->ID, $this->field_prefix . 'trigger_dom', true );
			$dom = html_entity_decode( $dom );

			$output[ $dom ][] = array(
				'ad'       => $this->generate_ad( $ad ),
				'position' => get_post_meta( $ad->ID, $this->field_prefix . 'trigger_dom_position', true ),
			);
		}

		// get the DOM for this Ad, if any
		return apply_filters( 'wp_relevant_ads_dom', $output, $ads );
	}

	/**
	 * Updates the click count for an Ad.
	 *
	 * @since 1.0.0
	 *
	 * @return int The total Ads clicks.
	 */
	public function count_clicks() {
		global $wp_relevant_ads_options;

		check_ajax_referer( $this->nonce );

		// generate the response
		if ( empty( $_POST['wp_taxad_id'] ) ) {
			wp_send_json_error();
		}

		$wp_taxad_id = (int) $_POST['wp_taxad_id'];

		if ( ! $wp_relevant_ads_options->count_clicks ) {
			wp_send_json_error();
		}

		$current_clicks = (int) get_post_meta( $wp_taxad_id, $this->field_prefix . 'clicks', true );
		$current_clicks++;

		update_post_meta( $wp_taxad_id, $this->field_prefix . 'clicks', $current_clicks );

		wp_send_json_success( array( 'clicks' => $current_clicks ) );
	}


	// __Helper Methods.
	/**
	 * Attaches actions to user specified hooks.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook The hook to attach the action to.
	 */
	private function attach_to_hook( $hook ) {
		if ( ! $hook ) {
			return;
		}
		$this->loader->add_action( $hook, $this, 'do_action' );
	}

	/**
	 * Retrives the Ads list.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args optional Array with the arguments to use in the query.
	 * @param int   $min_ca_ads optional The minimum call to action Ads to add to the returned Ad list.
	 * @return array Array list of Ads.
	 */
	public function get_ads( $query_args = array(), $min_ca_ads = 1 ) {

		$min_ca_ads = (int) $min_ca_ads;

		$ads = $ca_ads = array();

		$ad_tax = ( isset( $query_args['tax_query'][0]['terms'] ) ? array( $query_args['tax_query'][0]['terms'] ) : '' );

		$defaults = array(
			'post_type'      => $this->post_type,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		);
		$query_args = apply_filters( 'wp_relevant_ads_get_ads', wp_parse_args( $query_args, $defaults ) );

		$limit = $query_args['posts_per_page'];
		$query_args['posts_per_page'] = -1;

		$query_args_string = wp_json_encode( $query_args );
		$query_args_string = md5( $query_args_string );

		$transient_key = '_wp_relevant_ads_query_' . $query_args_string;

		if ( false === ( $posts = get_transient( $transient_key ) ) ) {
			$query = new WP_Query( $query_args );
			$posts = $query->posts;

			$transient_list = get_transient( '_wp_relevant_ads_transient_list' );
			$transient_list[ $transient_key ] = $transient_key;


			set_transient( $transient_key, $posts, DAY_IN_SECONDS );
			set_transient( '_wp_relevant_ads_transient_list', $transient_list );
		}

		if ( empty( $posts ) ) {
			return $ads;
		}

		$i = 0;

		/**
		 * Iterate through the Ads and compare terms against the current post.
		 */
		foreach ( $posts as $ad ) {

			if ( ! ( $is_relevant_ad = apply_filters( 'wp_relevant_ads_is_relevant', true, $ad ) ) ) {
				continue;
			}

			$ca_ad    = get_post_meta( $ad->ID, $this->field_prefix . 'call_action', true );
			$ad_terms = get_post_meta( $ad->ID, $this->field_prefix . 'terms', true );

			$order = 99;

			if ( $ad_terms ) {
				$order = 0;
			}

			if ( $is_relevant_ad && ! in_array( $ad, $ads ) ) {
				// Skip call to action Ads so they appear on the end of the Ads list.
				if ( $ca_ad ) {
					$ca_ads[ $i + $order ] = $ad;
				} else {
					$ads[ $i + $order ] = $ad;
				}
			}
			$i++;
		}

		ksort( $ads );
		ksort( $ca_ads );

		// Include call to action ads if requested or if there are no ads to display and there are call to action ads to display.
		if ( ( $min_ca_ads > 0 || empty( $ads ) ) && ! empty( $ca_ads ) ) {

			$total_ca_ads = $min_ca_ads - count( $ads );
			$min_ca_ads = ( $total_ca_ads <= 0 ? 1 : $total_ca_ads );

			if ( $min_ca_ads ) {
				$ads_ca_slots = $this->get_call_to_action( reset( $ca_ads ), $min_ca_ads );

				if ( ! empty( $ads_ca_slots ) ) {
					$ads = array_merge( $ads, $ads_ca_slots );
				}
			}
		}

		// Consider the limits requested by the user.
		if ( $limit > 0 ) {
			$ads = array_slice( $ads, 0, $limit );
		}

		return $ads;
	}

	/**
	 * Fill empty Ad slots with Call to Action Ads.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $ca_ad      A 'Call to Action Ad' post object used to fill empty slots.
	 * @param int     $min_ca_ads The minimum 'Call to Action' Ads to add to the returned Ad list.
	 * @return array              List of 'Call to Action' Ads.
	 */
	public function get_call_to_action( $ca_ad, $min_ca_ads = 1 ) {

		$total = 0;
		$ca_ads = array();
		if ( ! empty( $ca_ad ) ) {
			while ( $total < $min_ca_ads ) {
				$ca_ads[] = $ca_ad;
				$total++;
			}
		}
		return $ca_ads;
	}

	/**
	 * Outputs a specific Ad.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $ad The Ad post object.
	 * @param array   $attrs optional Array with the Ad attributes.
	 * @return string Outputs an HTML string for a specific Ad.
	 */
	public function generate_ad( $ad, $attrs = array() ) {

		if ( ! empty( $attrs['class'] ) ) {
			$class = $attrs['class'];
		} else {
			$class = get_post_meta( $ad->ID, $this->field_prefix . 'class', true );
		}

		if ( ! empty( $attrs['display'] ) ) {
			$display = $attrs['display'];
		} else {
			$display = get_post_meta( $ad->ID, $this->field_prefix . 'display', true );
		}

		if ( ! empty( $attrs['css'] ) ) {
			$style = $attrs['css'];
		} else {
			$style = get_post_meta( $ad->ID, $this->field_prefix . 'css', true );
		}

		$categories = wp_get_object_terms( $ad->ID, $this->taxonomy, array( 'fields' => 'slugs' ) );
		array_unshift( $categories, '' );

		$args = array(
			'id' 	=> esc_attr( $this->field_prefix . $ad->ID ),
			'class' => esc_attr( "wp_relevant_ads_ad $display $class" . implode( 'wp_rel_cat_', $categories ) ),
		);

		if ( $style ) {
			$args['style'] = esc_attr( $style );
		}

		// get the unfiltered content
		$content = apply_filters( 'the_content', $ad->post_content, 'wp_relevant_ads' );
		$ad_content = str_replace( ']]>', ']]&gt;', $content );

		$call_action = get_post_meta( $ad->ID, $this->field_prefix . 'call_action', true );
		if ( $call_action ) {
			$pre_sales_page = get_post_meta( $ad->ID, $this->field_prefix . 'pre_sale_page', true );
		}

		if ( ! empty( $pre_sales_page ) ) {
			// remove any links to be able to wrap the HTML with a new anchor tag
			$content = preg_replace( '/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $ad_content );
			$ad_content = html( 'a', array( 'href' => get_page_link( $pre_sales_page ) ), $content );
		}

		$output = html( 'div', $args, $ad_content );

		return $output;
	}

	/**
	 * Retrieves the terms meta for a given Ad.
	 *
	 * @since 1.0.0
	 *
	 * @param int $ad_id the Ad ID.
	 * @return array list of the Ad term ID's.
	 */
	public function get_ad_terms_meta( $ad_id ) {
		$ad_terms = get_post_meta( $ad_id, $this->field_prefix . 'terms', true );
		if ( ! $ad_terms ) {
			return array();
		}
		return array_keys( $ad_terms );
	}

	/**
	 * Formats and retrieves a specific Ad.
	 *
	 * @since 1.0.0
	 *
	 * @param string $ad The Ad HTML.
	 * @return string The formatted Ad HTML.
	 */
	private function ad_output( $ad ) {
		if ( ! $ad ) {
			return $ad;
		}
		$clear = html( 'div', array( 'class' => 'wp_relevant_ads_clear' ) );

		$ad = $clear . $ad . $clear;

		return $ad;
	}

	/**
	 * Outputs Ads configured to be displayed in widgets.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $ads A list if Ads represented as WP_Post objects.
	 * @param boolean $ad125 (optional) Should the widget output grid ads or not. Default is True.
	 */
	public function output_widget_ads( $ads, $ad125 = true ) {

		$i = 0;
		$li = '';

		foreach ( $ads as $ad ) {
			$ad_class    = get_post_meta( $ad->ID, $this->field_prefix . 'class', true );
			$call_action = get_post_meta( $ad->ID, $this->field_prefix . 'call_action', true );
			if ( $call_action ) {
				$pre_sales_page = get_post_meta( $ad->ID, $this->field_prefix . 'pre_sale_page', true );
			}

			$args = array(
				'id'         => $this->field_prefix . $ad->ID,
				'class'      => esc_attr( 'wp_relevant_ads_ad wp_relevant_ads_widget ' . ( $ad125 ? ' grid ': ' center ' ) . $ad_class . ( $ad125 ? ( $i & 1 ? 'right': 'left' ): '' ) ),
				'data-ad-id' => esc_attr( $ad->ID ),
			);

			if ( ! empty( $pre_sales_page ) ) {
				// Remove any links to be able to wrap the HTML with a new anchor tag.
				$content    = preg_replace( '/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $ad->post_content );
				$ad_content = html( 'a', array( 'href' => esc_url( get_page_link( $pre_sales_page ) ) ), $content );
			} else {
				$ad_content = $ad->post_content;
			}

			$li .= html( 'div', $args, $ad_content );

			$i++;
		} //end foreach

		if ( $li ) {
			echo html( 'div', array( 'class' => 'wp_relevant_ads' ), $li );
		} else {
			echo html( 'div', array( 'class' => 'wp_relevant_ads no_content' ), $li );
		}
	}

	/**
	 * Check if current post matches any required terms.
	 */
	public function filter_by_taxonomy( $relevant, $ad ) {
		global $post;

		$post_types = WP_Relevant_Ads_Register_Content::get_content( 'post_type', false );

		if ( ! in_array( $post->post_type, array_keys( $post_types ), true ) ) {
			return false;
		}

		$ad_terms = get_post_meta( $ad->ID, $this->field_prefix . 'terms', true );

		if ( ! $ad_terms ) {
			return true;
		}

		$valid_taxonomies = array();

		foreach ( $post_types as $post_type ) {
			$valid_taxonomies = array_merge( $valid_taxonomies, get_object_taxonomies( $post_type ) );
		}

		$taxonomies = $current_terms = array();

		if ( ! empty( $post ) && ( in_the_loop() || is_singular() ) ) {
			$taxonomies = array_keys( get_the_taxonomies( $post->ID ) );
		} elseif ( is_tax() ) {
			$taxonomies    = get_queried_object();
			$current_terms = (array) $taxonomies->term_id;
			$taxonomies    = $taxonomies->taxonomy;
		}

		$taxonomies = array_intersect( (array) $taxonomies, $valid_taxonomies );

		foreach ( $taxonomies as $taxonomy ) {
			$current_terms[ $taxonomy ] = wp_list_pluck( get_the_terms( $post, $taxonomy ), 'term_id' );
		}

		$common_terms = array();

		foreach ( (array) $taxonomies as $taxonomy ) {

			if ( isset( $ad_terms[ $taxonomy ] ) ) {
				$matched = array_intersect( $current_terms[ $taxonomy ], $ad_terms[ $taxonomy ] );
				if ( ! empty( $matched ) ) {
					$common_terms[] = $matched;
				}
			}
		}
		return ( ! empty( $common_terms ) );
	}

}

/**
 * Wrapper for retrieving/creating an instance of the plugin main class.
 *
 * @return WP_Relevant_Ads An instance of the plugin main class.
 */
function wp_relevant_ads( $file = '' ) {
	return WP_Relevant_Ads::instance( $file );
}
