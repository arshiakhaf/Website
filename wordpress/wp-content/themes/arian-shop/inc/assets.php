<?php
/**
 * بارگذاری استایل و اسکریپت
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	static function () {
		$css_ver = file_exists( ARIAN_DIR . '/assets/css/main.css' ) ? filemtime( ARIAN_DIR . '/assets/css/main.css' ) : ARIAN_VERSION;
		$js_ver  = file_exists( ARIAN_DIR . '/assets/js/main.js' ) ? filemtime( ARIAN_DIR . '/assets/js/main.js' ) : ARIAN_VERSION;

		wp_enqueue_style( 'arian-main', ARIAN_URI . '/assets/css/main.css', array(), $css_ver );
		wp_enqueue_script( 'arian-main', ARIAN_URI . '/assets/js/main.js', array(), $js_ver, true );
		wp_enqueue_style( 'dashicons' );

		wp_localize_script(
			'arian-main',
			'arianData',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'homeUrl'  => home_url( '/' ),
				'nonce'    => wp_create_nonce( 'arian_nonce' ),
				'cartUrl'  => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ),
				'isRtl'    => is_rtl() ? '1' : '0',
			)
		);

		// استایل‌های اضافه‌شده توسط ووکامرس را نگه می‌داریم اما موارد مزاحم را حذف می‌کنیم
		wp_dequeue_style( 'wc-blocks-style' );
	},
	20
);
