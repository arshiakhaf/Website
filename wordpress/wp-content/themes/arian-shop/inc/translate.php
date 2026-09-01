<?php
/**
 * ترجمه‌های فارسی ووکامرس (آفلاین و بدون نیاز به فایل زبان)
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

/**
 * نگاشت متن‌های ووکامرس به فارسی
 *
 * @return array
 */
function arian_wc_strings() {
	return array(
		/* add to cart */
		'Add to cart'                                              => 'افزودن به سبد خرید',
		'Add to basket'                                            => 'افزودن به سبد خرید',
		'Read more'                                                => 'مشاهده بیشتر',
		'Select options'                                           => 'انتخاب گزینه',
		'Out of stock'                                             => 'ناموجود',
		'In stock'                                                 => 'موجود',
		'On backorder'                                             => 'پیش‌خرید',
		'In stock (can be backordered)'                            => 'موجود (پیش‌خرید)',
		'View cart'                                                => 'مشاهده سبد',
		'View basket'                                              => 'مشاهده سبد خرید',
		'Continue shopping'                                        => 'ادامه خرید',
		'Added to your cart.'                                      => 'به سبد خرید اضافه شد.',
		'Product successfully added to your cart.'                 => 'محصول با موفقیت به سبد خرید اضافه شد.',

		/* shop */
		'Shop'                                                     => 'فروشگاه',
		'Browse categories'                                        => 'مرور دسته‌بندی‌ها',
		'Default sorting'                                          => 'مرتب‌سازی پیش‌فرض',
		'Sort by latest'                                           => 'جدیدترین',
		'Sort by popularity'                                       => 'محبوب‌ترین',
		'Sort by average rating'                                   => 'بالاترین امتیاز',
		'Sort by price: low to high'                               => 'ارزان‌ترین',
		'Sort by price: high to low'                               => 'گران‌ترین',
		'Showing %1$s–%2$s of %3$s results'                        => 'نمایش %1$s تا %2$s از %3$s نتیجه',
		'Showing all %d results'                                   => 'نمایش همه %d نتیجه',
		'No products were found matching your selection.'          => 'محصولی مطابق انتخاب شما پیدا نشد.',
		'Nothing found'                                            => 'چیزی پیدا نشد',
		'Sorry, nothing found at this location.'                   => 'متأسفانه در این مکان چیزی پیدا نشد.',
		'Search results for'                                       => 'نتایج جستجو برای',

		/* cart */
		'Cart'                                                     => 'سبد خرید',
		'Your cart is currently empty.'                            => 'سبد خرید شما خالی است.',
		'Return to shop'                                           => 'بازگشت به فروشگاه',
		'Product'                                                  => 'محصول',
		'Price'                                                    => 'قیمت',
		'Quantity'                                                 => 'تعداد',
		'Total'                                                    => 'جمع کل',
		'Cart totals'                                              => 'جمع کل سبد',
		'Subtotal'                                                 => 'جمع جزئی',
		'Shipping'                                                 => 'هزینه ارسال',
		'The shipping options to your address %1$s are being calculated.' => 'هزینه ارسال به آدرس %1$s در حال محاسبه است.',
		'Flat rate'                                                => 'ارسال پستی',
		'Free shipping'                                            => 'ارسال رایگان',
		'Local pickup'                                             => 'تحویل حضوری',
		'Apply coupon'                                             => 'اعمال کوپن',
		'Apply'                                                    => 'اعمال',
		'Remove'                                                   => 'حذف',
		'Remove this item'                                         => 'حذف این کالا',
		'Update cart'                                              => 'به‌روزرسانی سبد',
		'Coupon'                                                   => 'کوپن تخفیف',
		'Coupon code'                                              => 'کد تخفیف',
		'Have a coupon?'                                           => 'کد تخفیف دارید؟',
		'Enter coupon code'                                        => 'کد تخفیف را وارد کنید',
		'Coupon code applied successfully.'                        => 'کوپن تخفیف با موفقیت اعمال شد.',
		'Coupon does not exist!'                                   => 'کد تخفیف معتبر نیست!',
		'Maximum quantity of %1$s items allowed.'                  => 'حداکثر تعداد مجاز: %1$s عدد.',
		'Item already in your cart.'                               => 'این مورد از قبل در سبد شماست.',
		'No shipping options were found.'                          => 'هزینه ارسالی یافت نشد.',

		/* checkout */
		'Checkout'                                                 => 'تسویه حساب',
		'Place order'                                              => 'ثبت سفارش',
		'Billing details'                                          => 'اطلاعات صورتحساب',
		'Shipping details'                                         => 'اطلاعات حمل‌ونقل',
		'Ship to a different address?'                             => 'ارسال به آدرس دیگری باشد؟',
		'Your order'                                               => 'سفارش شما',
		'Payment'                                                  => 'پرداخت',
		'Payment method'                                           => 'روش پرداخت',
		'First name'                                               => 'نام',
		'Last name'                                                => 'نام خانوادگی',
		'Company name'                                             => 'نام شرکت',
		'Country / Region'                                         => 'کشور / منطقه',
		'Street address'                                           => 'آدرس پستی',
		'Town / City'                                              => 'شهر',
		'State / County'                                           => 'استان',
		'Postcode / ZIP'                                           => 'کد پستی',
		'Phone'                                                    => 'شماره تماس',
		'Email address'                                            => 'ایمیل',
		'Order notes'                                              => 'یادداشت سفارش (اختیاری)',
		'Notes about your order, e.g. special notes for delivery.' => 'توضیحات سفارش، مثلاً نحوه تحویل.',
		'Have an account? Read the <em>terms &amp; conditions</em>.' => 'حساب کاربری دارید؟',
		'Create an account?'                                       => 'حساب کاربری بسازم؟',
		'Billing address'                                          => 'آدرس صورتحساب',
		'Shipping address'                                         => 'آدرس ارسال',
		'Your order details'                                       => 'جزئیات سفارش شما',
		'Remove this item from your cart.'                         => 'حذف این کالا از سبد',
		''                                                         => '',
	);
}

/**
 * ترجمه هوشمند متن‌های ووکامرس
 */
add_filter( 'gettext', 'arian_translate_text', 20, 3 );
function arian_translate_text( $translated, $text, $domain ) {
	if ( 'woocommerce' !== $domain || function_exists( 'is_admin' ) && is_admin() ) {
		return $translated;
	}
	$map = arian_wc_strings();
	return isset( $map[ $text ] ) && $map[ $text ] !== '' ? $map[ $text ] : $translated;
}

add_filter( 'gettext_with_context', 'arian_translate_text_context', 20, 4 );
function arian_translate_text_context( $translated, $text, $context, $domain ) {
	if ( 'woocommerce' !== $domain || is_admin() ) {
		return $translated;
	}
	$map = arian_wc_strings();
	return isset( $map[ $text ] ) && $map[ $text ] !== '' ? $map[ $text ] : $translated;
}
