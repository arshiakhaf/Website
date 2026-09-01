<?php
/**
 * تنظیمات پایه قالب
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'arian-shop', ARIAN_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'custom-logo', array( 'height' => 90, 'width' => 300, 'flex-height' => true, 'flex-width' => true ) );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );

		// پشتیبانی کامل ووکامرس
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		// اندازه‌های تصویر
		add_image_size( 'arian-card', 600, 600, true );
		add_image_size( 'arian-hero', 1200, 700, true );

		// منوها
		register_nav_menus(
			array(
				'primary' => 'منوی اصلی',
				'footer'  => 'منوی پایین',
			)
		);
	},
	20
);

add_action(
	'widgets_init',
	static function () {
		register_sidebar(
			array(
				'name'          => 'سایدبار فروشگاه',
				'id'            => 'shop-sidebar',
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		for ( $i = 1; $i <= 4; $i++ ) {
			register_sidebar(
				array(
					/* translators: %d: شماره ستون */
					'name'          => sprintf( 'فوتر ستون %d', $i ),
					'id'            => 'footer-' . $i,
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h4 class="widget-title">',
					'after_title'   => '</h4>',
				)
			);
		}
	}
);
