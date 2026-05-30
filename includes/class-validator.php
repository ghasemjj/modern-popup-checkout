<?php
/**
 * Validator Class
 *
 * @package Modern_Popup_Checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for validating checkout data
 */
class MPPC_Validator {

	/**
	 * Validate step 1 data
	 *
	 * @param array $data Form data.
	 * @return array Validation result.
	 */
	public function validate_step1( $data ) {
		$errors = array();

		// Validate first name
		if ( empty( $data['firstName'] ) ) {
			$errors['firstName'] = __( 'First name is required', 'modern-popup-checkout' );
		} else {
			$data['firstName'] = sanitize_text_field( $data['firstName'] );
		}

		// Validate last name
		if ( empty( $data['lastName'] ) ) {
			$errors['lastName'] = __( 'Last name is required', 'modern-popup-checkout' );
		} else {
			$data['lastName'] = sanitize_text_field( $data['lastName'] );
		}

		// Validate phone
		if ( empty( $data['phone'] ) ) {
			$errors['phone'] = __( 'Mobile number is required', 'modern-popup-checkout' );
		} elseif ( ! $this->validate_iranian_phone( $data['phone'] ) ) {
			$errors['phone'] = __( 'Please enter a valid Iranian phone number', 'modern-popup-checkout' );
		} else {
			$data['phone'] = sanitize_text_field( $data['phone'] );
		}

		if ( ! empty( $errors ) ) {
			return array(
				'success' => false,
				'errors'  => $errors,
			);
		}

		return array(
			'success' => true,
			'data'    => $data,
		);
	}

	/**
	 * Validate step 2 data
	 *
	 * @param array $data Form data.
	 * @return array Validation result.
	 */
	public function validate_step2( $data ) {
		$errors = array();

		// Validate province
		if ( empty( $data['province'] ) ) {
			$errors['province'] = __( 'Province is required', 'modern-popup-checkout' );
		}

		// Validate city
		if ( empty( $data['city'] ) ) {
			$errors['city'] = __( 'City is required', 'modern-popup-checkout' );
		}

		// Validate street
		if ( empty( $data['street'] ) ) {
			$errors['street'] = __( 'Street is required', 'modern-popup-checkout' );
		}

		// Validate postal code
		if ( empty( $data['postalCode'] ) ) {
			$errors['postalCode'] = __( 'Postal code is required', 'modern-popup-checkout' );
		} elseif ( ! $this->validate_postal_code( $data['postalCode'] ) ) {
			$errors['postalCode'] = __( 'Please enter a valid postal code', 'modern-popup-checkout' );
		}

		// Validate shipping method
		if ( empty( $data['shippingMethod'] ) ) {
			$errors['shippingMethod'] = __( 'Please select a shipping method', 'modern-popup-checkout' );
		}

		if ( ! empty( $errors ) ) {
			return array(
				'success' => false,
				'errors'  => $errors,
			);
		}

		return array(
			'success' => true,
			'data'    => $data,
		);
	}

	/**
	 * Validate Iranian phone number
	 *
	 * @param string $phone Phone number.
	 * @return bool True if valid.
	 */
	private function validate_iranian_phone( $phone ) {
		$phone = preg_replace( '/[^0-9]/', '', $phone );

		// Valid Iranian phone patterns
		$patterns = array(
			'/^989[0-9]{9}$/',           // +989XX XXXXXXXXX
			'/^09[0-9]{9}$/',            // 09XX XXXXXXXXX
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $phone ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validate postal code
	 *
	 * @param string $postal_code Postal code.
	 * @return bool True if valid.
	 */
	private function validate_postal_code( $postal_code ) {
		// Iranian postal code format: 10 digits
		return preg_match( '/^[0-9]{10}$/', $postal_code );
	}
}
