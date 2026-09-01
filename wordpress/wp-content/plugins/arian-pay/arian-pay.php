<?php
/**
 * Plugin Name: درگاه پرداخت آزمایشی آرین
 * Description: درگاه بانکی شبیه‌سازی‌شده برای تست خرید کاملاً آفلاین (پرداخت موفق/ناموفق) + توضیحات درگاه در پیشخوان ووکامرس.
 * Version: 1.0.0
 * Author: Arian Shop
 * Text Domain: arian-pay
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

define( 'ARIAN_PAY_VER', '1.0.0' );
define( 'ARIAN_PAY_DIR', plugin_dir_path( __FILE__ ) );
define( 'ARIAN_PAY_URL', plugin_dir_url( __FILE__ ) );

require_once ARIAN_PAY_DIR . 'inc/class-arian-gateway.php';
require_once ARIAN_PAY_DIR . 'inc/pay-page.php';

/**
 * ثبت درگاه در فهرست ووکامرس
 */
add_filter(
	'woocommerce_payment_gateways',
	static function ( $methods ) {
		$methods[] = 'Arian_Gateway';
		return $methods;
	}
);
