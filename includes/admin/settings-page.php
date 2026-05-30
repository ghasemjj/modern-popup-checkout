<?php
/**
 * Admin settings page
 *
 * @package Modern_Popup_Checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Save settings
if ( isset( $_POST['mppc_save_settings'] ) && wp_verify_nonce( $_POST['mppc_nonce'], 'mppc_settings' ) ) {
	update_option( 'mppc_enable_glassmorphism', isset( $_POST['enable_glassmorphism'] ) );
	update_option( 'mppc_primary_color', sanitize_hex_color( $_POST['primary_color'] ?? '#6366f1' ) );
	update_option( 'mppc_enable_shake_effect', isset( $_POST['enable_shake_effect'] ) );
	update_option( 'mppc_enable_progress_bar', isset( $_POST['enable_progress_bar'] ) );
	update_option( 'mppc_thank_you_message', wp_kses_post( $_POST['thank_you_message'] ?? '' ) );
	
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'modern-popup-checkout' ) . '</p></div>';
}

$enable_glassmorphism = get_option( 'mppc_enable_glassmorphism', true );
$primary_color = get_option( 'mppc_primary_color', '#6366f1' );
$enable_shake_effect = get_option( 'mppc_enable_shake_effect', true );
$enable_progress_bar = get_option( 'mppc_enable_progress_bar', true );
$thank_you_message = get_option( 'mppc_thank_you_message', __( 'Thank you for your purchase ❤️' . "\n" . 'Your order has been successfully placed and will be processed soon.', 'modern-popup-checkout' ) );
?>

<div class="wrap mppc-settings">
	<h1><?php esc_html_e( 'Modern Popup Checkout Settings', 'modern-popup-checkout' ); ?></h1>

	<div class="mppc-settings-container">
		<form method="post" action="">
			<?php wp_nonce_field( 'mppc_settings', 'mppc_nonce' ); ?>

			<div class="mppc-settings-section">
				<h2><?php esc_html_e( 'Design Settings', 'modern-popup-checkout' ); ?></h2>

				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="enable_glassmorphism">
								<?php esc_html_e( 'Glassmorphism Effect', 'modern-popup-checkout' ); ?>
							</label>
						</th>
						<td>
							<input type="checkbox" id="enable_glassmorphism" name="enable_glassmorphism" value="1" <?php checked( $enable_glassmorphism ); ?>>
							<p class="description">
								<?php esc_html_e( 'Enable glassmorphism effect on the modal background', 'modern-popup-checkout' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="primary_color">
								<?php esc_html_e( 'Primary Color', 'modern-popup-checkout' ); ?>
							</label>
						</th>
						<td>
							<input type="text" id="primary_color" name="primary_color" class="color-picker" value="<?php echo esc_attr( $primary_color ); ?>">
							<p class="description">
								<?php esc_html_e( 'Main color for buttons and highlights', 'modern-popup-checkout' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="enable_shake_effect">
								<?php esc_html_e( 'Shake Effect on Error', 'modern-popup-checkout' ); ?>
							</label>
						</th>
						<td>
							<input type="checkbox" id="enable_shake_effect" name="enable_shake_effect" value="1" <?php checked( $enable_shake_effect ); ?>>
							<p class="description">
								<?php esc_html_e( 'Enable shake animation when validation fails', 'modern-popup-checkout' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="enable_progress_bar">
								<?php esc_html_e( 'Progress Bar', 'modern-popup-checkout' ); ?>
							</label>
						</th>
						<td>
							<input type="checkbox" id="enable_progress_bar" name="enable_progress_bar" value="1" <?php checked( $enable_progress_bar ); ?>>
							<p class="description">
								<?php esc_html_e( 'Show progress bar on checkout steps', 'modern-popup-checkout' ); ?>
							</p>
						</td>
					</tr>
				</table>
			</div>

			<div class="mppc-settings-section">
				<h2><?php esc_html_e( 'Messages', 'modern-popup-checkout' ); ?></h2>

				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="thank_you_message">
								<?php esc_html_e( 'Thank You Message', 'modern-popup-checkout' ); ?>
							</label>
						</th>
						<td>
							<?php
							wp_editor(
								$thank_you_message,
								'thank_you_message',
								array(
									'textarea_rows' => 5,
									'media_buttons' => false,
									'teeny'         => true,
								)
							);
							?>
							<p class="description">
								<?php esc_html_e( 'Message shown to customer after successful order', 'modern-popup-checkout' ); ?>
							</p>
						</td>
					</tr>
				</table>
			</div>

			<div class="mppc-settings-section">
				<h2><?php esc_html_e( 'Information', 'modern-popup-checkout' ); ?></h2>

				<div class="mppc-info-box">
					<h3><?php esc_html_e( 'Plugin Version', 'modern-popup-checkout' ); ?></h3>
					<p><?php echo esc_html( MPPC_VERSION ); ?></p>

					<h3><?php esc_html_e( 'Documentation', 'modern-popup-checkout' ); ?></h3>
					<p>
						<a href="https://github.com/ghasemjj/modern-popup-checkout" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'View on GitHub', 'modern-popup-checkout' ); ?>
						</a>
					</p>
				</div>
			</div>

			<?php submit_button( __( 'Save Settings', 'modern-popup-checkout' ), 'primary', 'mppc_save_settings' ); ?>
		</form>
	</div>
</div>
