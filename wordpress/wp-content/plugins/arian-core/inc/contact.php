<?php
/**
 * فرم تماس (ذخیره در پیشخوان)
 *
 * @package arian-core
 */

defined( 'ABSPATH' ) || exit;

/* نوع نوشته پیام‌ها */
add_action(
	'init',
	static function () {
		register_post_type(
			'arian_message',
			array(
				'labels'       => array(
					'name'          => 'پیام‌های تماس',
					'singular_name' => 'پیام تماس',
					'menu_name'     => 'پیام‌های تماس',
					'add_new'       => 'پیام جدید',
				),
				'public'       => false,
				'show_ui'      => true,
				'menu_icon'    => 'dashicons-email',
				'supports'     => array( 'title', 'editor' ),
				'show_in_menu' => true,
			)
		);
	}
);

/* ستون‌های فهرست پیام‌ها */
add_filter(
	'manage_arian_message_posts_columns',
	static function ( $columns ) {
		return array(
			'cb'          => $columns['cb'],
			'title'       => 'موضوع',
			'arian_from'  => 'فرستنده',
			'arian_email' => 'ایمیل',
			'arian_phone' => 'تلفن',
			'date'        => 'تاریخ',
		);
	}
);

add_action(
	'manage_arian_message_posts_custom_column',
	static function ( $column, $post_id ) {
		switch ( $column ) {
			case 'arian_from':
				echo esc_html( get_post_meta( $post_id, '_arian_name', true ) );
				break;
			case 'arian_email':
				echo esc_html( get_post_meta( $post_id, '_arian_email', true ) );
				break;
			case 'arian_phone':
				echo esc_html( get_post_meta( $post_id, '_arian_phone', true ) );
				break;
		}
	},
	10,
	2
);

/* دریافت فرم */
add_action(
	'admin_post_arian_contact',
	static function () {
		if ( ! isset( $_POST['arian_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['arian_contact_nonce'] ), 'arian_contact' ) ) {
			wp_safe_redirect( wp_get_referer() ? wp_get_referer() : home_url( '/' ) . '?sent=0' );
			exit;
		}

		// هانی‌پات: اگر پر شد یعنی ربات
		if ( ! empty( $_POST['website'] ) ) {
			wp_safe_redirect( home_url( '/contact/?sent=0' ) );
			exit;
		}

		$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : 'پیام جدید';
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

		if ( ! $name || ! $email || ! $message ) {
			wp_safe_redirect( home_url( '/contact/?sent=0' ) );
			exit;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'   => $subject,
				'post_content' => $message,
				'post_status'  => 'private',
				'post_type'    => 'arian_message',
			)
		);

		if ( $post_id ) {
			update_post_meta( $post_id, '_arian_name', $name );
			update_post_meta( $post_id, '_arian_email', $email );
			update_post_meta( $post_id, '_arian_phone', $phone );
		}

		wp_safe_redirect( home_url( '/contact/?sent=1' ) );
		exit;
	}
);
