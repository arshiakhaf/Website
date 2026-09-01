<?php
/**
 * فرم جستجو
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text">جستجو در سایت</span>
		<input type="search" class="search-field" placeholder="جستجو..." value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<button type="submit" class="search-submit"><?php echo arian_icon( 'search' ); ?> <span class="screen-reader-text">جستجو</span></button>
</form>
