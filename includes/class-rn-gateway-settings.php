<?php
/**
 * Robot Ninja Helper Gateways class
 *
 * @author 	Prospress
 * @since 	1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RN_Gateways_Settings {

	/**
	 * @var array $gateway_setting_ids - array of gateway IDs that the RN Helper plugin will return an array of settings for
	 */
	public static $gateway_setting_ids = array(
		'stripe',
		'authorize_net_cim_credit_card',
		'braintree_credit_card',
		'braintree_paypal',
		'braintree',
		'moneris',
		'intuit_payments_credit_card',
		'square',
		'first_data_payeezy_gateway_credit_card',
		'first_data_payeezy_credit_card',
		'affirm',
		'paypal_advanced',
		'ppec_paypal',
		'paypal_pro_payflow',
		'paypal-pro-hosted',
		'amazon_payments_advanced',
		'eway',
		'payfast',
	);

	/**
	 * Returns an array of all the settings needed for our gateway support classes in Robot Ninja.
	 *
	 * @since 1.3.0
	 * @return array
	 */
	public static function get_enabled_gateway_settings() {
		$gateway_settings = array();
		$payment_gateways = WC()->payment_gateways->payment_gateways();

		foreach ( self::$gateway_setting_ids as $gateway_id ) {
			// check if the store has any gateways that we provide support for
			if ( ! empty( $payment_gateways[ $gateway_id ] ) ) {
				$gateway = $payment_gateways[ $gateway_id ];

				if ( ! empty( $gateway->settings['enabled'] ) && 'yes' == $gateway->settings['enabled'] ) {
					$gateway_settings_function = 'get_' . str_replace( '-', '_', $gateway_id ) . '_settings';

					if ( method_exists( __CLASS__, $gateway_settings_function ) ) {
						$gateway_settings[ $gateway_id ] = call_user_func( array( __CLASS__, $gateway_settings_function ), $gateway );
					} else {
						$gateway_settings[ $gateway_id ] = self::get_standard_gateway_settings( $gateway );
					}

					$gateway_settings[ $gateway_id ]['is_available'] = $gateway->is_available();
				}
			}
		}

		return $gateway_settings;
	}

	/**
	* Return standard gateway settings to be used by Robot Ninja.
	* This standard is based off how Skyverge save the settings for all their gateways
	*
	* @since 1.3.0
	* @param WC_Gateway $gateway
	* @param array
	*/
	public static function get_standard_gateway_settings( $gateway ) {
		return array(
			'testmode'         => ( ! empty( $gateway->settings['environment'] ) && 'test' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type' => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
		);
	}

	/**
	 * Return Stripe settings for Robot Ninja
	 *
	 * @since 1.3.0
	 * @param WC_Gateway $gateway
	 * @return array
	 */
	public static function get_stripe_settings( $gateway ) {
		return array(
			'testmode'           => ( ! empty( $gateway->testmode ) ) ? $gateway->testmode : false,
			'payment_form_type'  => ( ! empty( $gateway->stripe_checkout ) && $gateway->stripe_checkout ) ? 'iframe' : 'checkout-form',
			'inline_cc_elements' => ( ! empty( $gateway->inline_cc_form ) ) ? $gateway->inline_cc_form : false,
			'transaction_type'   => ( ! empty( $gateway->capture ) && true == $gateway->capture ) ? 'charge' : 'authorize',
		);
	}

	/**
	 * Return Braintree Credit Card settings for Robot Ninja
	 *
	 * @since 1.3.0
	 * @param WC_Gateway $gateway
	 * @return array
	 */
	public static function get_braintree_credit_card_settings( $gateway ) {
		return array(
			'testmode'                  => ( ! empty( $gateway->settings['environment'] ) && 'sandbox' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type'          => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
			'card_verification_enabled' => ( ! empty( $gateway->settings['require_csc'] ) && 'yes' == $gateway->settings['require_csc'] ) ? true : false,
		);
	}

	/**
	 * Return Braintree PayPal settings for Robot Ninja
	 *
	 * @since 1.12.0
	 * @param WC_Gateway $gateway
	 * @return array
	 */
	public static function get_braintree_paypal_settings( $gateway ) {
		return array(
			'testmode'         => ( ! empty( $gateway->settings['environment'] ) && 'sandbox' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type' => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
		);
	}

	/**
	 * Return Moneris settings in System Status report for Robot Ninja
	 *
	 * @since 1.3.0
	 * @param WC_Gateway $gateway
	 * @return array
	 */
	public static function get_moneris_settings( $gateway ) {
		return array(
			'testmode'                     => ( ! empty( $gateway->settings['environment'] ) && 'test' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type'             => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
			'hosted_tokenization'          => ( ! empty( $gateway->settings['hosted_tokenization'] ) && 'yes' == $gateway->settings['hosted_tokenization'] ) ? true : false,
			'card_verification_enabled'    => ( ! empty( $gateway->settings['enable_csc'] ) && 'yes' == $gateway->settings['enable_csc'] ) ? true : false,
		);
	}

	/**
	 * Return Intuit Payments Credit Card settings for Robot Ninja
	 *
	 * @since 1.4.0
	 * @param WC_Gateway $gateway
	 * @return array
	 */
	public static function get_intuit_payments_credit_card_settings( $gateway ) {
		return array(
			'testmode'                  => ( ! empty( $gateway->settings['environment'] ) && 'sandbox' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type'          => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
			'tokenization'              => ( ! empty( $gateway->settings['tokenization'] ) && 'yes' == $gateway->settings['tokenization'] ) ? true : false,
			'card_verification_enabled' => ( ! empty( $gateway->settings['enable_csc'] ) && 'yes' == $gateway->settings['enable_csc'] ) ? true : false,
		);
	}

	/**
	 * Return the braintree gateway settings to be used by Robot Ninja.
	 *
	 * @since 1.9.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_braintree_settings( $gateway ) {
		return array(
			'testmode'          => ( ! empty( $gateway->settings['sandbox'] ) && 'yes' == $gateway->settings['sandbox'] ) ? true : false,
			'braintree_drop_in' => ( ! empty( $gateway->settings['enable_braintree_drop_in'] ) && 'yes' == $gateway->settings['enable_braintree_drop_in'] ) ? true : false,
			'transaction_type'  => ( ! empty( $gateway->settings['payment_action'] ) && 'Sale' == $gateway->settings['payment_action'] ) ? 'charge' : 'authorize',
		);
	}

	/**
	 * Return the square gateway settings to be used by Robot Ninja.
	 *
	 * @since 1.10.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_square_settings( $gateway ) {
		return array(
			'testmode'         => false, // doesn't have a testmode/sandbox mode
			'transaction_type' => ( ! empty( $gateway->settings['capture'] ) && 'no' == $gateway->settings['capture'] ) ? 'charge' : 'authorize',
			'create_customer'  => ( ! empty( $gateway->settings['create_customer'] ) && 'no' == $gateway->settings['create_customer'] ) ? false : true,
		);
	}

	/**
	 * Return the First Data Payeezy gateway credit card settings to be used by Robot Ninja.
	 *
	 * @since 1.11.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_first_data_payeezy_gateway_credit_card_settings( $gateway ) {
		return array(
			'testmode'                  => ( ! empty( $gateway->settings['environment'] ) && 'demo' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type'          => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
			'card_verification_enabled' => ( ! empty( $gateway->settings['enable_csc'] ) && 'no' == $gateway->settings['enable_csc'] ) ? false : true,
		);
	}

	/**
	 * Return the First data Payeezy credit card settings to be used by Robot Ninja.
	 *
	 * @since 1.11.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_first_data_payeezy_credit_card_settings( $gateway ) {
		return array(
			'testmode'         => ( ! empty( $gateway->settings['environment'] ) && 'sandbox' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type' => ( ! empty( $gateway->settings['transaction_type'] ) && 'charge' == $gateway->settings['transaction_type'] ) ? 'charge' : 'authorize',
		);
	}

	/**
	 * Return the Affirm Monthly Payments settings to be used by Robot Ninja.
	 *
	 * @since 1.12.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_affirm_settings( $gateway ) {
		return array(
			'testmode'         => ( ! empty( $gateway->settings['testmode'] ) && 'yes' == $gateway->settings['testmode'] ) ? true : false,
			'transaction_type' => ( ! empty( $gateway->settings['transaction_mode'] ) && 'capture' == $gateway->settings['transaction_mode'] ) ? 'charge' : 'authorize',
			'checkout_mode'    => ( ! empty( $gateway->settings['checkout_mode'] ) && 'modal' == $gateway->settings['checkout_mode'] ) ? 'modal' : 'redirect',
		);
	}


	/**
	 * Return the PayPal Advanced settings to be used by Robot Ninja.
	 *
	 * @since 1.12.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_paypal_advanced_settings( $gateway ) {
		return array(
			'testmode'         => ( ! empty( $gateway->settings['testmode'] ) && 'yes' == $gateway->settings['testmode'] ) ? true : false,
			'transaction_type' => ( ! empty( $gateway->settings['transtype'] ) && 'S' == $gateway->settings['transtype'] ) ? 'charge' : 'authorize', // S is for Sale == Capture payment immediately
			'checkout_layout'  => ( ! empty( $gateway->settings['layout'] ) ) ? $gateway->settings['layout'] : '',
		);
	}

	/**
	 * Return the PayPal Express Checkout settings to be used by Robot Ninja.
	 *
	 * @since 1.12.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_ppec_paypal_settings( $gateway ) {
		return array(
			'testmode'                      => ( ! empty( $gateway->settings['environment'] ) && 'sandbox' == $gateway->settings['environment'] ) ? true : false,
			'transaction_type'              => ( ! empty( $gateway->settings['paymentaction'] ) && 'sale' == $gateway->settings['paymentaction'] ) ? 'charge' : 'authorize',
			'landing_page'                  => ( ! empty( $gateway->settings['landing_page'] ) ) ? $gateway->settings['landing_page'] : 'Login',
			'smart_payment_buttons_enabled' => ( ! empty( $gateway->settings['use_spb'] ) && 'yes' == $gateway->settings['use_spb'] ) ? true : false,
		);
	}

	/**
	 * Return the PayPal Pro Payflow settings to be used by Robot Ninja.
	 *
	 * @since 1.12.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_paypal_pro_payflow_settings( $gateway ) {
		return array(
			'testmode'             => ( ! empty( $gateway->settings['testmode'] ) && 'yes' == $gateway->settings['testmode'] ) ? true : false,
			'transaction_type'     => ( ! empty( $gateway->settings['paypal_pro_payflow_paymentaction'] ) && 'S' == $gateway->settings['paypal_pro_payflow_paymentaction'] ) ? 'charge' : 'authorize', // S is for Sale == Capture payment immediately
			'transparent_redirect' => ( ! empty( $gateway->settings['transparent_redirect'] ) && 'yes' == $gateway->settings['transparent_redirect'] ) ? true : false,
		);
	}

	/**
	 * Return the PayPal Pro Hosted settings to be used by Robot Ninja.
	 *
	 * @since 1.12.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_paypal_pro_hosted_settings( $gateway ) {
		return array(
			'testmode'         => ( ! empty( $gateway->settings['sandbox'] ) && 'yes' == $gateway->settings['sandbox'] ) ? true : false,
			'transaction_type' => ( ! empty( $gateway->settings['payment_action'] ) && 'sale' == $gateway->settings['payment_action'] ) ? 'charge' : 'authorize', // S is for Sale == Capture payment immediately
		);
	}

	/**
	 * Return the PayPal Pro Hosted settings to be used by Robot Ninja.
	 *
	 * @since 1.13.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_amazon_payments_advanced_settings( $gateway ) {
		return array(
			'testmode'         => ( ! empty( $gateway->settings['sandbox'] ) && 'yes' == $gateway->settings['sandbox'] ) ? true : false,
			'hide_button_mode' => ( ! empty( $gateway->settings['hide_button_mode'] ) && 'yes' == $gateway->settings['hide_button_mode'] ) ? true : false,
			'transaction_type' => ( isset( $gateway->settings['payment_capture'] ) && empty( $gateway->settings['payment_capture'] ) ) ? 'charge' : 'authorize',
		);
	}

	/**
	 * Return the eWay settings to be used by Robot Ninja.
	 *
	 * @since 1.13.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_eway_settings( $gateway ) {
		return array(
			'testmode'    => ( ! empty( $gateway->settings['testmode'] ) && 'yes' == $gateway->settings['testmode'] ) ? true : false,
			'saved_cards' => ( ! empty( $gateway->settings['saved_cards'] ) && 'yes' == $gateway->settings['saved_cards'] ) ? true : false,
		);
	}

	/**
	 * Return the Payfast settings to be used by Robot Ninja.
	 *
	 * @since 1.13.0
	 * @param WC_Gateway $gateway
	 * @param array
	 */
	public static function get_payfast_settings( $gateway ) {
		return array(
			'testmode' => ( ! empty( $gateway->settings['testmode'] ) && 'yes' == $gateway->settings['testmode'] ) ? true : false,
		);
	}
}
