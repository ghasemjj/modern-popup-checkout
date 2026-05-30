<?php
/**
 * Frontend popup modal template
 *
 * @package Modern_Popup_Checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="mppc-modal" class="mppc-modal">
	<div class="mppc-modal-content">
		<!-- Header -->
		<div class="mppc-modal-header">
			<h2 id="mppc-modal-title">تسویه حساب</h2>
			<button type="button" class="mppc-modal-close" id="mppc-close-modal">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<line x1="18" y1="6" x2="6" y2="18"></line>
					<line x1="6" y1="6" x2="18" y2="18"></line>
				</svg>
			</button>
		</div>

		<!-- Progress Bar -->
		<div id="mppc-progress-container" class="mppc-progress-container">
			<div class="mppc-progress-bar">
				<div id="mppc-progress" class="mppc-progress-fill" style="width: 25%"></div>
			</div>
			<div class="mppc-progress-steps">
				<div class="mppc-progress-step active" data-step="1">
					<span class="mppc-step-number">1</span>
					<span class="mppc-step-label">اطلاعات</span>
				</div>
				<div class="mppc-progress-step" data-step="2">
					<span class="mppc-step-number">2</span>
					<span class="mppc-step-label">آدرس</span>
				</div>
				<div class="mppc-progress-step" data-step="3">
					<span class="mppc-step-number">3</span>
					<span class="mppc-step-label">پرداخت</span>
				</div>
				<div class="mppc-progress-step" data-step="4">
					<span class="mppc-step-number">4</span>
					<span class="mppc-step-label">تکمیل</span>
				</div>
			</div>
		</div>

		<!-- Body -->
		<div class="mppc-modal-body">
			<!-- Step 1: Personal Information -->
			<div id="mppc-step-1" class="mppc-step-content active">
				<form id="mppc-form-step1" class="mppc-form">
					<div class="mppc-form-group">
						<label for="mppc-firstName">نام *</label>
						<input type="text" id="mppc-firstName" name="firstName" placeholder="نام خود را وارد کنید" required>
						<span class="mppc-error-message" id="mppc-firstName-error"></span>
					</div>

					<div class="mppc-form-group">
						<label for="mppc-lastName">نام خانوادگی *</label>
						<input type="text" id="mppc-lastName" name="lastName" placeholder="نام خانوادگی خود را وارد کنید" required>
						<span class="mppc-error-message" id="mppc-lastName-error"></span>
					</div>

					<div class="mppc-form-group">
						<label for="mppc-phone">شماره موبایل *</label>
						<input type="tel" id="mppc-phone" name="phone" placeholder="09xxxxxxxxx" required>
						<span class="mppc-error-message" id="mppc-phone-error"></span>
					</div>
				</form>
			</div>

			<!-- Step 2: Shipping Address -->
			<div id="mppc-step-2" class="mppc-step-content">
				<form id="mppc-form-step2" class="mppc-form">
					<div class="mppc-form-row">
						<div class="mppc-form-group">
							<label for="mppc-province">استان *</label>
							<select id="mppc-province" name="province" required>
								<option value="">انتخاب کنید</option>
							</select>
							<span class="mppc-error-message" id="mppc-province-error"></span>
						</div>

						<div class="mppc-form-group">
							<label for="mppc-city">شهرستان / شهر *</label>
							<select id="mppc-city" name="city" required>
								<option value="">انتخاب کنید</option>
							</select>
							<span class="mppc-error-message" id="mppc-city-error"></span>
						</div>
					</div>

					<div class="mppc-form-group">
						<label for="mppc-street">خیابان *</label>
						<input type="text" id="mppc-street" name="street" placeholder="نام خیابان" required>
						<span class="mppc-error-message" id="mppc-street-error"></span>
					</div>

					<div class="mppc-form-row">
						<div class="mppc-form-group">
							<label for="mppc-buildingNumber">پلاک</label>
							<input type="text" id="mppc-buildingNumber" name="buildingNumber" placeholder="پلاک">
						</div>

						<div class="mppc-form-group">
							<label for="mppc-unit">واحد</label>
							<input type="text" id="mppc-unit" name="unit" placeholder="واحد">
						</div>
					</div>

					<div class="mppc-form-group">
						<label for="mppc-postalCode">کد پستی *</label>
						<input type="text" id="mppc-postalCode" name="postalCode" placeholder="10 رقم" maxlength="10" required>
						<span class="mppc-error-message" id="mppc-postalCode-error"></span>
					</div>

					<div class="mppc-form-group">
						<label for="mppc-shippingMethod">روش ارسال *</label>
						<select id="mppc-shippingMethod" name="shippingMethod" required>
							<option value="">انتخاب کنید</option>
						</select>
						<span class="mppc-error-message" id="mppc-shippingMethod-error"></span>
					</div>
				</form>
			</div>

			<!-- Step 3: Payment -->
			<div id="mppc-step-3" class="mppc-step-content">
				<div class="mppc-payment-container">
					<div class="mppc-order-summary">
						<h3>خلاصه سفارش</h3>
						<div class="mppc-summary-item">
							<span>تعداد محصولات:</span>
							<span id="mppc-product-count">0</span>
						</div>
						<div class="mppc-summary-item">
							<span>جمع فروش:</span>
							<span id="mppc-subtotal">0</span>
						</div>
						<div class="mppc-summary-item">
							<span>هزینه ارسال:</span>
							<span id="mppc-shipping-cost">0</span>
						</div>
						<div class="mppc-summary-item">
							<span>تخفیف:</span>
							<span id="mppc-discount" class="mppc-discount-value">0</span>
						</div>
						<div class="mppc-summary-divider"></div>
						<div class="mppc-summary-total">
							<span>مبلغ نهایی:</span>
							<span id="mppc-total">0</span>
						</div>
					</div>

					<div class="mppc-coupon-section">
						<label>کد تخفیف</label>
						<div class="mppc-coupon-input-group">
							<input type="text" id="mppc-coupon-code" placeholder="کد تخفیف را وارد کنید">
							<button type="button" id="mppc-apply-coupon" class="mppc-btn-apply">اعمال</button>
						</div>
						<span class="mppc-error-message" id="mppc-coupon-error"></span>
						<span class="mppc-success-message" id="mppc-coupon-success"></span>
					</div>

					<form id="mppc-form-step3" class="mppc-form">
						<div class="mppc-form-group">
							<label for="mppc-paymentMethod">روش پرداخت *</label>
							<select id="mppc-paymentMethod" name="paymentMethod" required>
								<option value="">انتخاب کنید</option>
							</select>
							<span class="mppc-error-message" id="mppc-paymentMethod-error"></span>
						</div>
					</form>
				</div>
			</div>

			<!-- Step 4: Order Complete -->
			<div id="mppc-step-4" class="mppc-step-content">
				<div class="mppc-success-container">
					<div class="mppc-success-icon">
						<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
					</div>
					<h3 id="mppc-success-message">از خرید شما متشکریم ❤️</h3>
					<p id="mppc-success-description">سفارش شما با موفقیت ثبت شد و به زودی پردازش خواهد شد.</p>
					<div class="mppc-order-info">
						<div class="mppc-info-item">
							<span class="mppc-info-label">شماره سفارش:</span>
							<span class="mppc-info-value" id="mppc-order-number">-</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Footer -->
		<div class="mppc-modal-footer">
			<button type="button" id="mppc-btn-back" class="mppc-btn mppc-btn-secondary" style="display: none;">بازگشت</button>
			<button type="button" id="mppc-btn-continue" class="mppc-btn mppc-btn-primary">ادامه</button>
			<button type="button" id="mppc-btn-back-shop" class="mppc-btn mppc-btn-secondary" style="display: none;">بازگشت به فروشگاه</button>
			<a href="#" id="mppc-btn-view-order" class="mppc-btn mppc-btn-primary" style="display: none; text-decoration: none;">مشاهده سفارش</a>
		</div>
	</div>
</div>

<!-- Overlay -->
<div id="mppc-overlay" class="mppc-overlay"></div>
