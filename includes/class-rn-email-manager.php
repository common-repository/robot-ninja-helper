<?php
/**
 * Robot Ninja Email Manager class
 *
 * @author 	Prospress
 * @since 	1.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RN_Email_Manager {

	/**
	 * @var array $email_ids - List of email IDs that can be toggled on/off if the email is related to Robot Ninja tests
	 */
	public static $email_ids = array(
		'new_order',
		'failed_order',
		'cancelled_order',
		'cancelled_subscription',
		'customer_processing_order',
		'customer_completed_order',
		'customer_refunded_order',
		'customer_new_account',
		'pip_email_packing_list',
		'pip_email_invoice',
	);

	/**
	 * Initialise Robot Ninja Helper Email Manager class
	 *
	 * @since 1.8.0
	 */
	public static function init() {
		foreach ( self::$email_ids as $email_id ) {
			$email_id_for_constant = strtoupper( $email_id );

			if ( defined( 'RN_SEND_' . $email_id_for_constant . '_EMAIL' ) && ! constant( 'RN_SEND_' . $email_id_for_constant . '_EMAIL' ) ) {
				add_filter( 'woocommerce_email_enabled_' . $email_id, __CLASS__ . '::disable_sending_rn_emails', 10, 2 );
			}
		}
	}

	/**
	 * Make sure we don't send emails for Robot Ninja test orders if the store
	 * has set the RN_SEND_{EMAIL_TYPE}_EMAIL set to false in wp-config.
	 *
	 * @since 1.8.0
	 * @param bool $email_enabled
	 * @param WC_Order|WC_Subscription $object
	 * @return bool
	 */
	public static function disable_sending_rn_emails( $email_enabled, $object ) {
		if ( $email_enabled && ( is_a( $object, 'WC_Order' ) || is_a( $object, 'WC_Subscription' ) || is_a( $object, 'WP_User' ) ) ) {
			$customer_email = ( is_a( $object, 'WP_User' ) ) ? $object->user_email : $object->get_billing_email();

			if ( $customer_email && preg_match( '/store[\+]guest[\-](\d+)[\@]robotninja.com/', $customer_email ) || preg_match( '/store[\+](\d+)[\@]robotninja.com/', $customer_email ) ) {
				$email_enabled = false;
			}
		}

		return $email_enabled;
	}
}
RN_Email_Manager::init();
