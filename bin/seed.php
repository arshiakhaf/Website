<?php
/**
 * ============================================================
 *  فاز ۲ راه‌اندازی فروشگاه آرین‌شاپ — داده‌های نمونه
 *  در این فرآیند ووکامرس به‌طور کامل بارگذاری شده است.
 *
 *  اجرا:  php /arian/bin/seed.php [--force]
 * ============================================================
 */

define( 'ARIA_CLI', true );
require '/var/www/html/wp-load.php';

if ( ! is_blog_installed() ) {
	fwrite( STDERR, "وردپرس نصب نشده است؛ ابتدا provision.php را اجرا کنید.\n" );
	exit( 1 );
}

$force = in_array( '--force', $argv, true );

if ( get_option( 'arian_seeded' ) === '1' && ! $force ) {
	echo "[seed] داده‌های نمونه از قبل موجود است. (برای بازسازی: --force)\n";
	exit( 0 );
}

if ( ! function_exists( 'WC' ) ) {
	fwrite( STDERR, "ووکامرس بارگذاری نشده است.\n" );
	exit( 1 );
}

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );
ini_set( 'memory_limit', '512M' );
set_time_limit( 600 );

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/post.php';
require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
require_once ABSPATH . 'wp-admin/includes/user.php';
require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

$log = static function ( $msg ) {
	echo "[seed] {$msg}\n";
};

/* ------------------------------------------------------------------ */
/*  ابزارهای کمکی                                                      */
/* ------------------------------------------------------------------ */

/**
 * پیوست‌کردن تصویر از مسیر داخلی (با کش درون‌فرآیندی).
 *
 * @var array $image_cache
 */
$GLOBALS['ari_img_cache'] = array();

function arian_attach_image( $file ) {
	if ( ! file_exists( $file ) ) {
		return 0;
	}
	$name = basename( $file );
	if ( isset( $GLOBALS['ari_img_cache'][ $name ] ) ) {
		return $GLOBALS['ari_img_cache'][ $name ];
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
			'meta_key'       => '_ari_demo_file',
			'meta_value'     => $name,
			'fields'         => 'ids',
		)
	);
	if ( $existing ) {
		$GLOBALS['ari_img_cache'][ $name ] = $existing[0];
		return $existing[0];
	}

	$upload = wp_upload_bits( $name, null, file_get_contents( $file ) );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$att_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $name ),
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( $att_id ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		$meta = wp_generate_attachment_metadata( $att_id, $upload['file'] );
		wp_update_attachment_metadata( $att_id, $meta );
		update_post_meta( $att_id, '_ari_demo_file', $name );
	}

	$GLOBALS['ari_img_cache'][ $name ] = $att_id;
	return $att_id;
}

function arian_get_or_create_term( $taxonomy, $name, $args = array() ) {
	$term = term_exists( $name, $taxonomy );
	if ( ! $term ) {
		$term = wp_insert_term( $name, $taxonomy, $args );
	}
	return is_wp_error( $term ) ? false : (int) $term['term_id'];
}

/* ------------------------------------------------------------------ */
/*  دسته‌بندی‌ها                                                       */
/* ------------------------------------------------------------------ */

$log( 'ساخت دسته‌بندی‌های محصولات...' );

$categories = array(
	'digital' => 'دیجیتال و موبایل',
	'audio'   => 'صوتی و تصویری',
	'home'    => 'خانه و آشپزخانه',
	'fashion' => 'مد و پوشاک',
	'beauty'  => 'زیبایی و سلامت',
	'sport'   => 'ورزش و سفر',
);

$cat_ids = array();
foreach ( $categories as $slug => $name ) {
	$cat_ids[ $slug ] = arian_get_or_create_term( 'product_cat', $name, array( 'slug' => $slug ) );
}

/* ویژگی برند */
$brand_attr_id = (int) get_option( 'arian_attr_brand' );
if ( ! $brand_attr_id || ! taxonomy_exists( 'pa_brand' ) ) {
	$brand_attr_id = wc_create_attribute(
		array(
			'name'     => 'برند',
			'slug'     => 'brand',
			'type'     => 'select',
			'order_by' => 'menu_order',
		)
	);
	update_option( 'arian_attr_brand', $brand_attr_id );
}
$brand_tax = 'pa_brand';
$brands    = array( 'آریا', 'راد', 'نیکان', 'الف', 'سام', 'تیبا' );
foreach ( $brands as $b ) {
	arian_get_or_create_term( $brand_tax, $b );
}

