<?php if ( ! defined( 'ABSPATH' ) ) die;

/**
 * Plugin Name:       Z4Money for WooCommerce
 * Plugin URI:        https://github.com/santanamic/wc-z4money
 * Description:       Take payments using Z4Money.
 * Version:           1.2.2
 * Author:            WILLIAN SANTANA
 * Author URI:        https://github.com/santanamic/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-z4money
 * Domain Path:       /languages
 */

define( 'WC_Z4MONEY_NAME', 'WooCommerce Z4Money' );
define( 'WC_Z4MONEY_VERSION', '1.2.2' );
define( 'WC_Z4MONEY_DEBUG_OUTPUT', 0 );
define( 'WC_Z4MONEY_BASENAME', plugin_basename( __FILE__ ) );
define( 'WC_Z4MONEY_SLUG', plugin_basename( plugin_dir_path( __FILE__ ) ) );
define( 'WC_Z4MONEY_CORE_FILE', __FILE__ );
define( 'WC_Z4MONEY_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WC_Z4MONEY_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

require_once( WC_Z4MONEY_PATH . 'vendor/autoload.php' );

/**
 * The code that runs during plugin activation and The code that runs during plugin deactivation.
 */
register_activation_hook( __FILE__, 'wc_z4money_plugin_activate' );
register_deactivation_hook( __FILE__, 'wc_z4money_plugin_deactivate' );

/**
 * Initial hook for plugin run and Initial hook for plugin internationalization.
 */
add_action( 'plugins_loaded', 'wc_z4money' );
add_action( 'plugins_loaded', 'wc_z4money_plugin_i18n' );

/**
 * Initial hook for add admin scripts and styles.
 */
add_filter( 'plugin_action_links_' . WC_Z4MONEY_BASENAME, 'wc_z4money_admin_links' );
add_action( 'admin_enqueue_scripts', 'wc_z4money_admin_enqueue_script' );
add_action( 'admin_enqueue_scripts', 'wc_z4money_admin_enqueue_styles' );
