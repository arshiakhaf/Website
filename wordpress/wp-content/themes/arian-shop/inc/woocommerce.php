<?php
/**
 * یکپارچه‌سازی ووکامرس در قالب
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------- */
/*  ظاهر و چیدمان                                                    */
/* ---------------------------------------------------------------- */

// ۳ ستون در فروشگاه (با سایدبار)
add_filter( 'loop_shop_columns', static fn() => 3 );

// ۱۲ محصول در هر صفحه
add_filter( 'loop_shop_per_page', static fn() => 12 );

// تعداد محصولات مرتبط
add_filter(
	'woocommerce_output_related_products_args',
	static function ( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']        = 4;
		return $args;
	}
);

// حذف سایدبار پیش‌فرض ووکامرس (سایدبار خود قالب)
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// حذف نوار مزاحم فروشگاه
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

// نمایش «افزودن به سبد» در آرشیو
add_filter( 'woocommerce_product_add_to_cart_text', 'arian_add_to_cart_text', 10, 2 );
/**
 * متن دکمه افزودن به سبد
 *
 * @param string $text    متن.
 * @param object $product محصول.
 * @return string
 */
function arian_add_to_cart_text( $text, $product ) {
	if ( $product && $product->is_type( 'variable' ) ) {
		return 'انتخاب گزینه';
	}
	if ( $product && ! $product->is_in_stock() ) {
		return 'ناموجود';
	}
	return 'افزودن به سبد';
}

// نماد واحد پول
add_filter( 'woocommerce_currency_symbol', static fn() => 'تومان' );

// قالب قیمت: «۱٬۲۳۴٬۵۶۷ تومان»
add_filter( 'woocommerce_price_format', static fn() => '%1$s %2$s' );

// جداکننده هزارگان
add_filter( 'wc_price_args', static function ( $args ) {
	$args['thousand_separator'] = '٬';
	return $args;
} );

// حذف هدر/فوتر پیش‌فرض ووکامرس — از قالب استفاده می‌کنیم
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

// نوار پیشرفت (مشخصات) سفارشی برای تگ‌های محصول در تک‌محصول
add_filter(
	'woocommerce_breadcrumb_defaults',
	static function ( $defaults ) {
		$defaults['delimiter']   = ' <span class="crumb-sep">/</span> ';
		$defaults['wrap_before'] = '<nav class="arian-breadcrumb" aria-label="breadcrumb"><div class="container">';
		$defaults['wrap_after']  = '</div></nav>';
		return $defaults;
	}
);

// حذف توضیح (excerpt) و متا از خلاصه تک‌محصول (طراحی سفارشی)
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// عناصر تک‌محصول به‌صورت سفارشی در قالب رندر می‌شوند
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

// چیدمان تب‌ها با فیلتر
add_filter(
	'woocommerce_product_tabs',
	static function ( $tabs ) {
		if ( isset( $tabs['description'] ) ) {
			$tabs['description']['title'] = 'توضیحات محصول';
		}
		if ( isset( $tabs['reviews'] ) ) {
			$tabs['reviews']['title'] = 'نظرات مشتریان';
		}
		if ( isset( $tabs['additional_information'] ) ) {
			$tabs['additional_information']['title'] = 'مشخصات';
		}
		return $tabs;
	}
);

/* ---------------------------------------------------------------- */
/*  مینی‌سبد (قسمت هدر)                                              */
/* ---------------------------------------------------------------- */

add_filter(
	'woocommerce_add_to_cart_fragments',
	static function ( $fragments ) {
		$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

		ob_start();
		?>
		<span class="cart-count" data-cart-count><?php echo esc_html( arian_fa_digits( $count ) ); ?></span>
		<?php
		$fragments['.cart-count'] = ob_get_clean();

		ob_start();
		?>
		<span class="cart-total"><?php echo wp_kses_post( WC()->cart ? WC()->cart->get_cart_subtotal() : wc_price( 0 ) ); ?></span>
		<?php
		$fragments['.cart-total'] = ob_get_clean();

		return $fragments;
	}
);

/* ---------------------------------------------------------------- */
/*  خرید فوری (Buy Now)                                              */
/* ---------------------------------------------------------------- */

add_action(
	'template_redirect',
	static function () {
		if ( ! isset( $_GET['buy-now'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$product_id = absint( $_GET['buy-now'] ); // phpcs:ignore WordPress.Security.NonceVerification
		$product    = wc_get_product( $product_id );
		if ( ! $product || ! $product->is_type( 'simple' ) || ! $product->is_in_stock() ) {
			wp_safe_redirect( wc_get_checkout_url() );
			exit;
		}
		WC()->cart->empty_cart();
		WC()->cart->add_to_cart( $product_id, 1 );
		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	}
);

/* ---------------------------------------------------------------- */
/*  جستجوی محصولات                                                   */
/* ---------------------------------------------------------------- */

add_filter(
	'pre_get_posts',
	static function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return $query;
		}

		// ?on_sale=1 → فقط محصولات تخفیف‌دار
		if ( $query->is_post_type_archive( 'product' ) && isset( $_GET['on_sale'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$tax_query = $query->get( 'tax_query' );
			if ( ! is_array( $tax_query ) ) {
				$tax_query = array();
			}
			$tax_query[] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => array( 'onsale' ),
			);
			$query->set( 'tax_query', $tax_query );
		}

		return $query;
	}
);

/* ---------------------------------------------------------------- */
/*  محصولات صفحه اصلی                                                */
/* ---------------------------------------------------------------- */

/**
 * دریافت محصولات برای بخش‌های صفحه اصلی
 *
 * @param string $type  نوع محصول (recent|featured|sale|best).
 * @param int    $count تعداد.
 * @return WC_Product[]
 */
function arian_get_products( $type = 'recent', $count = 8 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$args = array(
		'status'  => 'publish',
		'limit'   => $count,
		'orderby' => 'date',
		'order'   => 'DESC',
	);

	switch ( $type ) {
		case 'featured':
			$args['featured'] = true;
			break;
		case 'sale':
			$args['include'] = wc_get_product_ids_on_sale();
			$args['limit']   = $count;
			break;
		case 'best':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['order']    = 'DESC';
			break;
	}

	return wc_get_products( $args );
}

/**
 * آی‌دی‌های محصولات تخفیف‌دار (برای شمارش معکوس)
 *
 * @return array
 */
function arian_sale_product_ids() {
	return function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
}
