<?php
/**
 * توابع کمکی قالب (آیکون‌ها، گزینه‌های سایت، ...)
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

/**
 * آیکون‌های SVG داخلی (بدون وابستگی به فونت‌آیکون خارجی)
 *
 * @param string $name نام آیکون.
 * @param string $class کلاس اضافی.
 * @return string
 */
function arian_icon( $name, $class = '' ) {
	$icons = array(
		'search'      => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/>',
		'cart'        => '<circle cx="9" cy="21" r="1.6"/><circle cx="19" cy="21" r="1.6"/><path d="M2.5 3h2l2.7 12.4a2 2 0 0 0 2 1.6h9.2a2 2 0 0 0 2-1.6L22 7H6"/>',
		'user'        => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
		'heart'       => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>',
		'eye'         => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
		'star'        => '<path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/>',
		'phone'       => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2z"/>',
		'mail'        => '<path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="M22 6l-10 7L2 6"/>',
		'pin'         => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
		'truck'       => '<path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
		'shield'      => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
		'headset'     => '<path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>',
		'tag'         => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><circle cx="7" cy="7" r="1.6"/>',
		'gift'        => '<path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7zM12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>',
		'check'       => '<path d="M20 6L9 17l-5-5"/>',
		'x'           => '<path d="M18 6L6 18M6 6l12 12"/>',
		'menu'        => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'chev-down'   => '<path d="M6 9l6 6 6-6"/>',
		'chev-left'   => '<path d="M15 18l-6-6 6-6"/>',
		'arrow-left'  => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
		'arrow-right' => '<path d="M5 12h14M12 5l7 7-7 7"/>',
		'plus'        => '<path d="M12 5v14M5 12h14"/>',
		'minus'       => '<path d="M5 12h14"/>',
		'credit'      => '<rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/>',
		'refresh'     => '<path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.5 9a9 9 0 0 1 14.9-3.4L23 10M1 14l4.6 4.4A9 9 0 0 0 20.5 15"/>',
		'box'         => '<path d="M21 8v8a2 2 0 0 1-1 1.7l-7 4a2 2 0 0 1-2 0l-7-4A2 2 0 0 1 3 16V8a2 2 0 0 1 1-1.7l7-4a2 2 0 0 1 2 0l7 4a2 2 0 0 1 1 1.7z"/><path d="M3.3 7L12 12l8.7-5M12 22V12"/>',
		'clock'       => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'flame'       => '<path d="M12 2s-6 5-6 11a6 6 0 0 0 12 0c0-2-.6-3.8-1.7-5.4C15.6 9.5 14 8 14 8s-.5 2.5-2 3.5C11 10 12 8 12 6.5 12 5 12 3 12 2z"/>',
		'sparkle'     => '<path d="M12 3l1.9 5.8L20 10.7l-6.1 1.9L12 18.5l-1.9-5.9L4 10.7l6.1-1.9z"/><path d="M19 2l.7 2.1L22 5l-2.3.9L19 8l-.7-2.1L16 5l2.3-.9z"/>',
		'instagram'   => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/>',
		'telegram'    => '<path d="M22 3L2 10.5l6 2.4L9.5 20l3.6-4.5 5.4 3.9z"/><path d="M8 12.9L22 3"/>',
		'whatsapp'    => '<path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 21l2.2-5.4A8.5 8.5 0 1 1 21 11.5z"/><path d="M9 10c.5 2.5 2.5 4.5 5 5l1.5-1.5-2-1.5-1 .5c-.7-.4-1.6-1.3-2-2l.5-1-1.5-2z"/>',
	);

	$path = isset( $icons[ $name ] ) ? $icons[ $name ] : $icons['sparkle'];

	return '<svg class="arian-icon ' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}

/**
 * مقدار گزینه قالب با پیش‌فرض
 *
 * @param string $key  کلید.
 * @param mixed  $def  مقدار پیش‌فرض.
 * @return mixed
 */
function arian_opt( $key, $def = '' ) {
	return get_theme_mod( $key, $def );
}

/**
 * اطلاعات تماس پیش‌فرض فروشگاه
 *
 * @return array
 */
function arian_contact_data() {
	return array(
		'phone'     => arian_opt( 'arian_phone', '021-91001234' ),
		'mobile'    => arian_opt( 'arian_mobile', '0912-345-6789' ),
		'email'     => arian_opt( 'arian_email', 'info@arian-shop.local' ),
		'address'   => arian_opt( 'arian_address', 'تهران، خیابان ولیعصر، مجتمع آرین‌شاپ، طبقه همکف' ),
		'instagram' => arian_opt( 'arian_instagram', 'https://instagram.com/arian.shop' ),
		'telegram'  => arian_opt( 'arian_telegram', 'https://t.me/arian_shop' ),
		'whatsapp'  => arian_opt( 'arian_whatsapp', '989123456789' ),
	);
}

/**
 * دسترسی به فونت/استایل قالب
 *
 * @param string $path مسیر نسبی.
 * @return string
 */
function arian_asset( $path ) {
	return ARIAN_URI . '/' . ltrim( $path, '/' );
}

/**
 * تبدیل اعداد انگلیسی به فارسی (در سرور — برای موارد خاص)
 *
 * @param string $text متن.
 * @return string
 */
function arian_fa_digits( $text ) {
	$en = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$fa = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
	return str_replace( $en, $fa, (string) $text );
}

/**
 * عنوان صفحه (نوشته‌ها و بایگانی)
 *
 * @param string $title    عنوان.
 * @param string $eyebrow  زیرعنوان کوچک.
 * @return void
 */
function arian_page_header( $title = '', $eyebrow = '' ) {
	if ( ! $title ) {
		$title = get_the_archive_title();
	}
	echo '<div class="page-head archive-head">';
	if ( $eyebrow ) {
		echo '<span class="archive-eyebrow">' . esc_html( $eyebrow ) . '</span>';
	}
	echo '<h1 class="page-title">' . wp_kses_post( $title ) . '</h1>';
	echo '</div>';
}

/**
 * برچسب تخفیف یک محصول
 *
 * @param WC_Product|null $product محصول.
 * @return string
 */
function arian_discount_badge( $product = null ) {
	if ( ! $product ) {
		return '';
	}
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
		$percent = round( ( ( $regular - $sale ) / $regular ) * 100 );
		return '<span class="badge badge-sale">' . arian_fa_digits( $percent ) . '٪ تخفیف</span>';
	}
	if ( $product->is_featured() ) {
		return '<span class="badge badge-featured">پیشنهاد آرین‌شاپ</span>';
	}
	return '';
}
