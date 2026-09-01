<?php
/**
 * صفحه ۴۰۴
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container notfound">
	<div class="nf-code">۴۰۴</div>
	<h1 class="page-title">صفحه‌ای که دنبالش بودید پیدا نشد!</h1>
	<p>شاید آدرس اشتباه باشد یا صفحه جابه‌جا شده باشد.</p>
	<div class="nf-actions">
		<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">بازگشت به خانه</a>
		<a class="btn btn-ghost" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>">مشاهده فروشگاه</a>
	</div>
</div>
<?php get_footer(); ?>
