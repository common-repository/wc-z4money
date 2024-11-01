<?php if ( ! defined( 'ABSPATH' ) ) die;

if ( ! function_exists( 'wc_z4money_plugin_activate' ) ) {

	/**
	 * Plugin activate call function
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function wc_z4money_plugin_activate() 
	{

	}

}

if ( ! function_exists( 'wc_z4money_plugin_deactivate' ) ) {

	/**
	 * Plugin deactivation call function
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function wc_z4money_plugin_deactivate() 
	{

	}
	
}

if ( ! function_exists( 'wc_z4money_plugin_i18n' ) ) {

	/**
	 * Load the plugin text domain for translation
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	function wc_z4money_plugin_i18n() 
	{

	load_plugin_textdomain( 'wc-z4money', false, WC_Z4MONEY_SLUG . '/languages/' );
	
	}
	
}

if ( ! function_exists( 'wc_z4money' ) ) {

	/**
	 * Begins execution of the plugin.
	 *
	 * @since    1.0.0
	 * @return   Wc_Z4Money
	 */
	function wc_z4money() 
	{
		
	$plugin = Wc_Z4Money::instance();
		
	return $plugin;
	
	}

}