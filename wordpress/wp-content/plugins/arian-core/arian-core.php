<?php
/**
 * Plugin Name: آرین‌کور — هسته فروشگاه
 * Description: امکانات تکمیلی فروشگاه آرین‌شاپ: شورت‌کدها، فرم تماس، پیشخوان مدیریت و ابزار داده‌های نمونه.
 * Version: 1.0.0
 * Author: Arian Shop
 * Text Domain: arian-core
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

define( 'ARIAN_CORE_VER', '1.0.0' );
define( 'ARIAN_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ARIAN_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once ARIAN_CORE_DIR . 'inc/shortcodes.php';
require_once ARIAN_CORE_DIR . 'inc/contact.php';
require_once ARIAN_CORE_DIR . 'inc/admin.php';
