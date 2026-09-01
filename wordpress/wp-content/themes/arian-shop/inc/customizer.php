<?php
/**
 * گزینه‌های سفارشی‌سازی قالب (شخصی‌سازی)
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	static function ( $wp_customize ) {

		/* ---------------- تماس و شبکه‌های اجتماعی ---------------- */
		$wp_customize->add_section(
			'arian_contact',
			array(
				'title'    => 'تماس و شبکه‌های اجتماعی',
				'priority' => 20,
			)
		);

		$fields = array(
			'arian_phone'     => array( 'label' => 'تلفن فروشگاه', 'default' => '021-91001234' ),
			'arian_mobile'    => array( 'label' => 'موبایل (واتساپ)', 'default' => '0912-345-6789' ),
			'arian_email'     => array( 'label' => 'ایمیل', 'default' => 'info@arian-shop.local' ),
			'arian_address'   => array( 'label' => 'آدرس', 'default' => 'تهران، خیابان ولیعصر، مجتمع آرین‌شاپ، طبقه همکف' ),
			'arian_instagram' => array( 'label' => 'آدرس اینستاگرام', 'default' => 'https://instagram.com/arian.shop' ),
			'arian_telegram'  => array( 'label' => 'آدرس تلگرام', 'default' => 'https://t.me/arian_shop' ),
			'arian_whatsapp'  => array( 'label' => 'شماره واتساپ (با کد کشور)', 'default' => '989123456789' ),
		);

		foreach ( $fields as $key => $f ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => $f['default'],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				$key,
				array(
					'label'   => $f['label'],
					'section' => 'arian_contact',
					'type'    => 'text',
				)
			);
		}

		/* ---------------- صفحه اصلی ---------------- */
		$wp_customize->add_section(
			'arian_home',
			array(
				'title'    => 'صفحه اصلی فروشگاه',
				'priority' => 21,
			)
		);

		$home_fields = array(
			'arian_hero_title'    => array( 'label' => 'تیتر بنر اصلی', 'default' => 'خرید هوشمند، با یک کلیک' ),
			'arian_hero_subtitle' => array( 'label' => 'زیرتیتر بنر اصلی', 'default' => 'جدیدترین کالاهای دیجیتال و خانگی با گارانتی اصالت و ارسال سریع' ),
			'arian_hero_btn'      => array( 'label' => 'متن دکمه بنر', 'default' => 'مشاهده فروشگاه' ),
			'arian_hero_btn_url'  => array( 'label' => 'آدرس دکمه بنر', 'default' => '/shop/' ),
			'arian_hero_badge'    => array( 'label' => 'نشان بنر (مثلاً: تخفیف ویژه پاییزی)', 'default' => '🔥 تخفیف ویژه پاییزی تا ۴۰٪' ),
			'arian_footer_about'  => array( 'label' => 'متن معرفی فوتر', 'default' => 'آرین‌شاپ؛ فروشگاه اینترنتی کالاهای دیجیتال، خانه و سبک زندگی. هفت روز هفته، ۲۴ ساعته در کنار شما هستیم.' ),
			'arian_copyright'     => array( 'label' => 'متن کپی‌رایت', 'default' => 'کلیه حقوق این وب‌سایت متعلق به فروشگاه آرین‌شاپ است.' ),
		);

		foreach ( $home_fields as $key => $f ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => $f['default'],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				$key,
				array(
					'label'   => $f['label'],
					'section' => 'arian_home',
					'type'    => 'text',
				)
			);
		}

		/* تصویر بنر اصلی */
		$wp_customize->add_setting(
			'arian_hero_image',
			array(
				'default'           => '',
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'arian_hero_image',
				array(
					'label'     => 'تصویر بنر اصلی',
					'section'   => 'arian_home',
					'mime_type' => 'image',
				)
			)
		);

		/* پیام خبرنامه */
		$wp_customize->add_setting(
			'arian_newsletter_text',
			array(
				'default'           => 'از تخفیف‌ها زودتر باخبر شوید؛ خبرنامه آرین‌شاپ را دنبال کنید.',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'arian_newsletter_text',
			array(
				'label'   => 'متن خبرنامه',
				'section' => 'arian_home',
				'type'    => 'text',
			)
		);
	}
);