/* ویژگی‌های متغیر: رنگ و عدسی */
$color_attr_id = (int) get_option( 'arian_attr_color' );
if ( ! $color_attr_id || ! taxonomy_exists( 'pa_color' ) ) {
	$color_attr_id = wc_create_attribute(
		array(
			'name'     => 'رنگ',
			'slug'     => 'color',
			'type'     => 'select',
			'order_by' => 'menu_order',
		)
	);
	update_option( 'arian_attr_color', $color_attr_id );
}
$colors = array( 'مشکی', 'آبی', 'نقره‌ای', 'قهوه‌ای', 'خاکستری' );
foreach ( $colors as $c ) {
	arian_get_or_create_term( 'pa_color', $c );
}

/* ------------------------------------------------------------------ */
/*  کاتالوگ محصولات                                                    */
/* ------------------------------------------------------------------ */

$demo_dir = WP_PLUGIN_DIR . '/arian-core/assets/demo';
$lens_attr_id = (int) get_option( 'arian_attr_lens' );

$catalog = array(
	array(
		'name' => 'هدفون بی‌سیم نویزکنسلینگ S9 Pro', 'sku' => 'AR-1001', 'cat' => 'audio',
		'brand' => 'آریا', 'price' => '1890000', 'sale' => '1490000', 'featured' => true,
		'img' => 'p01-headphones.jpg', 'stock' => 'instock', 'color_variation' => true,
		'short' => 'حذف نویز فعال، ۴۰ ساعت پخش، بلوتوث ۵٫۴ و میکروفون شفاف مکالمه.',
		'desc' => "هدفون بی‌سیم S9 Pro با فناوری حذف نویز فعال (ANC)، درایورهای ۴۰ میلی‌متری و باتری ۴۰ ساعته طراحی شده است. طراحی سبک و مقاوم با بالشتک‌های خنک‌کننده، تجربه‌ای راحت برای استفاده طولانی فراهم می‌کند.\n\nویژگی‌ها:\n- حذف نویز فعال تا ۴۵ دسی‌بل\n- بلوتوث ۵٫۴ با اتصال دوگانه\n- شارژ سریع: ۱۰ دقیقه = ۵ ساعت پخش\n- میکروفون داخلی برای تماس شفاف",
	),
	array(
		'name' => 'ساعت هوشمند FitX 2', 'sku' => 'AR-1002', 'cat' => 'digital',
		'brand' => 'راد', 'price' => '2650000', 'sale' => '2190000', 'featured' => true,
		'img' => 'p02-smartwatch.jpg', 'stock' => 'instock',
		'short' => 'صفحه AMOLED، سنجش ضربان و اکسیژن، GPS داخلی و مقاومت ۵ATM.',
		'desc' => "ساعت هوشمند FitX 2 با صفحه‌نمایش AMOLED، بیش از ۱۰۰ حالت ورزشی، پایش خواب و استرس و باتری ۱۲ روزه، همراه روزمره شماست.\n\n- صفحه ۱٫۴۳ اینچی AMOLED\n- GPS داخلی\n- مقاوم در برابر آب تا ۵۰ متر\n- باتری ۱۲ روزه",
	),
	array(
		'name' => 'اسپیکر بلوتوثی راد R-300', 'sku' => 'AR-1003', 'cat' => 'audio',
		'brand' => 'راد', 'price' => '1240000', 'sale' => '990000', 'featured' => false,
		'img' => 'p03-speaker.jpg', 'stock' => 'instock',
		'short' => 'صدای ۳۶۰ درجه، ۲۰ ساعت پخش و مقاومت IPX7 در برابر آب.',
		'desc' => "اسپیکر R-300 با طراحی استوانه‌ای و صدای ۳۶۰ درجه، مناسب مهمانی‌ها و سفر است. باتری ۲۰ ساعته و استاندارد IPX7 آن را به همراهی مطمئن تبدیل کرده است.",
	),
	array(
		'name' => 'کفش ورزشی رانینگ Breeze', 'sku' => 'AR-1004', 'cat' => 'fashion',
		'brand' => 'نیکان', 'price' => '1750000', 'sale' => '1390000', 'featured' => true,
		'img' => 'p04-sneakers.jpg', 'stock' => 'instock',
		'short' => 'کفی فوم نانو، رویه مش تنفس‌پذیر و وزن ۲۱۰ گرم برای دویدن روزانه.',
		'desc' => "کفش رانینگ Breeze با فوم جذب‌کننده ضربه و رویه مش‌بافی سبک، برای دویدن‌های روزانه و تمرین‌های طولانی طراحی شده است.",
	),
	array(
		'name' => 'کیف لپ‌تاپ ضدآب شهری ۱۵/۶', 'sku' => 'AR-1005', 'cat' => 'fashion',
		'brand' => 'سام', 'price' => '890000', 'sale' => '690000', 'featured' => false,
		'img' => 'p05-backpack.jpg', 'stock' => 'instock',
		'short' => 'پارچه ضدآب، جای لپ‌تاپ ۱۵٫۶ اینچ و جیب مخفی ضدسرقت.',
		'desc' => "کیف لپ‌تاپ شهری با پارچه ضدآب ۱۰۰۰D، پد فوم محافظ لپ‌تاپ تا ۱۵٫۶ اینچ و بندهای ارگونومیک. جیب مخفی پشت کیف از لوازم شما محافظت می‌کند.",
	),
	array(
		'name' => 'عینک آفتابی پلاریزه نیکان', 'sku' => 'AR-1006', 'cat' => 'fashion',
		'brand' => 'نیکان', 'price' => '480000', 'sale' => '380000', 'featured' => false,
		'img' => 'p06-sunglasses.jpg', 'stock' => 'instock', 'lens_variation' => true,
		'short' => 'عدسی پلاریزه UV400، فریم سبک از جنس TR90 با لولاهای فلزی.',
		'desc' => "عینک آفتابی با عدسی پلاریزه ضداشعه UV400 و فریم سبک TR90، برای رانندگی و استفاده روزانه. هر بسته شامل کیف و دستمال مخصوص است.",
	),
	array(
		'name' => 'کیبورد مکانیکی گیمینگ K87', 'sku' => 'AR-1007', 'cat' => 'digital',
		'brand' => 'الف', 'price' => '1980000', 'sale' => '1680000', 'featured' => false,
		'img' => 'p07-keyboard.jpg', 'stock' => 'instock',
		'short' => 'سوییچ‌های قرمز، نورپردازی RGB و بدنه تمام‌آلومینیومی.',
		'desc' => "کیبورد مکانیکی K87 با سوییچ‌های رد لینیر، نورپردازی RGB قابل شخصی‌سازی و صفحه‌کلید روسی/فارسی، انتخابی حرفه‌ای برای گیم و تایپ است.",
	),
	array(
		'name' => 'ماوس بی‌سیم ارگونومیک Silent', 'sku' => 'AR-1008', 'cat' => 'digital',
		'brand' => 'آریا', 'price' => '720000', 'sale' => '590000', 'featured' => false,
		'img' => 'p08-mouse.jpg', 'stock' => 'instock',
		'short' => 'کلیک بی‌صدا، سنسور ۱۶۰۰۰DPI و باتری ۶ ماهه.',
		'desc' => "ماوس ارگونومیک Silent با کلیک‌های بی‌صدا، سنسور دقیق ۱۶۰۰۰DPI و اتصال دوگانه (بلوتوث + دانگل)، مناسب کار طولانی است.",
	),
	array(
		'name' => 'دستگاه قهوه‌ساز ۱۲ فنجان', 'sku' => 'AR-1009', 'cat' => 'home',
		'brand' => 'سام', 'price' => '4850000', 'sale' => '4190000', 'featured' => false,
		'img' => 'p09-coffee.jpg', 'stock' => 'instock',
		'short' => 'ظرفیت ۱٫۵ لیتر، برنامه‌ریز زمان و صفحه‌نمایش لمسی.',
		'desc' => "قهوه‌ساز ۱۲ فنجانی با قابلیت برنامه‌ریزی زمان دم‌آوری، دمای قابل تنظیم و صفحه‌نمایش لمسی، برای شروع روزی پرانرژی.",
	),
	array(
		'name' => 'چراغ مطالعه LED سه‌حالته', 'sku' => 'AR-1010', 'cat' => 'home',
		'brand' => 'آریا', 'price' => '320000', 'sale' => '260000', 'featured' => false,
		'img' => 'p10-lamp.jpg', 'stock' => 'instock',
		'short' => 'سه دمای نور، تنظیم بی‌نهایت شدت و شارژ USB-C.',
		'desc' => "چراغ مطالعه LED با سه حالت نور (گرم/طبیعی/سرد)، کاهش خستگی چشم و باتری شارژی USB-C. مناسب میز کار و کتابخانه.",
	),
	array(
		'name' => 'جاروبرقی رباتیک S5 Self-Clean', 'sku' => 'AR-1011', 'cat' => 'home',
		'brand' => 'راد', 'price' => '8900000', 'sale' => '7700000', 'featured' => true,
		'img' => 'p11-robot.jpg', 'stock' => 'instock',
		'short' => 'نقشه‌برداری LiDAR، مکش ۵۰۰۰Pa و ایستگاه خودتمیزشونده.',
		'desc' => "جاروبرقی رباتیک S5 با نقشه‌برداری LiDAR و مکش قدرتمند ۵۰۰۰Pa، مسیر بهینه را طراحی کرده و با ایستگاه خودتمیزشونده، دست شما را کوتاه می‌کند.",
	),
	array(
		'name' => 'ست مراقبت پوست طبیعی ۴ عددی', 'sku' => 'AR-1012', 'cat' => 'beauty',
		'brand' => 'الف', 'price' => '640000', 'sale' => '520000', 'featured' => true,
		'img' => 'p12-skincare.jpg', 'stock' => 'instock',
		'short' => 'شوینده، تونر، سرم و مرطوب‌کننده با عصاره گیاهان طبیعی.',
		'desc' => "ست مراقبت پوست با ترکیبات گیاهی (آلوئه‌ورا، رزماری و بابونه) و بدون پارابن، برای انواع پوست. شامل شوینده، تونر، سرم ویتامین C و مرطوب‌کننده.",
	),
	array(
		'name' => 'ادکلن مردانه الف ۱۰۰ میلی‌لیتر', 'sku' => 'AR-1013', 'cat' => 'beauty',
		'brand' => 'الف', 'price' => '1150000', 'sale' => '990000', 'featured' => false,
		'img' => 'p13-perfume.jpg', 'stock' => 'instock',
		'short' => 'رایحه چوبی-ادویه‌ای با ماندگاری ۱۰+ ساعت.',
		'desc' => "ادکلن مردانه الف با رایحه چوبی-ادویه‌ای و نت‌های هرمی از لیمو، فلفل صورتی، چوب صندل و کهربا. ماندگاری بیش از ۱۰ ساعت.",
	),
	array(
		'name' => 'کوله‌پشتی ضدآب ۲۵ لیتری کوهنوردی', 'sku' => 'AR-1014', 'cat' => 'sport',
		'brand' => 'سام', 'price' => '950000', 'sale' => '780000', 'featured' => false,
		'img' => 'p14-hiking.jpg', 'stock' => 'instock',
		'short' => 'پارچه رپ‌استاپ، سیستم تهویه پشت و پوشش باران.',
		'desc' => "کوله‌پشتی ۲۵ لیتری با پارچه رپ‌استاپ ضدآب، سیستم تهویه پشت و جیب‌های متعدد، همراه خوب پیاده‌روی و کمپینگ.",
	),
	array(
		'name' => 'بطری حرارتی استیل ۷۵۰ میلی‌لیتر', 'sku' => 'AR-1015', 'cat' => 'sport',
		'brand' => 'تیبا', 'price' => '380000', 'sale' => '290000', 'featured' => false,
		'img' => 'p15-bottle.jpg', 'stock' => 'outofstock',
		'short' => '۲۴ ساعت سرد / ۱۲ ساعت گرم، درب ضدنشت و بدنه استیل ۳۰۴.',
		'desc' => "بطری حرارتی با بدنه استیل ضدزنگ ۳۰۴، حفظ دمای سرد تا ۲۴ ساعت و گرم تا ۱۲ ساعت. درب ضدنشت برای استفاده در سفر و باشگاه.",
	),
	array(
		'name' => 'پاوربانک ۲۰۰۰۰ میلی‌آمپر PD 65W', 'sku' => 'AR-1016', 'cat' => 'digital',
		'brand' => 'آریا', 'price' => '1290000', 'sale' => '1090000', 'featured' => true,
		'img' => 'p16-powerbank.jpg', 'stock' => 'instock',
		'short' => 'شارژ سریع ۶۵ وات، نمایشگر دیجیتال و سه خروجی.',
		'desc' => "پاوربانک ۲۰۰۰۰ میلی‌آمپر با خروجی PD 65W مناسب لپ‌تاپ، نمایشگر درصد شارژ و محافظت هوشمند در برابر اضافه‌بار.",
	),
);

