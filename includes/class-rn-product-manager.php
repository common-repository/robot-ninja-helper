<?php
/**
 * Robot Ninja Product Manager class
 *
 * @author 	Prospress
 * @since 	1.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RN_Product_Manager {

	/**
	 * Initialise Robot Ninja Helper Product Manager class
	 *
	 * @since 1.10.0
	 */
	public static function init() {
		if ( defined( 'RN_UPDATE_PRODUCT_SALES_COUNT' ) && ! RN_UPDATE_PRODUCT_SALES_COUNT ) {
			add_action( 'woocommerce_order_status_completed', __CLASS__ . '::dont_update_total_sales_count', 5 );
			add_action( 'woocommerce_order_status_processing', __CLASS__ . '::dont_update_total_sales_count', 5 );
			add_action( 'woocommerce_order_status_on-hold', __CLASS__ . '::dont_update_total_sales_count', 5 );
		}
	}

	/**
	 * Make sure we don't record product sales for purchases made by Robot Ninja test customers
	 * This is to stop products used for testing appearing at the top of the "sort by popularity" query.
	 *
	 * @since 1.10.0
	 * @param int $order_id
	 * @return void
	 */
	public static function dont_update_total_sales_count( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order && is_a( $order, 'WC_Order' ) && ! $order->get_data_store()->get_recorded_sales( $order ) && $order->get_billing_email() ) {
			$billing_email = $order->get_billing_email();

			if ( preg_match( '/store[\+]guest[\-](\d+)[\@]robotninja.com/', $billing_email ) || preg_match( '/store[\+](\d+)[\@]robotninja.com/', $billing_email ) ) {
				remove_action( 'woocommerce_order_status_completed', 'wc_update_total_sales_counts' );
				remove_action( 'woocommerce_order_status_processing', 'wc_update_total_sales_counts' );
				remove_action( 'woocommerce_order_status_on-hold', 'wc_update_total_sales_counts' );
			}
		}
	}
}
RN_Product_Manager::init();
