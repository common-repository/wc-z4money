<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Functions for payment method.
 *
 * @link       https://github.com/santanamic/wc-z4money
 * @since      1.0.0
 * 
 * @package    wc-z4money
 * @subpackage wc-z4money/includes/gateway/methods/creditcard/
 * @author     AQUARELA - WILLIAN SANTANA <williansantanamic@gmail.com>
 */
 
if ( ! function_exists( 'wc_z4money_method_creditcard_admin_enqueue' ) ) {

/**
 * Register admin styles and scripts for payment method
 *
 * @since    1.0.0
 * @return   array    void
 */
	function wc_z4money_method_creditcard_admin_enqueue() 
	{

	wp_enqueue_script( 'wc-z4money-method-creditcard-admin', 
		WC_Z4MONEY_URI . 'includes/gateway/methods/creditcard/assets/admin/js/script.js' );
	wp_enqueue_style( 'wc-z4money-method-creditcard-admin', 
		WC_Z4MONEY_URI . 'includes/gateway/methods/creditcard/assets/admin/css/style.css' );

	}

}

if ( ! function_exists( 'wc_z4money_method_creditcard_public_enqueue' ) ) {

/**
 * Register public styles and scripts for payment method
 *
 * @since    1.0.0
 * @return   array    void
 */
	function wc_z4money_method_creditcard_public_enqueue() 
	{

	wp_enqueue_script( 'wc-z4money-method-creditcard', 
		WC_Z4MONEY_URI . 'includes/gateway/methods/creditcard/assets/public/js/script.js', array( 'jquery', 'wc-credit-card-form' ), WC_Z4MONEY_VERSION, true );
	wp_enqueue_style( 'wc-z4money-method-creditcard', 
		WC_Z4MONEY_URI . 'includes/gateway/methods/creditcard/assets/public/css/style.css' );

	}

}