/* ------------------------------------------------------------------ */
/*  ساخت محصولات                                                       */
/* ------------------------------------------------------------------ */

$log( 'ساخت محصولات (با تصاویر دمو)...' );
$product_ids = array();

foreach ( $catalog as $i => $data ) {
	$is_variable = ! empty( $data['color_variation'] ) || ! empty( $data['lens_variation'] );
	$variation_attr = ! empty( $data['color_variation'] ) ? 'pa_color' : 'pa_lens';

	/* ضمانت اجرا: فقط یک‌بار بر اساس SKU */
	$exists = wc_get_product_id_by_sku( $data['sku'] );
	if ( $exists ) {
		$product_ids[] = $exists;
		$log( "محصول موجود است: {$data['name']} (#{$exists})" );
		continue;
	}

	if ( $is_variable && $variation_attr === 'pa_lens' && ! $lens_attr_id ) {
		$lens_attr_id = wc_create_attribute(
			array(
				'name'     => 'رنگ عدسی',
				'slug'     => 'lens',
				'type'     => 'select',
				'order_by' => 'menu_order',
			)
		);
		update_option( 'arian_attr_lens', $lens_attr_id );
		foreach ( array( 'قهوه‌ای', 'خاکستری' ) as $l ) {
			arian_get_or_create_term( 'pa_lens', $l );
		}
		if ( ! taxonomy_exists( 'pa_lens' ) ) {
			$lens_attr_id = 0;
		}
	}

	$product = $is_variable ? new WC_Product_Variable() : new WC_Product_Simple();
	$product->set_name( $data['name'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_sku( $data['sku'] );
	$product->set_description( $data['desc'] );
	$product->set_short_description( $data['short'] );
	$product->set_category_ids( array( $cat_ids[ $data['cat'] ] ) );
	$product->set_stock_status( $data['stock'] );
	$product->set_manage_stock( true );
	$product->set_stock_quantity( 25 );
	$product->set_featured( ! empty( $data['featured'] ) );

	if ( ! $is_variable ) {
		$product->set_regular_price( $data['price'] );
		if ( ! empty( $data['sale'] ) ) {
			$product->set_sale_price( $data['sale'] );
		}
	}

	/* ویژگی برند */
	$brand_term_id = arian_get_or_create_term( $brand_tax, $data['brand'] );
	$brand_attr    = new WC_Product_Attribute();
	$brand_attr->set_id( $brand_attr_id );
	$brand_attr->set_name( $brand_tax );
	$brand_attr->set_options( array( get_term( $brand_term_id, $brand_tax )->slug ) );
	$brand_attr->set_position( 0 );
	$brand_attr->set_visible( true );
	$brand_attr->set_variation( false );
	$product->set_attributes( array( $brand_attr ) );

	/* تصویر شاخص */
	$img_file = $demo_dir . '/' . $data['img'];
	if ( file_exists( $img_file ) ) {
		$img_id = arian_attach_image( $img_file );
		if ( $img_id ) {
			$product->set_image_id( $img_id );
		}
	}

	$product_id = $product->save();
	$product_ids[] = $product_id;

	/* متغیرها */
	if ( $is_variable ) {
		$attr_tax  = $variation_attr;
		$attr_id   = $attr_tax === 'pa_color' ? $color_attr_id : $lens_attr_id;
		$variants  = $attr_tax === 'pa_color'
			? array( 'مشکی' => $data['price'], 'آبی' => $data['sale'], 'نقره‌ای' => $data['price'] )
			: array( 'قهوه‌ای' => $data['sale'], 'خاکستری' => $data['price'] );

		$attr_obj = new WC_Product_Attribute();
		$attr_obj->set_id( $attr_id );
		$attr_obj->set_name( $attr_tax );
		$attr_obj->set_options( array_keys( $variants ) );
		$attr_obj->set_position( 1 );
		$attr_obj->set_visible( true );
		$attr_obj->set_variation( true );
		$product->set_attributes( array( $brand_attr, $attr_obj ) );
		$product->save();

		foreach ( $variants as $label => $price ) {
			$term = get_term_by( 'name', $label, $attr_tax );
			if ( ! $term ) {
				continue;
			}
			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $product_id );
			$variation->set_attributes( array( $attr_tax => $term->slug ) );
			$variation->set_regular_price( $price );
			$variation->set_sku( $data['sku'] . '-' . $term->slug );
			$variation->set_manage_stock( false );
			$variation->set_stock_status( 'instock' );
			$variation->save();
		}
	}

	$log( sprintf( 'محصول %02d: %s (#%d)', $i + 1, $data['name'], $product_id ) );
}

/* به‌روزرسانی جدول‌های جستجوی ووکامرس */
if ( function_exists( 'wc_update_product_lookup_tables' ) ) {
	wc_update_product_lookup_tables();
}

/* تصاویر دسته‌ها (از اولین محصولات هر دسته) */
$cat_thumb_map = array(
	'digital' => 'p02-smartwatch.jpg',
	'audio'   => 'p01-headphones.jpg',
	'home'    => 'p09-coffee.jpg',
	'fashion' => 'p04-sneakers.jpg',
	'beauty'  => 'p12-skincare.jpg',
	'sport'   => 'p14-hiking.jpg',
);
foreach ( $cat_thumb_map as $slug => $img ) {
	$thumb = arian_attach_image( $demo_dir . '/' . $img );
	if ( $thumb && ! empty( $cat_ids[ $slug ] ) ) {
		update_term_meta( $cat_ids[ $slug ], 'thumbnail_id', $thumb );
	}
}

/* ------------------------------------------------------------------ */
/*  مشتری نمونه + نظرات + سفارش‌های نمونه                              */
/* ------------------------------------------------------------------ */

$log( 'ساخت کاربر نمونه، نظرات و سفارش‌های آزمایشی...' );

$customer_id = get_user_by( 'login', 'mohammadi' ) ? get_user_by( 'login', 'mohammadi' )->ID : 0;
if ( ! $customer_id ) {
	$customer_id = wp_insert_user(
		array(
			'user_login'   => 'mohammadi',
			'user_pass'    => 'Customer@1234',
			'user_email'   => 'mohammadi@example.com',
			'first_name'   => 'محمد',
			'last_name'    => 'محمدی',
			'display_name' => 'محمد محمدی',
			'role'         => 'customer',
		)
	);
	$log( "مشتری نمونه ساخته شد: mohammadi (Customer@1234)" );
}

/* نظرات محصولات */
$reviews = array(
	array( 'product' => 'AR-1001', 'rating' => 5, 'author' => 'سارا احمدی', 'text' => 'کیفیت صدا عالی بود و حذف نویز واقعاً کار می‌کنه. بسته‌بندی هم خیلی تمیز بود.' ),
	array( 'product' => 'AR-1001', 'rating' => 4, 'author' => 'علی رضایی', 'text' => 'نسبت به قیمتش عالیه. فقط کاش رنگ بندی بیشتری داشت.' ),
	array( 'product' => 'AR-1004', 'rating' => 5, 'author' => 'مریم کریمی', 'text' => 'برای دویدن عالی، سبکه و کفش به پا می‌چسبه. پیشنهاد می‌کنم.' ),
	array( 'product' => 'AR-1004', 'rating' => 4, 'author' => 'رضا موسوی', 'text' => 'سایزبندی استاندارده. حمل سریع هم انجام شد.' ),
	array( 'product' => 'AR-1011', 'rating' => 5, 'author' => 'نگار حسینی', 'text' => 'از رباتیک خیلی راضی‌ام؛ واقعاً مسیرها رو حفظ می‌کنه و تمیزکاری خوبی داره.' ),
	array( 'product' => 'AR-1013', 'rating' => 4, 'author' => 'امیر شریفی', 'text' => 'رایحه‌اش موندگار و خاصه. بسته‌بندی شکیل بود.' ),
);

foreach ( $reviews as $r ) {
	$pid = wc_get_product_id_by_sku( $r['product'] );
	if ( ! $pid ) {
		continue;
	}
	$comment_id = wp_insert_comment(
		array(
			'comment_post_ID'      => $pid,
			'comment_author'       => $r['author'],
			'comment_author_email' => 'review@example.com',
			'comment_content'      => $r['text'],
			'comment_approved'     => 1,
			'comment_type'         => 'review',
		)
	);
	if ( $comment_id ) {
		update_comment_meta( $comment_id, 'rating', $r['rating'] );
		update_comment_meta( $comment_id, 'verified', 0 );
	}
}

/* میانگین امتیازها */
foreach ( array_unique( array_column( $reviews, 'product' ) ) as $sku ) {
	$pid = wc_get_product_id_by_sku( $sku );
	if ( ! $pid ) {
		continue;
	}
	$ratings = get_comments(
		array(
			'post_id' => $pid,
			'type'    => 'review',
			'status'  => 'approve',
			'fields'  => 'ids',
		)
	);
	$sum = 0;
	foreach ( $ratings as $cid ) {
		$sum += (int) get_comment_meta( $cid, 'rating', true );
	}
	$count = count( $ratings );
	update_post_meta( $pid, '_wc_review_count', $count );
	update_post_meta( $pid, '_wc_average_rating', $count ? round( $sum / $count, 1 ) : 0 );
}

/* سفارش‌های نمونه */
$address = array(
	'first_name' => 'محمد',
	'last_name'  => 'محمدی',
	'address_1'  => 'خیابان ولیعصر، کوچه بهار، پلاک ۱۲',
	'city'       => 'تهران',
	'state'      => 'Tehran',
	'postcode'   => '1998765432',
	'country'    => 'IR',
	'phone'      => '09121234567',
	'email'      => 'mohammadi@example.com',
);

$sample_orders = array(
	array( 'status' => 'completed', 'items' => array( 'AR-1001', 'AR-1013' ) ),
	array( 'status' => 'processing', 'items' => array( 'AR-1004' ) ),
);

foreach ( $sample_orders as $o ) {
	$order = wc_create_order( array( 'customer_id' => $customer_id, 'status' => 'pending' ) );
	foreach ( $o['items'] as $sku ) {
		$pid = wc_get_product_id_by_sku( $sku );
		if ( $pid ) {
			$order->add_product( wc_get_product( $pid ), 1 );
		}
	}
	$order->set_address( $address, 'billing' );
	$order->set_address( $address, 'shipping' );
	$order->calculate_totals();
	$order->set_status( $o['status'], 'سفارش نمونه' );
	$order->save();
	$log( "سفارش نمونه ساخت شد: #{$order->get_order_number()} ({$o['status']})" );
}

/* ------------------------------------------------------------------ */
/*  کوپن‌های تخفیف                                                     */
/* ------------------------------------------------------------------ */

$log( 'ساخت کوپن‌های تخفیف...' );

$coupons = array(
	array(
		'code'   => 'WELCOME10', 'type' => 'percent', 'amount' => 10,
		'desc'   => '۱۰٪ تخفیف خوش‌آمدگویی', 'limit' => 1000,
	),
	array(
		'code'   => 'VIP20', 'type' => 'percent', 'amount' => 20,
		'desc'   => '۲۰٪ تخفیف ویژه خرید بالای ۳ میلیون تومان', 'min' => '3000000', 'limit' => 500,
	),
);
foreach ( $coupons as $c ) {
	if ( wc_get_coupon_id_by_code( $c['code'] ) ) {
		continue;
	}
	$coupon = new WC_Coupon();
	$coupon->set_code( $c['code'] );
	$coupon->set_discount_type( $c['type'] );
	$coupon->set_amount( $c['amount'] );
	$coupon->set_description( $c['desc'] );
	$coupon->set_usage_limit( $c['limit'] );
	if ( ! empty( $c['min'] ) ) {
		$coupon->set_minimum_amount( $c['min'] );
	}
	$coupon->save();
	$log( "کوپن ساخته شد: {$c['code']}" );
}

/* ------------------------------------------------------------------ */
/*  حمل‌ونقل و درگاه‌های پرداخت                                        */
/* ------------------------------------------------------------------ */

$log( 'تنظیم حمل‌ونقل و درگاه‌های پرداخت...' );

/* منطقه حمل‌ونقل ایران (در صورت نبود) */
global $wpdb;
$zone_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_shipping_zones" );
if ( $zone_count === 0 ) {
	$zone = new WC_Shipping_Zone();
	$zone->set_zone_name( 'ایران' );
	$zone->add_location( 'IR', 'country' );
	$zone_id = $zone->save();

	$free_instance = $zone->add_shipping_method( 'free_shipping' );
	if ( $free_instance ) {
		update_option(
			'woocommerce_free_shipping_' . $free_instance . '_settings',
			array(
				'title'       => 'ارسال رایگان (خرید بالای ۵۰۰ هزار تومان)',
				'requires'    => 'min_amount',
				'min_amount'  => '500000',
				'enabled'     => 'yes',
			)
		);
	}

	$flat_instance = $zone->add_shipping_method( 'flat_rate' );
	if ( $flat_instance ) {
		update_option(
			'woocommerce_flat_rate_' . $flat_instance . '_settings',
			array(
				'title'   => 'ارسال پستی پیشتاز',
				'tax_status' => 'none',
				'cost'    => '30000',
				'enabled' => 'yes',
			)
		);
	}
	$log( 'منطقه حمل‌ونقل «ایران» ساخته شد (ارسال رایگان + پستی).' );
}

/* درگاه‌ها */
update_option(
	'woocommerce_cod_settings',
	array(
		'title'       => 'پرداخت در محل',
		'description' => 'مبلغ سفارش را هنگام تحویل به‌صورت نقدی پرداخت کنید.',
		'instructions' => 'لطفاً مبلغ را هنگام دریافت مرسوله پرداخت کنید.',
		'enable_for_methods' => array(),
		'enable_for_virtual' => 'yes',
		'enabled'     => 'yes',
	)
);
update_option(
	'woocommerce_arian_gateway_settings',
	array(
		'title'       => 'پرداخت آنلاین (آزمایشی)',
		'description' => 'شبیه‌ساز درگاه بانکی برای تست فرایند خرید در حالت آفلاین.',
		'instructions' => 'پس از ثبت سفارش به صفحه پرداخت آزمایشی منتقل می‌شوید.',
		'enabled'     => 'yes',
	)
);

/* ------------------------------------------------------------------ */
/*  منوها                                                              */
/* ------------------------------------------------------------------ */

$log( 'ساخت منوهای سایت...' );

$shop_page    = get_option( 'woocommerce_shop_page_id' );
$cart_page    = get_option( 'woocommerce_cart_page_id' );
$checkout_page = get_option( 'woocommerce_checkout_page_id' );
$account_page = get_option( 'woocommerce_myaccount_page_id' );
$contact_page = get_page_by_path( 'contact' );
$blog_page    = get_page_by_path( 'blog' );
$about_page   = get_page_by_path( 'about' );
$terms_page   = get_page_by_path( 'terms' );
$privacy_page = get_page_by_path( 'privacy' );
$track_page   = get_page_by_path( 'order-tracking' );

$menu_items = function ( $menu_id, $items ) {
	if ( ! $menu_id ) {
		return;
	}
	$existing = wp_get_nav_menu_items( $menu_id );
	foreach ( $items as $item ) {
		if ( ! empty( $item['object_id'] ) && is_array( $existing ) ) {
			$found = false;
			foreach ( $existing as $e ) {
				if ( (int) $e->object_id === (int) $item['object_id'] && $e->menu_item_parent == ( $item['parent'] ?? 0 ) ) {
					$found = true;
					break;
				}
			}
			if ( $found ) {
				continue;
			}
		}
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array_merge(
				array(
					'menu-item-status' => 'publish',
					'menu-item-parent-id' => $item['parent'] ?? 0,
				),
				$item
			)
		);
	}
};

