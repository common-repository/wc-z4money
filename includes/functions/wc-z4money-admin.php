<?php

/**
 * Register styles and scripts in admin panel.
 *
 * @link       https://github.com/santanamic/wc-z4money
 * @since      1.0.0
 * 
 * @package    wc-z4money
 * @subpackage wc-z4money/includes/functions/admin/
 * @author     AQUARELA - WILLIAN SANTANA <williansantanamic@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! function_exists( 'wc_z4money_admin_enqueue_script' ) ) {

/**
 * Register scripts in WP admin
 *
 * @since    1.0.0
 * @return   array    void
 */
	function wc_z4money_admin_enqueue_script() 
	{
	
	wp_enqueue_script( 'wc-z4money-admin', WC_Z4MONEY_URI . 'admin/assets/js/script.js' );

	}

}

if ( ! function_exists( 'wc_z4money_admin_enqueue_styles' ) ) {

/**
 * Register styles in WP admin
 *
 * @since    1.0.0
 * @return   array    void
 */
	function wc_z4money_admin_enqueue_styles() 
	{

	wp_enqueue_style( 'wc-z4money-admin', WC_Z4MONEY_URI . 'admin/assets/css/style.css' );

	}

}

if ( ! function_exists( 'wc_z4money_admin_links' ) ) {

	/**
	 * Add link shortcut in admin page
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function wc_z4money_admin_links( $links ) 
	{

	$links[] = '<a href="' . esc_url( admin_url('admin.php?page=wc-settings&tab=checkout' ) ) . '">' . __( 'Settings', 'wc-z4money' ) . '</a>';
	$links[] = '<a href="https://www.z4money.com.br/contato/">' . __('Support', 'wc-z4money') . '</a>';
	
	return $links;
	
	}
	
}