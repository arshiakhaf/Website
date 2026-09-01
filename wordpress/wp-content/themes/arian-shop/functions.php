<?php
/**
 * توابع اصلی قالب آرین‌شاپ
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

define( 'ARIAN_VERSION', '1.0.0' );
define( 'ARIAN_DIR', get_template_directory() );
define( 'ARIAN_URI', get_template_directory_uri() );

require ARIAN_DIR . '/inc/setup.php';
require ARIAN_DIR . '/inc/assets.php';
require ARIAN_DIR . '/inc/helpers.php';
require ARIAN_DIR . '/inc/woocommerce.php';
require ARIAN_DIR . '/inc/ajax.php';
require ARIAN_DIR . '/inc/translate.php';
require ARIAN_DIR . '/inc/customizer.php';
