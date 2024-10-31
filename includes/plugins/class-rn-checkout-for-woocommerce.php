<?php

/**
 * Robot Ninja Checkout For WooCommerce support class
 *
 * @author 	Prospress
 * @since 	1.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RN_Checkout_For_WooCommerce {

	/**
	 * Initialise the Checkout For WooCommerce support class
	 *
	 * @since 1.11.0
	 */
	public static function init() {
		add_filter( 'rn_helper_plugin_support_settings', __CLASS__ . '::add_plugin_setings' );
	}

	/**
	 * Check if Checkout for WooCommerce is enabled in the settings
	 *
	 * @since 1.11.0
	 * @param array $plugin_support_settings - stores all the store theme settings that WALL-E needs to know about
	 * @return array
	 */
	public static function add_plugin_setings( $plugin_support_settings ) {
		$settings = get_option( '_cfw__settings', false );

		if ( $settings ) {
			$plugin_support_settings['checkout_for_woocommerce']['enabled'] = ( isset( $settings['enable'] ) && 'yes' == $settings['enable'] ) ? true : false;
		}

		return $plugin_support_settings;
	}
}
RN_Checkout_For_WooCommerce::init();
