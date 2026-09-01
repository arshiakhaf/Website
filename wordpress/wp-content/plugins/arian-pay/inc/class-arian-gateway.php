<?php
/**
 * درگاه پرداخت آزمایشی (شبیه‌ساز بانک)
 *
 * @package arian-pay
 */

defined( 'ABSPATH' ) || exit;

/**
 * کلاس درگاه
 */
class Arian_Gateway extends WC_Payment_Gateway {

	/**
	 * سازنده
	 */
	public function __construct() {
		$this->id                 = 'arian_gateway';
		$this->method_title       = 'درگاه پرداخت آزمایشی (آرین)';
		$this->method_description = 'شبیه‌ساز درگاه بانکی برای تست کامل فرایند خرید در حالت آفلاین. در صفحه پرداخت، دکمه «پرداخت موفق» یا «پرداخت ناموفق» را انتخاب می‌کنید.';
		$this->has_fields         = false;

		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title', 'پرداخت آنلاین (آزمایشی)' );
		$this->description = $this->get_option( 'description', 'شبیه‌ساز درگاه بانکی برای تست خرید آفلاین' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * فیلدهای تنظیمات
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => 'فعال‌سازی',
				'type'    => 'checkbox',
				'label'   => 'فعال کردن درگاه آزمایشی',
				'default' => 'yes',
			),
			'title' => array(
				'title'   => 'عنوان درگاه',
				'type'    => 'text',
				'default' => 'پرداخت آنلاین (آزمایشی)',
			),
			'description' => array(
				'title'   => 'توضیح',
				'type'    => 'textarea',
				'default' => 'شبیه‌ساز درگاه بانکی برای تست کامل فرایند خرید در حالت آفلاین.',
			),
			'instructions' => array(
				'title'   => 'راهنما پس از پرداخت',
				'type'    => 'textarea',
				'default' => 'پرداخت آزمایشی با موفقیت انجام شد.',
			),
		);
	}

	/**
	 * پردازش پرداخت — انتقال به صفحه درگاه آزمایشی
	 *
	 * @param int $order_id شناسه سفارش.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return array(
				'result' => 'failure',
			);
		}

		$order->update_status( 'pending', 'در انتظار پرداخت از طریق درگاه آزمایشی.' );

		$pay_url = add_query_arg(
			array(
				'arian-pay' => '1',
				'key'       => $order->get_order_key(),
			),
			home_url( '/pay/' )
		);

		return array(
			'result'   => 'success',
			'redirect' => $pay_url,
		);
	}
}
