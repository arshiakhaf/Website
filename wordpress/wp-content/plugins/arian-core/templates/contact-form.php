<?php
/**
 * فرم تماس با ما
 *
 * @package arian-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$arian_sent  = isset( $_GET['sent'] ) ? absint( $_GET['sent'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
$arian_name  = is_user_logged_in() ? wp_get_current_user()->display_name : '';
$arian_email = is_user_logged_in() ? wp_get_current_user()->user_email : '';
?>
<div class="contact-wrap">
	<?php if ( 1 === $arian_sent ) : ?>
		<div class="woocommerce-message">پیام شما با موفقیت ثبت شد؛ به‌زودی پاسخ می‌دهیم. ✅</div>
	<?php elseif ( $arian_sent > 1 ) : ?>
		<div class="woocommerce-error">لطفاً همه فیلدهای ضروری را کامل کنید.</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="contact-form">
		<input type="hidden" name="action" value="arian_contact">
		<?php wp_nonce_field( 'arian_contact', 'arian_contact_nonce' ); ?>
		<p class="hp-field" style="position:absolute;right:-9999px;" aria-hidden="true">
			<label>وب‌سایت <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
		</p>

		<div class="form-row">
			<label for="arian-name">نام و نام خانوادگی <span class="required">*</span></label>
			<input type="text" id="arian-name" name="name" value="<?php echo esc_attr( $arian_name ); ?>" required>
		</div>
		<div class="form-row">
			<label for="arian-email">ایمیل <span class="required">*</span></label>
			<input type="email" id="arian-email" name="email" value="<?php echo esc_attr( $arian_email ); ?>" required>
		</div>
		<div class="form-row">
			<label for="arian-phone">شماره تماس</label>
			<input type="tel" id="arian-phone" name="phone" placeholder="مثلاً 0912xxxxxxx">
		</div>
		<div class="form-row">
			<label for="arian-subject">موضوع</label>
			<input type="text" id="arian-subject" name="subject" placeholder="مثلاً پیگیری سفارش">
		</div>
		<div class="form-row">
			<label for="arian-message">متن پیام <span class="required">*</span></label>
			<textarea id="arian-message" name="message" rows="5" required></textarea>
		</div>
		<button type="submit" class="button">ارسال پیام <?php echo esc_html( '←' ); ?></button>
	</form>
</div>