$primary = wp_get_nav_menu_object( 'منوی اصلی' );
if ( ! $primary ) {
	$primary_id = wp_create_nav_menu( 'منوی اصلی' );
} else {
	$primary_id = $primary->term_id;
}

$menu_items(
	$primary_id,
	array(
		array( 'menu-item-type' => 'custom', 'menu-item-title' => 'فروشگاه', 'menu-item-url' => get_permalink( $shop_page ) ),
		array( 'menu-item-type' => 'custom', 'menu-item-title' => 'پیشنهاد ویژه', 'menu-item-url' => add_query_arg( 'on_sale', '1', get_permalink( $shop_page ) ) ),
		array( 'menu-item-type' => 'custom', 'menu-item-title' => 'دسته‌بندی‌ها', 'menu-item-url' => get_permalink( $shop_page ) . '#categories' ),
		$blog_page    ? array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $blog_page->ID, 'menu-item-title' => 'وبلاگ' ) : null,
		$about_page   ? array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $about_page->ID, 'menu-item-title' => 'درباره ما' ) : null,
		$contact_page ? array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $contact_page->ID, 'menu-item-title' => 'تماس با ما' ) : null,
	)
);

$footer = wp_get_nav_menu_object( 'منوی پایین' );
if ( ! $footer ) {
	$footer_id = wp_create_nav_menu( 'منوی پایین' );
} else {
	$footer_id = $footer->term_id;
}

$menu_items(
	$footer_id,
	array(
		$about_page   ? array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $about_page->ID, 'menu-item-title' => 'درباره ما' ) : null,
		$terms_page   ? array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $terms_page->ID, 'menu-item-title' => 'قوانین و مقررات' ) : null,
		$privacy_page ? array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $privacy_page->ID, 'menu-item-title' => 'حریم خصوصی' ) : null,
		$track_page   ? array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $track_page->ID, 'menu-item-title' => 'پیگیری سفارش' ) : null,
		$account_page ? array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $account_page->ID, 'menu-item-title' => 'حساب کاربری من' ) : null,
	)
);
$log( 'منوهای اصلی و پایین ساخته شدند.' );

/* ------------------------------------------------------------------ */
/*  پایان                                                              */
/* ------------------------------------------------------------------ */

update_option( 'arian_seeded', '1' );
$GLOBALS['wp_rewrite']->set_permalink_structure( '/%postname%/' );
flush_rewrite_rules( true );

$log( 'داده‌های نمونه با موفقیت بارگذاری شدند. ✅' );
$log( 'مشتری نمونه:  mohammadi / Customer@1234' );
$log( 'کوپن‌ها:      WELCOME10 و VIP20' );
exit( 0 );
