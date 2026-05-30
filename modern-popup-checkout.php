<?php
/**
 * Plugin Name: Modern Popup Checkout
 * Plugin URI: https://github.com/ghasemjj/modern-popup-checkout
 * Description: A professional WooCommerce popup checkout plugin with glassmorphism design and multi-step process
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/ghasemjj
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: modern-popup-checkout
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 *
 * @package Modern_Popup_Checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'MPPC_VERSION', '1.0.0' );
define( 'MPPC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MPPC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MPPC_ASSETS_URL', MPPC_PLUGIN_URL . 'assets/' );
define( 'MPPC_INCLUDES_DIR', MPPC_PLUGIN_DIR . 'includes/' );

// Autoloader
spl_autoload_register( function ( $class ) {
	// Only load classes from this plugin
	if ( false === strpos( $class, 'MPPC' ) ) {
		return;
	}

	$file = MPPC_INCLUDES_DIR . 'class-' . strtolower( str_replace( '_', '-', substr( $class, 5 ) ) ) . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );

// Check WooCommerce is active
function mppc_check_woocommerce() {
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
		add_action( 'admin_notices', 'mppc_woocommerce_missing_notice' );
		return false;
	}
	return true;
}

// WooCommerce missing notice
function mppc_woocommerce_missing_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Modern Popup Checkout requires WooCommerce to be installed and activated.', 'modern-popup-checkout' ); ?></p>
	</div>
	<?php
}

// Plugin initialization
function mppc_init() {
	// Load text domain
	load_plugin_textdomain( 'modern-popup-checkout', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	if ( ! mppc_check_woocommerce() ) {
		return;
	}

	// Initialize main plugin class
	MPPC_Plugin::get_instance();
}

// Hook into WordPress
add_action( 'plugins_loaded', 'mppc_init' );

// Activation hook
register_activation_hook( __FILE__, 'mppc_activate_plugin' );
function mppc_activate_plugin() {
	if ( ! mppc_check_woocommerce() ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html__( 'Modern Popup Checkout requires WooCommerce to be installed and activated.', 'modern-popup-checkout' ) );
	}

	// Set default options
	$defaults = array(
		'enable_glassmorphism' => true,
		'primary_color'        => '#6366f1',
		'enable_shake_effect'  => true,
		'enable_progress_bar'  => true,
		'thank_you_message'    => __( 'Thank you for your purchase ❤️' . "\n" . 'Your order has been successfully placed and will be processed soon.', 'modern-popup-checkout' ),
	);

	foreach ( $defaults as $key => $value ) {
		if ( ! get_option( 'mppc_' . $key ) ) {
			update_option( 'mppc_' . $key, $value );
		}
	}
}

// Deactivation hook
register_deactivation_hook( __FILE__, 'mppc_deactivate_plugin' );
function mppc_deactivate_plugin() {
	// Clean up any transients if needed
	wp_cache_flush();
}
