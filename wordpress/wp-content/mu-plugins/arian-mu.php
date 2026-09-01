<?php
/**
 * Plugin Name: آرین‌شاپ — هسته امن و آفلاین
 * Description: سخت‌سازی پایه، جلوگیری از تماس با اینترنت در حالت آفلاین و حذف موارد غیرضروری.
 * Version: 1.0.0
 * Author: Arian Shop
 * Text Domain: arian-mu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------- حالت آفلاین: قطع کامل خروجی ---------------- */
add_filter(
	'pre_http_request',
	static function ( $preempt, $args, $url ) {
		if ( defined( 'ARIAN_OFFLINE' ) && ARIAN_OFFLINE ) {
			return new WP_Error(
				'arian_offline',
				'این سایت در حالت آفلاین اجرا می‌شود و به اینترنت دسترسی ندارد.'
			);
		}
		return $preempt;
	},
	10,
	3
);

/* ---------------- هدرهای امنیتی ---------------- */
add_action(
	'send_headers',
	static function () {
		if ( headers_sent() ) {
			return;
		}
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
	}
);

/* ---------------- غیرفعال‌سازی موارد غیرضروری ---------------- */

// XML-RPC (امنیت)
add_filter( 'xmlrpc_enabled', '__return_false' );

// پنهان‌سازی نسخه وردپرس
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_generator' );

// ایموجی‌های راه دور (افزایش سرعت)
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// جلوگیری از شمارش کاربران از طریق REST
add_filter(
	'rest_endpoints',
	static function ( $endpoints ) {
		if ( isset( $endpoints['/wp/v2/users'] ) ) {
			unset( $endpoints['/wp/v2/users'] );
		}
		return $endpoints;
	}
);

// جلوگیری از ثبت‌نام خودکار در REST
add_filter(
	'rest_authentication_errors',
	static function ( $result ) {
		if ( ! empty( $result ) ) {
			return $result;
		}
		return null;
	}
);

// غیرفعال‌سازی Google Fonts و منابع خارجی قالب‌های پیش‌فرض
add_filter(
	'wp_resource_hints',
	static function ( $urls ) {
		return array();
	},
	100
);
