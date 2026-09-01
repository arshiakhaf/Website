<?php
/**
 * ============================================================
 *  فاز ۱ راه‌اندازی فروشگاه آرین‌شاپ
 *  - نصب وردپرس (در صورت نیاز)
 *  - تنظیمات پایه
 *  - فعال‌سازی قالب و افزونه‌ها (ووکامرس)
 *  سپس فاز ۲ (seed.php) را برای داده‌های نمونه فراخوانی می‌کند.
 *
 *  اجرا:  php /arian/bin/provision.php [--force]
 * ============================================================
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );
ini_set( 'memory_limit', '512M' );

$WP_ROOT = '/var/www/html';
if ( ! is_dir( $WP_ROOT ) ) {
	fwrite( STDERR, "[arian] ریشه وردپرس پیدا نشد: {$WP_ROOT}\n" );
	exit( 1 );
}

define( 'ARIA_CLI', true );
define( 'WP_INSTALLING', true );
define( 'ABSPATH', $WP_ROOT . '/' );

$site_url = getenv( 'SITE_URL' ) ?: 'http://localhost:8080';
$_SERVER['HTTP_HOST']   = parse_url( $site_url, PHP_URL_HOST ) ?: 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];

$force = in_array( '--force', $argv, true );

/* ------------------------------------------------------------------ */
$hr = static function ( $msg = '' ) {
	echo "\n" . str_repeat( '=', 62 ) . "\n" . $msg . "\n" . str_repeat( '=', 62 ) . "\n";
};

