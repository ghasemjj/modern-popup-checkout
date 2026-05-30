<?php
/**
 * Checkout Handler Class
 *
 * @package Modern_Popup_Checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for handling checkout operations
 */
class MPPC_Checkout {

	/**
	 * Process checkout
	 *
	 * @param array $data Checkout data from form.
	 * @return array Response array.
	 */
	public function process_checkout( $data ) {
		try {
			// Validate required data
			if ( ! isset( $data['firstName'], $data['lastName'], $data['phone'], $data['province'], $data['city'], $data['street'], $data['postalCode'], $data['paymentMethod'] ) ) {
				return array(
					'success' => false,
					'message' => __( 'Missing required fields', 'modern-popup-checkout' ),
				);
			}

			// Get cart
			$cart = WC()->cart;

			if ( $cart->is_empty() ) {
				return array(
					'success' => false,
					'message' => __( 'Your cart is empty', 'modern-popup-checkout' ),
				);
			}

			// Create order
			$order = $this->create_order( $data );

			if ( is_wp_error( $order ) ) {
				return array(
					'success' => false,
					'message' => $order->get_error_message(),
				);
			}

			// Process payment
			$payment_result = $this->process_payment( $order, $data['paymentMethod'] );

			if ( ! $payment_result['success'] ) {
				return $payment_result;
			}

			// Clear cart
			$cart->empty_cart();

			return array(
				'success'      => true,
				'message'      => __( 'Order placed successfully', 'modern-popup-checkout' ),
				'orderNumber'  => $order->get_order_number(),
				'orderTotal'   => $order->get_total(),
				'orderUrl'     => $order->get_checkout_order_received_url(),
				'redirectUrl'  => apply_filters( 'mppc_checkout_redirect_url', wc_get_page_permalink( 'shop' ) ),
			);
		} catch ( Exception $e ) {
			return array(
				'success' => false,
				'message' => $e->getMessage(),
			);
		}
	}

	/**
	 * Create order
	 *
	 * @param array $data Order data.
	 * @return WC_Order|WP_Error Order object or error.
	 */
	private function create_order( $data ) {
		try {
			$order = wc_create_order();

			if ( is_wp_error( $order ) ) {
				return $order;
			}

			// Add items to order
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$product = $cart_item['data'];
				$order->add_product( $product, $cart_item['quantity'] );
			}

			// Set billing address
			$order->set_billing_first_name( sanitize_text_field( $data['firstName'] ) );
			$order->set_billing_last_name( sanitize_text_field( $data['lastName'] ) );
			$order->set_billing_phone( sanitize_text_field( $data['phone'] ) );
			$order->set_billing_address_1( sanitize_text_field( $data['street'] ) );
			$order->set_billing_address_2( isset( $data['unit'] ) ? sanitize_text_field( $data['unit'] ) : '' );
			$order->set_billing_city( sanitize_text_field( $data['city'] ) );
			$order->set_billing_state( sanitize_text_field( $data['province'] ) );
			$order->set_billing_postcode( sanitize_text_field( $data['postalCode'] ) );
			$order->set_billing_country( WC()->customer->get_billing_country() );

			// Set shipping address (same as billing)
			$order->set_shipping_first_name( sanitize_text_field( $data['firstName'] ) );
			$order->set_shipping_last_name( sanitize_text_field( $data['lastName'] ) );
			$order->set_shipping_address_1( sanitize_text_field( $data['street'] ) );
			$order->set_shipping_address_2( isset( $data['unit'] ) ? sanitize_text_field( $data['unit'] ) : '' );
			$order->set_shipping_city( sanitize_text_field( $data['city'] ) );
			$order->set_shipping_state( sanitize_text_field( $data['province'] ) );
			$order->set_shipping_postcode( sanitize_text_field( $data['postalCode'] ) );
			$order->set_shipping_country( WC()->customer->get_billing_country() );

			// Set shipping method
			if ( isset( $data['shippingMethod'] ) ) {
				$order->set_shipping_method( sanitize_text_field( $data['shippingMethod'] ) );
			}

			// Calculate totals
			$order->calculate_totals();

			// Save order
			$order->save();

			// Apply coupon if exists
			if ( isset( $data['couponCode'] ) && ! empty( $data['couponCode'] ) ) {
				$order->apply_coupon( sanitize_text_field( $data['couponCode'] ) );
				$order->save();
			}

			return $order;
		} catch ( Exception $e ) {
			return new WP_Error( 'order_creation_failed', $e->getMessage() );
		}
	}

	/**
	 * Process payment
	 *
	 * @param WC_Order $order Order object.
	 * @param string   $gateway_id Payment gateway ID.
	 * @return array Response array.
	 */
	private function process_payment( $order, $gateway_id ) {
		try {
			$gateways = WC()->payment_gateways->get_available_payment_gateways();

			if ( ! isset( $gateways[ $gateway_id ] ) ) {
				return array(
					'success' => false,
					'message' => __( 'Invalid payment method', 'modern-popup-checkout' ),
				);
			}

			$gateway = $gateways[ $gateway_id ];

			// Set payment method
			$order->set_payment_method( $gateway );
			$order->save();

			// Check if gateway requires redirect
			if ( $gateway->has_fields() || $gateway->supports( 'default_credit_card_form' ) ) {
				// Process payment through gateway
				do_action( 'woocommerce_payment_complete', $order->get_id() );
				$order->payment_complete();
			} else {
				// Auto-complete order
				$order->payment_complete();
			}

			return array(
				'success' => true,
				'message' => __( 'Payment processed successfully', 'modern-popup-checkout' ),
			);
		} catch ( Exception $e ) {
			return array(
				'success' => false,
				'message' => $e->getMessage(),
			);
		}
	}
}
