<?php
/**
 * فوتر سایت
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

$arian_contact = arian_contact_data();
$arian_wc      = function_exists( 'WC' );
$arian_shop_url  = $arian_wc ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
</main>

<footer class="site-footer">
	<div class="footer-main">
		<div class="container footer-grid">

			<div class="footer-col footer-about">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link footer-logo">
					<span class="logo-mark" aria-hidden="true">آ</span>
					<span class="logo-text">آرین<span>‌شاپ</span></span>
				</a>
				<p><?php echo esc_html( arian_opt( 'arian_footer_about', 'آرین‌شاپ؛ فروشگاه اینترنتی کالاهای دیجیتال، خانه و سبک زندگی. هفت روز هفته، ۲۴ ساعته در کنار شما هستیم.' ) ); ?></p>
				<div class="socials">
					<a href="<?php echo esc_url( $arian_contact['instagram'] ); ?>" aria-label="اینستاگرام"><?php echo arian_icon( 'instagram' ); ?></a>
					<a href="<?php echo esc_url( $arian_contact['telegram'] ); ?>" aria-label="تلگرام"><?php echo arian_icon( 'telegram' ); ?></a>
					<a href="https://wa.me/<?php echo esc_attr( $arian_contact['whatsapp'] ); ?>" aria-label="واتساپ"><?php echo arian_icon( 'whatsapp' ); ?></a>
					<a href="<?php echo esc_url( 'mailto:' . $arian_contact['email'] ); ?>" aria-label="ایمیل"><?php echo arian_icon( 'mail' ); ?></a>
				</div>
			</div>

			<div class="footer-col">
				<h4 class="footer-title">دسترسی سریع</h4>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-links',
						'fallback_cb'    => false,
					)
				);
				if ( ! has_nav_menu( 'footer' ) ) {
					echo '<ul class="footer-links">';
					echo '<li><a href="' . esc_url( $arian_shop_url ) . '">فروشگاه</a></li>';
					echo '<li><a href="' . esc_url( home_url( '/order-tracking/' ) ) . '">پیگیری سفارش</a></li>';
					echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">درباره ما</a></li>';
					echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">تماس با ما</a></li>';
					echo '</ul>';
				}
				?>
			</div>

			<div class="footer-col">
				<h4 class="footer-title">خدمات مشتریان</h4>
				<ul class="footer-links">
					<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">قوانین و مقررات</a></li>
					<li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>">حریم خصوصی</a></li>
					<li><a href="<?php echo esc_url( home_url( '/order-tracking/' ) ); ?>">پیگیری سفارش</a></li>
					<li><a href="<?php echo esc_url( $arian_wc ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>">حساب کاربری</a></li>
				</ul>
			</div>

			<div class="footer-col">
				<h4 class="footer-title">اطلاعات تماس</h4>
				<ul class="footer-contact">
					<li><?php echo arian_icon( 'pin' ); ?><span><?php echo esc_html( $arian_contact['address'] ); ?></span></li>
					<li><?php echo arian_icon( 'phone' ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $arian_contact['phone'] ) ); ?>"><?php echo esc_html( $arian_contact['phone'] ); ?></a></li>
					<li><?php echo arian_icon( 'mail' ); ?><a href="mailto:<?php echo esc_attr( $arian_contact['email'] ); ?>"><?php echo esc_html( $arian_contact['email'] ); ?></a></li>
					<li><?php echo arian_icon( 'clock' ); ?><span>شنبه تا پنجشنبه، ۹ تا ۲۱</span></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="footer-trust">
		<div class="container trust-inner">
			<div class="trust-item"><?php echo arian_icon( 'shield' ); ?><div><strong>ضمانت اصالت</strong><span>کالای ۱۰۰٪ اورجینال</span></div></div>
			<div class="trust-item"><?php echo arian_icon( 'refresh' ); ?><div><strong>۷ روز بازگشت</strong><span>ضمانت بازگشت کالا</span></div></div>
			<div class="trust-item"><?php echo arian_icon( 'credit' ); ?><div><strong>پرداخت امن</strong><span>درگاه بانکی مطمئن</span></div></div>
			<div class="trust-item"><?php echo arian_icon( 'truck' ); ?><div><strong>ارسال سریع</strong><span>به سراسر کشور</span></div></div>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container footer-bottom-inner">
			<p><?php echo esc_html( arian_opt( 'arian_copyright', 'کلیه حقوق این وب‌سایت متعلق به فروشگاه آرین‌شاپ است.' ) ); ?></p>
			<div class="pay-badges" aria-label="روش‌های پرداخت">
				<a href="#"><?php echo arian_icon( 'credit' ); ?> پرداخت آنلاین</a>
				<a href="#"><?php echo arian_icon( 'box' ); ?> پرداخت در محل</a>
				<a href="#"><?php echo arian_icon( 'truck' ); ?> حواله بانکی</a>
			</div>
		</div>
	</div>
</footer>

<a href="https://wa.me/<?php echo esc_attr( $arian_contact['whatsapp'] ); ?>" class="float-whatsapp" aria-label="پشتیبانی واتساپ" target="_blank" rel="noopener">
	<?php echo arian_icon( 'whatsapp' ); ?>
</a>
<button class="to-top" data-to-top aria-label="بازگشت به بالا"><?php echo arian_icon( 'chev-left' ); ?></button>

<!-- پیش‌نمایش سریع محصول -->
<div class="qv-modal" data-qv-modal hidden>
	<div class="qv-backdrop" data-qv-close></div>
	<div class="qv-panel" role="dialog" aria-modal="true">
		<button class="qv-close" data-qv-close aria-label="بستن"><?php echo arian_icon( 'x' ); ?></button>
		<div class="qv-content" data-qv-content></div>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
