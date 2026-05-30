<?php
/**
 * Main Plugin Class
 *
 * @package Modern_Popup_Checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 */
class MPPC_Plugin {

	/**
	 * Single instance of the plugin
	 *
	 * @var MPPC_Plugin
	 */
	private static $instance = null;

	/**
	 * Get single instance
	 *
	 * @return MPPC_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
		$this->load_classes();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Frontend hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_filter( 'woocommerce_checkout_redirect_url', array( $this, 'disable_checkout_redirect' ) );
		
		// Backend hooks
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		
		// AJAX hooks
		add_action( 'wp_ajax_mppc_validate_step1', array( $this, 'ajax_validate_step1' ) );
		add_action( 'wp_ajax_nopriv_mppc_validate_step1', array( $this, 'ajax_validate_step1' ) );
		
		add_action( 'wp_ajax_mppc_validate_step2', array( $this, 'ajax_validate_step2' ) );
		add_action( 'wp_ajax_nopriv_mppc_validate_step2', array( $this, 'ajax_validate_step2' ) );
		
		add_action( 'wp_ajax_mppc_validate_coupon', array( $this, 'ajax_validate_coupon' ) );
		add_action( 'wp_ajax_nopriv_mppc_validate_coupon', array( $this, 'ajax_validate_coupon' ) );
		
		add_action( 'wp_ajax_mppc_get_payment_gateways', array( $this, 'ajax_get_payment_gateways' ) );
		add_action( 'wp_ajax_nopriv_mppc_get_payment_gateways', array( $this, 'ajax_get_payment_gateways' ) );
		
		add_action( 'wp_ajax_mppc_process_checkout', array( $this, 'ajax_process_checkout' ) );
		add_action( 'wp_ajax_nopriv_mppc_process_checkout', array( $this, 'ajax_process_checkout' ) );
		
		add_action( 'wp_ajax_mppc_get_shipping_methods', array( $this, 'ajax_get_shipping_methods' ) );
		add_action( 'wp_ajax_nopriv_mppc_get_shipping_methods', array( $this, 'ajax_get_shipping_methods' ) );
		
		// Checkout page modification
		add_action( 'woocommerce_before_checkout_form', array( $this, 'output_popup_modal' ) );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'hide_checkout_fields' ) );
	}

	/**
	 * Load required classes
	 */
	private function load_classes() {
		require_once MPPC_INCLUDES_DIR . 'class-checkout.php';
		require_once MPPC_INCLUDES_DIR . 'class-validator.php';
		require_once MPPC_INCLUDES_DIR . 'class-cart.php';
	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets() {
		if ( is_checkout() ) {
			// Styles
			wp_enqueue_style(
				'mppc-frontend',
				MPPC_ASSETS_URL . 'css/frontend.css',
				array(),
				MPPC_VERSION
			);

			// Scripts
			wp_enqueue_script(
				'mppc-frontend',
				MPPC_ASSETS_URL . 'js/frontend.js',
				array( 'jquery', 'wp-i18n' ),
				MPPC_VERSION,
				true
			);

			// Localize script
			wp_localize_script(
				'mppc-frontend',
				'mppcData',
				array(
					'ajaxUrl'              => admin_url( 'admin-ajax.php' ),
					'nonce'                => wp_create_nonce( 'mppc_nonce' ),
					'enableGlassmorphism'  => get_option( 'mppc_enable_glassmorphism', true ),
					'primaryColor'         => get_option( 'mppc_primary_color', '#6366f1' ),
					'enableShakeEffect'    => get_option( 'mppc_enable_shake_effect', true ),
					'enableProgressBar'    => get_option( 'mppc_enable_progress_bar', true ),
					'currencySymbol'       => html_entity_decode( get_woocommerce_currency_symbol() ),
					'currencyPos'          => get_option( 'woocommerce_currency_pos' ),
					'thousandSeparator'    => wc_get_price_thousand_separator(),
					'decimalSeparator'     => wc_get_price_decimal_separator(),
					'priceDecimals'        => wc_get_price_decimals(),
					'i18n'                 => array(
						'step1_title'          => __( 'Personal Information', 'modern-popup-checkout' ),
						'step2_title'          => __( 'Shipping Address', 'modern-popup-checkout' ),
						'step3_title'          => __( 'Payment', 'modern-popup-checkout' ),
						'step4_title'          => __( 'Order Complete', 'modern-popup-checkout' ),
						'firstName'            => __( 'First Name', 'modern-popup-checkout' ),
						'lastName'             => __( 'Last Name', 'modern-popup-checkout' ),
						'phone'                => __( 'Mobile Number', 'modern-popup-checkout' ),
						'required'             => __( 'Required', 'modern-popup-checkout' ),
						'invalidPhone'         => __( 'Please enter a valid Iranian phone number', 'modern-popup-checkout' ),
						'continue'             => __( 'Continue', 'modern-popup-checkout' ),
						'back'                 => __( 'Back', 'modern-popup-checkout' ),
						'province'             => __( 'Province', 'modern-popup-checkout' ),
						'city'                 => __( 'City', 'modern-popup-checkout' ),
						'street'               => __( 'Street', 'modern-popup-checkout' ),
						'buildingNumber'       => __( 'Building Number', 'modern-popup-checkout' ),
						'unit'                 => __( 'Unit', 'modern-popup-checkout' ),
						'postalCode'           => __( 'Postal Code', 'modern-popup-checkout' ),
						'shippingMethod'       => __( 'Shipping Method', 'modern-popup-checkout' ),
						'orderSummary'         => __( 'Order Summary', 'modern-popup-checkout' ),
						'products'             => __( 'Products', 'modern-popup-checkout' ),
						'subtotal'             => __( 'Subtotal', 'modern-popup-checkout' ),
						'shippingCost'         => __( 'Shipping Cost', 'modern-popup-checkout' ),
						'couponCode'           => __( 'Coupon Code', 'modern-popup-checkout' ),
						'applyCoupon'          => __( 'Apply', 'modern-popup-checkout' ),
						'total'                => __( 'Total', 'modern-popup-checkout' ),
						'paymentMethod'        => __( 'Payment Method', 'modern-popup-checkout' ),
						'finalPayment'         => __( 'Final Payment', 'modern-popup-checkout' ),
						'orderNumber'          => __( 'Order Number', 'modern-popup-checkout' ),
						'backToShop'           => __( 'Back to Shop', 'modern-popup-checkout' ),
						'viewOrder'            => __( 'View Order', 'modern-popup-checkout' ),
						'invalidCoupon'        => __( 'Invalid coupon code', 'modern-popup-checkout' ),
						'couponApplied'        => __( 'Coupon applied successfully', 'modern-popup-checkout' ),
						'selectShippingMethod' => __( 'Please select a shipping method', 'modern-popup-checkout' ),
						'selectPaymentMethod'  => __( 'Please select a payment method', 'modern-popup-checkout' ),
					),
				)
			);
		}
	}

	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets( $page ) {
		if ( 'toplevel_page_mppc-settings' === $page ) {
			wp_enqueue_style(
				'mppc-admin',
				MPPC_ASSETS_URL . 'css/admin.css',
				array(),
				MPPC_VERSION
			);

			wp_enqueue_script(
				'mppc-admin',
				MPPC_ASSETS_URL . 'js/admin.js',
				array( 'jquery', 'wp-color-picker' ),
				MPPC_VERSION,
				true
			);

			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Modern Popup Checkout', 'modern-popup-checkout' ),
			__( 'Popup Checkout', 'modern-popup-checkout' ),
			'manage_options',
			'mppc-settings',
			array( $this, 'render_settings_page' ),
			'dashicons-shopping-cart',
			56
		);
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		require_once MPPC_INCLUDES_DIR . 'admin/settings-page.php';
	}

	/**
	 * Output popup modal
	 */
	public function output_popup_modal() {
		require_once MPPC_INCLUDES_DIR . 'frontend/popup-modal.php';
	}

	/**
	 * Hide checkout fields
	 */
	public function hide_checkout_fields( $fields ) {
		// Fields will be handled by popup instead
		return $fields;
	}

	/**
	 * Disable checkout redirect
	 */
	public function disable_checkout_redirect( $url ) {
		return $url;
	}

	/**
	 * AJAX: Validate step 1
	 */
	public function ajax_validate_step1() {
		check_ajax_referer( 'mppc_nonce', 'nonce' );

		$validator = new MPPC_Validator();
		$response  = $validator->validate_step1( $_POST );

		wp_send_json( $response );
	}

	/**
	 * AJAX: Validate step 2
	 */
	public function ajax_validate_step2() {
		check_ajax_referer( 'mppc_nonce', 'nonce' );

		$validator = new MPPC_Validator();
		$response  = $validator->validate_step2( $_POST );

		wp_send_json( $response );
	}

	/**
	 * AJAX: Validate coupon
	 */
	public function ajax_validate_coupon() {
		check_ajax_referer( 'mppc_nonce', 'nonce' );

		$coupon_code = isset( $_POST['coupon_code'] ) ? sanitize_text_field( $_POST['coupon_code'] ) : '';
		$cart        = new MPPC_Cart();
		$response    = $cart->validate_coupon( $coupon_code );

		wp_send_json( $response );
	}

	/**
	 * AJAX: Get payment gateways
	 */
	public function ajax_get_payment_gateways() {
		check_ajax_referer( 'mppc_nonce', 'nonce' );

		$gateways = WC()->payment_gateways->get_available_payment_gateways();
		$response = array(
			'success'   => true,
			'gateways'  => array(),
		);

		foreach ( $gateways as $gateway ) {
			$response['gateways'][] = array(
				'id'    => $gateway->id,
				'title' => $gateway->get_title(),
				'icon'  => $gateway->get_icon(),
			);
		}

		wp_send_json( $response );
	}

	/**
	 * AJAX: Get shipping methods
	 */
	public function ajax_get_shipping_methods() {
		check_ajax_referer( 'mppc_nonce', 'nonce' );

		$cart        = new MPPC_Cart();
		$methods     = $cart->get_shipping_methods();
		$response    = array(
			'success' => true,
			'methods' => $methods,
		);

		wp_send_json( $response );
	}

	/**
	 * AJAX: Process checkout
	 */
	public function ajax_process_checkout() {
		check_ajax_referer( 'mppc_nonce', 'nonce' );

		$checkout = new MPPC_Checkout();
		$response = $checkout->process_checkout( $_POST );

		wp_send_json( $response );
	}
}
