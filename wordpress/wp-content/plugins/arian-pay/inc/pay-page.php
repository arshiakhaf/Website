<?php
/**
 * صفحه درگاه آزمایشی
 *
 * @package arian-pay
 */

defined( 'ABSPATH' ) || exit;

/* ---------------- پردازش نتیجه پرداخت ---------------- */
add_action(
	'template_redirect',
	static function () {
		if ( ! isset( $_GET['arian-result'] ) || ! isset( $_GET['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		$result = sanitize_key( $_GET['arian-result'] ); // phpcs:ignore WordPress.Security.NonceVerification
		$key    = sanitize_text_field( wp_unslash( $_GET['key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

		if ( ! function_exists( 'wc_get_order_id_by_order_key' ) ) {
			return;
		}

		$order_id = wc_get_order_id_by_order_key( $key );
		$order    = $order_id ? wc_get_order( $order_id ) : null;

		if ( ! $order ) {
			wp_safe_redirect( home_url( '/' ) );
			exit;
		}

		if ( 'success' === $result ) {
			if ( $order->has_status( array( 'pending', 'on-hold' ) ) ) {
				$order->payment_complete();
				$order->add_order_note( 'پرداخت آزمایشی با موفقیت انجام شد. (درگاه شبیه‌سازی‌شده)' );
			}
			wp_safe_redirect( $order->get_checkout_order_received_url() );
			exit;
		}

		if ( 'fail' === $result ) {
			if ( $order->has_status( array( 'pending', 'on-hold' ) ) ) {
				$order->update_status( 'failed', 'پرداخت آزمایشی ناموفق بود. (ردشده توسط کاربر)' );
			}
			// بازگشت به صفحه پرداخت برای انتخاب روش دیگر
			wp_safe_redirect( $order->get_checkout_payment_url() . '&payment-failed=1' );
			exit;
		}
	}
);

/**
 * [arian_pay]
 */
add_shortcode(
	'arian_pay',
	static function () {
		if ( ! function_exists( 'wc_get_order_id_by_order_key' ) ) {
			return '<div class="arian-pay-page"><p>ووکامرس فعال نیست.</p></div>';
		}

		$key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! $key ) {
			return '<div class="arian-pay-page">'
				. '<p>درگاه پرداخت به درستی باز نشده است. لطفاً از سبد خرید، سفارش جدید ثبت کنید.</p>'
				. '<a class="button" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">بازگشت به فروشگاه</a>'
				. '</div>';
		}

		$order_id = wc_get_order_id_by_order_key( $key );
		$order    = $order_id ? wc_get_order( $order_id ) : null;

		if ( ! $order ) {
			return '<div class="arian-pay-page"><p>سفارش پیدا نشد.</p><a class="button" href="' . esc_url( home_url( '/' ) ) . '">بازگشت</a></div>';
		}

		if ( $order->is_paid() ) {
			return '<div class="arian-pay-page">'
				. '<div class="pay-logo">' . arian_icon_safe() . '</div>'
				. '<h2>این سفارش قبلاً پرداخت شده است</h2>'
				. '<a class="button" href="' . esc_url( $order->get_checkout_order_received_url() ) . '">مشاهده جزئیات سفارش</a>'
				. '</div>';
		}

		$amount  = $order->get_total();
		$pay_url = home_url( '/pay/' );

		ob_start();
		?>
		<div class="arian-pay-page">
			<div class="pay-logo" aria-hidden="true"><?php echo arian_icon_safe(); ?></div>
			<h2>درگاه پرداخت آزمایشی</h2>
			<p class="pay-order">
				سفارش <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
				— <?php echo esc_html( $order->get_billing_first_name() ); ?>
			</p>
			<div class="pay-amount"><?php echo wp_kses_post( wc_price( $amount ) ); ?></div>
			<p style="color:#8a8fa8;font-size:13px;">
				این یک درگاه شبیه‌سازی‌شده است؛ برای تست، یکی از دو گزینه را انتخاب کنید.
			</p>
			<div class="pay-buttons">
				<a class="btn pay-ok" href="<?php echo esc_url( add_query_arg( array( 'arian-result' => 'success', 'key' => $order->get_order_key() ), $pay_url ) ); ?>">
					✓ پرداخت موفق شد
				</a>
				<a class="btn pay-fail" href="<?php echo esc_url( add_query_arg( array( 'arian-result' => 'fail', 'key' => $order->get_order_key() ), $pay_url ) ); ?>">
					✗ پرداخت ناموفق (تست)
				</a>
			</div>
			<p style="margin-top:22px;font-size:12.5px;color:#8a8fa8;">
				در محیط واقعی، این صفحه به درگاه رسمی بانکی (زرین‌پال / زیبال) متصل می‌شود.
			</p>
		</div>
		<?php
		return ob_get_clean();
	}
);

/**
 * آیکون امن صفحه پرداخت
 *
 * @return string
 */
function arian_icon_safe() {
	return '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>';
}