try {
	$hr( "📦 مرحله ۱: بارگذاری وردپرس" );
	require ABSPATH . 'wp-load.php';

	/* ---------------- نصب وردپرس ---------------- */
	if ( ! is_blog_installed() ) {
		$hr( "⚙️ نصب وردپرس..." );
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$blog_title = 'فروشگاه آرین‌شاپ';
		$user       = getenv( 'WP_ADMIN_USER' ) ?: 'admin';
		$email      = getenv( 'WP_ADMIN_EMAIL' ) ?: 'admin@arian-shop.local';
		$pass       = getenv( 'WP_ADMIN_PASSWORD' ) ?: 'Admin@1234';

		$result = wp_install( $blog_title, $user, $email, true, '', $pass, '' );
		echo "[arian] وردپرس نصب شد. user_id={$result['user_id']}\n";
	} else {
		echo "[arian] وردپرس از قبل نصب شده است.\n";
	}

	/* ---------------- تنظیمات پایه ---------------- */
	update_option( 'blogname', 'فروشگاه آرین‌شاپ' );
	update_option( 'blogdescription', 'خرید آنلاین کالای دیجیتال، خانه و مد با ارسال سریع' );
	update_option( 'blog_public', '1' );
	update_option( 'date_format', 'j F Y' );
	update_option( 'time_format', 'H:i' );
	update_option( 'timezone_string', 'Asia/Tehran' );
	update_option( 'start_of_week', '6' ); // شنبه
	update_option( 'default_role', 'customer' );
	update_option( 'WPLANG', '' ); // زبان سایت فروشگاه: فارسی (پیشخوان به‌صورت ضمنی)
	update_option( 'users_can_register', '1' );

	/* ---------------- فعال‌سازی قالب و افزونه‌ها ---------------- */
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/post.php';
	require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';
	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

	$plugins = array(
		'arian-core/arian-core.php',
		'arian-pay/arian-pay.php',
		'woocommerce/woocommerce.php',
	);

	foreach ( $plugins as $plugin ) {
		if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
			fwrite( STDERR, "[arian] افزونه پیدا نشد: {$plugin}\n" );
			continue;
		}
		if ( is_plugin_active( $plugin ) ) {
			echo "[arian] افزونه فعال است: {$plugin}\n";
			continue;
		}
		$res = activate_plugin( $plugin );
		if ( is_wp_error( $res ) ) {
			throw new Exception( "فعال‌سازی {$plugin} ناموفق: " . $res->get_error_message() );
		}
		echo "[arian] افزونه فعال شد: {$plugin}\n";
	}

	/* اگر نصب ووکامرس به هر دلیلی کامل نشده باشد، دوباره فراخوانی می‌شود */
	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::install();
	}

	/* قالب */
	if ( wp_get_theme( 'arian-shop' )->exists() ) {
		switch_theme( 'arian-shop' );
		echo "[arian] قالب فعال شد: arian-shop\n";
	} else {
		echo "[arian] هشدار: قالب arian-shop پیدا نشد.\n";
	}

	/* ---------------- تنظیمات ووکامرس ---------------- */
	$hr( "🛒 تنظیمات ووکامرس" );

	$wc_opts = array(
		'woocommerce_currency'                  => 'IRT',
		'woocommerce_currency_pos'              => 'right_space',
		'woocommerce_price_thousand_sep'        => '٬',
		'woocommerce_price_decimal_sep'         => '/',
		'woocommerce_price_num_decimals'        => 0,
		'woocommerce_default_country'           => 'IR',
		'woocommerce_weight_unit'               => 'kg',
		'woocommerce_dimension_unit'            => 'cm',
		'woocommerce_enable_guest_checkout'     => 'yes',
		'woocommerce_enable_checkout_login_reminder' => 'no',
		'woocommerce_enable_signup_and_login_from_checkout' => 'no',
		'woocommerce_enable_myaccount_registration' => 'yes',
		'woocommerce_registration_generate_password' => 'no',
		'woocommerce_enable_coupons'            => 'yes',
		'woocommerce_calc_taxes'                => 'no',
		'woocommerce_enable_reviews'            => 'yes',
		'woocommerce_review_rating_verification_required' => 'no',
		'woocommerce_manage_stock'              => 'yes',
		'woocommerce_hold_stock_minutes'        => 60,
		'woocommerce_shop_page_id'              => '',
		'woocommerce_cart_page_id'              => '',
		'woocommerce_checkout_page_id'          => '',
		'woocommerce_myaccount_page_id'         => '',
		'woocommerce_terms_page_id'             => '',
		'woocommerce_product_style'             => '',
		'woocommerce_coming_soon'               => 'no',
		'woocommerce_allow_tracking'            => 'no',
	);
	foreach ( $wc_opts as $key => $value ) {
		update_option( $key, $value );
	}

	/* صفحات ووکامرس (فروشگاه / سبد / تسویه / حساب) */
	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::create_pages();

		/* فارسی‌سازی عنوان صفحات پیش‌فرض ووکامرس */
		$wc_pages = array(
			'woocommerce_shop_page_id'      => 'فروشگاه',
			'woocommerce_cart_page_id'      => 'سبد خرید',
			'woocommerce_checkout_page_id'  => 'تسویه حساب',
			'woocommerce_myaccount_page_id' => 'حساب کاربری',
		);
		foreach ( $wc_pages as $opt => $title ) {
			$pid = (int) get_option( $opt );
			if ( $pid ) {
				wp_update_post(
					array(
						'ID'         => $pid,
						'post_title' => $title,
					)
				);
			}
		}
	}

	/* ساخت صفحات اختصاصی سایت */
	$pages = array(
		'خانه'       => array( 'template' => '',          'slug' => 'home' ),
		'وبلاگ'      => array( 'template' => '',          'slug' => 'blog' ),
		'درباره ما'  => array( 'template' => '',          'slug' => 'about' ),
		'تماس با ما' => array( 'template' => '',          'slug' => 'contact', 'content' => '[arian_contact_form]' ),
		'قوانین و مقررات' => array( 'template' => '',     'slug' => 'terms', 'content' => '<h2>قوانین خرید</h2><p>تمام کالاها دارای ضمانت اصالت هستند. بازگشت کالا تا ۷ روز پس از تحویل امکان‌پذیر است.</p>' ),
		'حریم خصوصی' => array( 'template' => '',           'slug' => 'privacy', 'content' => '<h2>حریم خصوصی</h2><p>اطلاعات شما فقط برای پردازش سفارش استفاده می‌شود و نزد فروشگاه محفوظ است.</p>' ),
		'پیگیری سفارش' => array( 'template' => '',         'slug' => 'order-tracking', 'content' => '[woocommerce_order_tracking]' ),
		'پرداخت'     => array( 'template' => '',           'slug' => 'pay', 'content' => '[arian_pay]' ),
	);

	foreach ( $pages as $title => $cfg ) {
		$slug = sanitize_title( $cfg['slug'] );
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			$page_id = wp_insert_post(
				array(
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_content' => $cfg['content'] ?? '',
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);
			echo "[arian] صفحه ایجاد شد: {$title} (#{$page_id})\n";
		}
	}

	/* صفحه اصلی + وبلاگ */
	$home_id = get_page_by_path( 'home' ) ? get_page_by_path( 'home' )->ID : 0;
	$blog_id = get_page_by_path( 'blog' ) ? get_page_by_path( 'blog' )->ID : 0;
	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
	if ( $blog_id ) {
		update_option( 'page_for_posts', $blog_id );
	}

	/* آدرس‌های زیبا برای ووکامرس */
	update_option( 'permalink_structure', '/%postname%/' );
	$GLOBALS['wp_rewrite']->set_permalink_structure( '/%postname%/' );
	flush_rewrite_rules( true );
	echo "[arian] پیوندهای یکتا به‌روزرسانی شدند.\n";

	/* پرچم فاز ۱ */
	update_option( 'arian_installed', '1' );
	echo "[arian] فاز ۱ کامل شد. ✅\n\n";
} catch ( Throwable $e ) {
	fwrite( STDERR, "[arian] خطا در فاز ۱: {$e->getMessage()}\n{$e->getTraceAsString()}\n" );
	exit( 1 );
}

/* ------------------------------------------------------------------ */
/*  فاز ۲: داده‌های نمونه در یک فرآیند تازه (ووکامرس بارگذاری‌شده)      */
/* ------------------------------------------------------------------ */
$php = PHP_BINARY;
$cmd = escapeshellarg( $php ) . ' ' . escapeshellarg( __DIR__ . '/seed.php' ) . ( $force ? ' --force' : '' );
echo "[arian] اجرای فاز ۲ (داده‌های نمونه)...\n";
$output = array();
$rc     = 0;
exec( $cmd, $output, $rc );
echo implode( "\n", $output ) . "\n";

if ( $rc !== 0 ) {
	fwrite( STDERR, "[arian] خطا در فاز ۲ (کد {$rc}).\n" );
	exit( 1 );
}

echo "\n==============================================================\n";
echo "✅  راه‌اندازی کامل شد!\n";
echo "   سایت:       " . $site_url . "\n";
echo "   پیشخوان:    " . $site_url . "/wp-admin\n";
echo "   نام کاربری: " . getenv( 'WP_ADMIN_USER' ) . "\n";
echo "   رمز عبور:   " . getenv( 'WP_ADMIN_PASSWORD' ) . "\n";
echo "==============================================================\n";
exit( 0 );
