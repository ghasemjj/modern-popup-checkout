<?php
/**
 * Cart Handler Class
 *
 * @package Modern_Popup_Checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for handling cart operations
 */
class MPPC_Cart {

	/**
	 * Validate coupon
	 *
	 * @param string $coupon_code Coupon code.
	 * @return array Validation result.
	 */
	public function validate_coupon( $coupon_code ) {
		if ( empty( $coupon_code ) ) {
			return array(
				'success' => false,
				'message' => __( 'Coupon code is required', 'modern-popup-checkout' ),
			);
		}

		$coupon = new WC_Coupon( $coupon_code );

		// Check if coupon exists
		if ( ! $coupon->get_id() ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid coupon code', 'modern-popup-checkout' ),
			);
		}

		// Check if coupon is valid
		$validation = $coupon->validate();

		if ( is_wp_error( $validation ) ) {
			return array(
				'success' => false,
				'message' => $validation->get_error_message(),
			);
		}

		// Apply coupon to cart
		WC()->cart->apply_coupon( $coupon_code );

		// Get new totals
		$totals = $this->get_cart_totals();

		return array(
			'success' => true,
			'message' => __( 'Coupon applied successfully', 'modern-popup-checkout' ),
			'discount' => WC()->cart->get_discount_total(),
			'total'    => $totals['total'],
		);
	}

	/**
	 * Get cart totals
	 *
	 * @return array Cart totals.
	 */
	public function get_cart_totals() {
		return array(
			'subtotal'      => WC()->cart->get_subtotal(),
			'discount'      => WC()->cart->get_discount_total(),
			'shipping'      => WC()->cart->get_shipping_total(),
			'tax'           => WC()->cart->get_tax_total(),
			'total'         => WC()->cart->get_total(),
		);
	}

	/**
	 * Get shipping methods
	 *
	 * @return array Available shipping methods.
	 */
	public function get_shipping_methods() {
		$methods = array();
		$zones   = WC_Shipping_Zones::get_zones();

		foreach ( $zones as $zone ) {
			$shipping_methods = $zone['shipping_methods'];

			foreach ( $shipping_methods as $method ) {
				if ( $method->is_enabled() ) {
					$methods[] = array(
						'id'    => $method->get_instance_id(),
						'title' => $method->get_method_title(),
						'cost'  => $method->cost,
					);
				}
			}
		}

		return $methods;
	}
}
