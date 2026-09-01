<?php
/**
 * شورت‌کدهای فروشگاه
 *
 * @package arian-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * [arian_products type="recent|featured|sale|best" limit="8" columns="4"]
 */
add_shortcode(
	'arian_products',
	static function ( $atts ) {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return '<p>ووکامرس فعال نیست.</p>';
		}

		$atts   = shortcode_atts(
			array(
				'type'    => 'recent',
				'limit'   => 8,
				'columns' => 4,
			),
			$atts,
			'arian_products'
		);
		$limit  = max( 1, min( 24, absint( $atts['limit'] ) ) );
		$type   = sanitize_key( $atts['type'] );

		$args = array(
			'status' => 'publish',
			'limit'  => $limit,
		);

		if ( 'sale' === $type ) {
			$args['include'] = wc_get_product_ids_on_sale();
		} elseif ( 'featured' === $type ) {
			$args['featured'] = true;
		} elseif ( 'best' === $type ) {
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['orderby']  = 'meta_value_num';
		} else {
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
		}

		$products = wc_get_products( $args );
		if ( ! $products ) {
			return '<div class="empty-shop"><p>محصولی یافت نشد.</p></div>';
		}

		ob_start();
		echo '<ul class="products product-grid" data-arian-products>';
		foreach ( $products as $arian_sc_product ) {
			$GLOBALS['product'] = $arian_sc_product;
			wc_get_template_part( 'content', 'product' );
		}
		echo '</ul>';

		return ob_get_clean();
	}
);

/**
 * [arian_contact_form]
 */
add_shortcode(
	'arian_contact_form',
	static function () {
		ob_start();
		include ARIAN_CORE_DIR . 'templates/contact-form.php';
		return ob_get_clean();
	}
);

/**
 * [arian_stats] — آمار لحظه‌ای فروشگاه
 */
add_shortcode(
	'arian_stats',
	static function () {
		if ( ! function_exists( 'WC' ) ) {
			return '';
		}

		$products   = wp_count_posts( 'product' );
		$orders     = wc_get_orders(
			array(
				'limit'  => 1,
				'return' => 'ids',
			)
		);
		$customers  = count_users();
		$product_n  = $products && isset( $products->publish ) ? (int) $products->publish : 0;

		return '<div class="hero-stats">'
			. '<div><strong>' . esc_html( number_format_i18n( $product_n ) ) . '+</strong><span>کالا</span></div>'
			. '<div><strong>%98</strong><span>رضایت مشتری</span></div>'
			. '<div><strong>' . esc_html( number_format_i18n( $customers['total_users'] ) ) . '</strong><span>کاربر فعال</span></div>'
			. '</div>';
	}
);
