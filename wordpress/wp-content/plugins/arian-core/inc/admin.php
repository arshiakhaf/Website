<?php
/**
 * پیشخوان مدیریت فروشگاه + ابزار داده نمونه
 *
 * @package arian-core
 */

defined( 'ABSPATH' ) || exit;

/* ---------------- ویجت پیشخوان ---------------- */
add_action(
	'wp_dashboard_setup',
	static function () {
		wp_add_dashboard_widget(
			'arian_dashboard_widget',
			'🏪 خلاصه فروشگاه آرین‌شاپ',
			'arian_dashboard_widget_render'
		);
	}
);

/**
 * رندر ویجت پیشخوان
 */
function arian_dashboard_widget_render() {
	$products = wp_count_posts( 'product' );
	$orders   = function_exists( 'wc_get_orders' ) ? wc_get_orders( array( 'limit' => -1, 'return' => 'ids' ) ) : array();
	$paid     = 0;
	$revenue  = 0;
	if ( function_exists( 'wc_get_order' ) ) {
		foreach ( $orders as $oid ) {
			$order = wc_get_order( $oid );
			if ( ! $order ) {
				continue;
			}
			if ( in_array( $order->get_status(), array( 'completed', 'processing' ), true ) ) {
				$paid++;
				$revenue += (float) $order->get_total();
			}
		}
	}
	$customers = count_users();

	echo '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin:6px 0 14px;">';
	$stats = array(
		'محصولات'  => isset( $products->publish ) ? (int) $products->publish : 0,
		'سفارش‌ها' => count( $orders ),
		'درآمد'    => function_exists( 'wc_price' ) ? wp_strip_all_tags( wc_price( $revenue ) ) : $revenue,
		'کاربران'  => $customers['total_users'],
	);
	foreach ( $stats as $label => $value ) {
		echo '<div style="background:#f6f7fb;border-radius:10px;padding:12px;text-align:center;">'
			. '<div style="font-size:20px;font-weight:800;color:#5b3df5;">' . esc_html( $value ) . '</div>'
			. '<div style="font-size:12px;color:#666;">' . esc_html( $label ) . '</div></div>';
	}
	echo '</div>';

	$links = array(
		'افزودن محصول'  => admin_url( 'post-new.php?post_type=product' ),
		'سفارش‌ها'      => admin_url( 'edit.php?post_type=shop_order' ),
		'گزارش‌ها'      => admin_url( 'admin.php?page=wc-reports' ),
		'تنظیمات'       => admin_url( 'admin.php?page=wc-settings' ),
		'ابزار دمو'     => admin_url( 'tools.php?page=arian-demo' ),
	);
	foreach ( $links as $label => $url ) {
		echo '<a class="button" style="margin:2px;" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a> ';
	}
}

/* ---------------- ابزار داده نمونه ---------------- */
add_action( 'admin_menu', static function () {
	add_management_page( 'ابزار داده نمونه', 'ابزار دمو', 'manage_options', 'arian-demo', 'arian_demo_page' );
} );

/**
 * صفحه ابزار دمو
 */
function arian_demo_page() {
	$output = '';
	if ( isset( $_POST['arian_demo_action'] ) && current_user_can( 'manage_options' ) ) {
		check_admin_referer( 'arian_demo' );
		$action = sanitize_key( $_POST['arian_demo_action'] );

		if ( 'seed' === $action ) {
			$cli  = '/arian/bin/seed.php';
			$php  = defined( 'PHP_BINARY' ) ? PHP_BINARY : 'php';
			if ( is_readable( $cli ) ) {
				exec( escapeshellarg( $php ) . ' ' . escapeshellarg( $cli ) . ' --force 2>&1', $lines, $rc );
				$output = implode( "\n", $lines );
			} else {
				$output = "برای بارگذاری داده‌های نمونه از دستور زیر استفاده کنید:\n\nbash bin/cli.sh provision";
			}
		} elseif ( 'flush' === $action ) {
			update_option( 'arian_seeded', '0' );
			$output = 'پرچم داده نمونه ریست شد. حالا دکمه «بارگذاری دمو» را بزنید.';
		}
	}

	$seeded = '1' === get_option( 'arian_seeded' );
	?>
	<div class="wrap">
		<h1>🧰 ابزار داده‌های نمونه — فروشگاه آرین‌شاپ</h1>
		<div class="card" style="max-width:760px;padding:20px;">
			<p>
				با این ابزار می‌توانید داده‌های نمونه (محصولات، دسته‌ها، کوپن‌ها، سفارش و مشتری تستی) را در فروشگاه
				بارگذاری کنید. نصب اولیه به‌صورت خودکار این کار را انجام می‌دهد؛ این صفحه برای بازسازی مجدد است.
			</p>
			<p>
				<b>وضعیت:</b>
				<?php echo $seeded ? '<span style="color:#16a34a;font-weight:700;">داده نمونه موجود است ✓</span>' : '<span style="color:#b45309;font-weight:700;">هنوز بارگذاری نشده است</span>'; ?>
			</p>
			<form method="post">
				<?php wp_nonce_field( 'arian_demo' ); ?>
				<p>
					<button class="button button-primary" name="arian_demo_action" value="seed">بارگذاری / بازسازی داده‌های نمونه</button>
					<button class="button" name="arian_demo_action" value="flush">ریست پرچم دمو</button>
				</p>
			</form>
			<?php if ( $output ) : ?>
				<pre style="background:#0d1117;color:#c9d1d9;padding:14px;border-radius:10px;overflow:auto;direction:ltr;text-align:left;"><?php echo esc_html( $output ); ?></pre>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
