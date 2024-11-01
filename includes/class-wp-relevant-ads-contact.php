<?php
/**
 * A simple contact shortcode.
 *
 * @package WP Relevant Ads/Includes/Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_Contact {

	public function __construct() {
		add_shortcode( 'wp_relevant_ads_contact_form', array( $this, 'shortcode' ) );
	}

	/**
	 * The HTMl contact form.
	 */
	public function html_form_code() {
		echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
		echo '<p>';
		echo __( 'Your Name (required) <br/>', 'wp-relevant-ads' );
		echo '<input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST['cf-name'] ) ? esc_attr( $_POST['cf-name'] ) : '' ) . '" size="40" />';
		echo '</p>';
		echo '<p>';
		echo __( 'Your Email (required) <br/>', 'wp-relevant-ads' );
		echo '<input type="email" name="cf-email" value="' . ( isset( $_POST['cf-email'] ) ? esc_attr( $_POST['cf-email'] ) : '' ) . '" size="40" />';
		echo '</p>';
		echo '<p>';
		echo __( 'Ad Information (required) <br/>', 'wp-relevant-ads' );
		echo '<textarea rows="10" cols="35" name="cf-message">' . ( isset( $_POST['cf-message'] ) ? esc_attr( $_POST['cf-message'] ) : '' ) . '</textarea>';
		echo html( 'small', __( 'Please describe the type of Ad you want to display and where you would like to position it. You can also choose to display your Ads on specific categories.', 'wp-relevant-ads' ) );
		echo '</p>';
		echo '<p><input type="submit" name="cf-submitted" value="Send"></p>';
		echo '</form>';
	}

	/**
	 * Send the email.
	 */
	function deliver_mail( $to, $subject ) {

		// if the submit button is clicked, send the email.
		if ( isset( $_POST['cf-submitted'] ) ) {

			// sanitize form values
			$name    = sanitize_text_field( $_POST['cf-name'] );
			$email   = sanitize_email( $_POST['cf-email'] );
			$message = esc_textarea( $_POST['cf-message'] );

			$headers = "From: $name <$email>" . "\r\n";

			// If email has been process for sending, display a success message
			if ( $result = wp_mail( $to, $subject, $message, $headers ) ) {
				echo html( 'div', __( '<p>Thanks for contacting us, expect a response soon.</p>', 'wp-relevant-ads' ) );
				return true;
			} else {
				echo __( 'An unexpected error occurred. Please try again later.', 'wp-relevant-ads' );
			}
		}
		return false;
	}

	function shortcode( $atts ) {

		$atts = shortcode_atts( array(
			'subject' => __( sprintf( 'New Ad Pre-Sale Request on \'%s\'.', get_bloginfo( 'name' ) ) , 'wp-relevant-ads' ),
			'to'      => get_bloginfo( 'admin_email' ),
		), $atts, 'wp_relevant_ads_contact_form' );

		ob_start();

		extract( $atts );

		$result = $this->deliver_mail( $to, $subject );

		if ( ! $result ) {
			$this->html_form_code();
		}

		return ob_get_clean();
	}

}
