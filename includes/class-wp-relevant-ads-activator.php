<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package WP Relevant Ads/Includes/Activator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_Activator {

	/**
	 * Core object.
	 *
	 * @since 1.0.0
	 *
	 * @var object $properties The plugin core object.
	 */
	static $core;

	/**
	 * Code run exclusively on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public static function activate( $file ) {

		self::$core = new WP_Relevant_Ads_Core( $file );

		self::$core->register_post_types();
		self::$core->register_taxonomies();

		self::load_default_content();

		flush_rewrite_rules();
	}

	/**
	 * Load default content.
	 *
	 * @since 1.0.0
	 */
	public static function load_default_content() {

		$wp_tax_ads = new WP_Query( array(
			'post_type'      => self::$core->post_type,
			'posts_per_page' => 1,
		) );

		if ( $wp_tax_ads->post_count ) {
			return;
		}

		// Add default sales page.
		$content = "This is a dummy pre-sales page. Use it to sell Ads space on your site through a simple contact form.

					Use the provided shortcode below to display a simple contact form or use your own.

					[wp_relevant_ads_contact_form]";

		if ( ! ( $sales_page = get_page_by_path( 'pre-sales-page-wp-relevant-ads') ) ) {

			$sales_page_id = wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Pre-Sales Page (WP Relevant Ads)',
				'post_name'    => 'pre-sales-page-wp-relevant-ads',
				'post_content' => $content,
			) );

		} else {
			$sales_page_id = $sales_page->ID;
		}

		$term = get_terms( 'category', array(
			'hide_empty' => false,
		) );
		$term = reset( $term );

		$ads = array(
			array(
				'title'     => 'Sample - Widget',
				'type'      => 'widget',
				'terms'     => WP_Relevant_Ads_Utils::maybe_insert_term( 'Widgets', self::$core->taxonomy ),
				'image_path'=> plugin_dir_path( dirname( __FILE__ ) ) . 'images/widget.png',
				'width'     => 300,
				'height'    => 300,
				'meta'      => array(
					self::$core->field_prefix . 'trigger_rule'  => 'widget',
					self::$core->field_prefix . 'pre_sale_page' => '',
				),
			),
			array(
				'title'      => 'Sample - Call to Action Widget',
				'type'       => 'widget',
				'terms'      => WP_Relevant_Ads_Utils::maybe_insert_term( 'Widgets', self::$core->taxonomy ),
				'image_path' => plugin_dir_path( dirname( __FILE__ ) ) . 'images/call-action-ad-300x300.png',
				'width'      => 293,
				'height'     => 293,
				'meta'       => array(
					self::$core->field_prefix . 'call_action'   => 1,
					self::$core->field_prefix . 'pre_sale_page' => $sales_page_id,
					self::$core->field_prefix . 'trigger_rule'  => 'widget',
				),
			),
			array(
				'title'     => 'Sample - Banner',
				'type'      => 'banner',
				'terms'     => WP_Relevant_Ads_Utils::maybe_insert_term( 'Banners', self::$core->taxonomy ),
				'image_path'=> plugin_dir_path( dirname( __FILE__ ) ) . 'images/banner.png',
				'width'     => 468,
				'height'    => 60,
				'meta'      => array(
					self::$core->field_prefix . 'terms'         => array( 'category' => array( (string) $term->term_id ) ),
					self::$core->field_prefix . 'hooks'         => array( 'wp_relevant_ads_before_the_content' ),
					self::$core->field_prefix . 'trigger_rule'  => 'shortcode',
					self::$core->field_prefix . 'pre_sale_page' => '',
				),
				'content' => '
					<hr />
					This is a sample shortcode Ad. To see it working paste the shortcode as explained below on one of your posts categorized with <strong>' . $term->name . '</strong>.
					Of course, this text will also show up. Anything you add in the Ad content will be displayed as an Ad.
				',
			),
			array(
				'title'      => 'Sample - Call to Action Banner',
				'type'       => 'hook',
				'terms'      => WP_Relevant_Ads_Utils::maybe_insert_term( 'Banners', self::$core->taxonomy ),
				'image_path' => plugin_dir_path( dirname( __FILE__ ) ) . 'images/call-action-ad-banner.png',
				'width'      => 468,
				'height'     => 60,
				'meta'       => array(
					self::$core->field_prefix . 'call_action'   => 1,
					self::$core->field_prefix . 'pre_sale_page' => $sales_page_id,
					self::$core->field_prefix . 'hooks_custom'  => 'wp_head',
					self::$core->field_prefix . 'trigger_rule'  => 'shortcode',
				),
				'content' => '
					<hr />
					This is a sample \'Call to Action\' shortcode Ad. Since no terms are assigned to this Ad, it will be displayed wherever you use the shortcode until you have a specific Ad your Posts terms.
					Of course, this text will also show up. Anything you add in the Ad content will be displayed as an Ad.
				',
			),
		);

		$wp_upload_dir = wp_upload_dir();

		foreach( $ads as $ad ) {

			$ad = wp_parse_args( $ad, array(
				'status'  => 'publish',
				'meta'    => array(),
			) );

			$image_path = $ad['image_path'];

			$image_data = file_get_contents( $image_path );

			// if we were able to load the image set it up.
			if ( false !== $image_data ) {

				$filename = basename( $image_path );

				if ( wp_mkdir_p( $wp_upload_dir['path'] ) ) {
					$file = $wp_upload_dir['path'] . '/' . $filename;
				} else {
					$file = $wp_upload_dir['basedir'] . '/' . $filename;
				}

				$file_url = $wp_upload_dir['url'] . '/' . $filename;

				file_put_contents( $file, $image_data );

				$wp_filetype = wp_check_filetype( basename( $filename ), null );

				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title'     => sanitize_file_name( $filename ),
					'post_status'    => 'inherit'
				);
				$attach_id = wp_insert_attachment( $attachment, $file );

				require_once ABSPATH . 'wp-admin/includes/image.php';
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
				wp_update_attachment_metadata( $attach_id, $attach_data );

			} else {
				$file_url = '';
			}

			$ad_cat = $ad['terms'];

			// Add default ads.

			$ad_id = wp_insert_post( array(
				'post_type'    => self::$core->post_type,
				'post_status'  => $ad['status'],
				'post_title'   => $ad['title'],
				'post_content' => ( ! empty( $ad['content'] ) ? $ad['content'] : '' ) . ( $file_url ?
					html( 'img', array(
						'src'    => esc_url( $file_url ),
						'width'  => esc_attr( $ad['width'] ),
						'height' => esc_attr( $ad['height'] ),
						'class'  => 'aligncenter',
					) ) :
					html( 'div style="width: ' . esc_attr( $ad['width'] ) . 'px; height: ' . esc_attr( $ad['height'] ) . 'px; border: 1px solid #ccc; text-align: center;"', 'Your Ad Here' ) ),
				'tax_input' => array(
					self::$core->taxonomy => (int) $ad_cat['term_id'],
				),
				'menu_order' => 99,
			) );

			foreach ( $ad['meta'] as $key => $value ) {
				update_post_meta( $ad_id, $key, $value );
			}
		}

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
