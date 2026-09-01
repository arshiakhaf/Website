<?php
/**
 * سایدبار نوشته‌ها
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'shop-sidebar' ) ) {
	return;
}
?>
<aside class="page-sidebar">
	<?php dynamic_sidebar( 'shop-sidebar' ); ?>
</aside>
