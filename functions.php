<?php
/**
 * Theme functions file
 * @package Louie
 * @since Theme version 1.0.0
 */

/**
 * 常量
 */
define( 'THEME_VERSION', '1.0.7' );
define( 'THEME_DIR', get_template_directory() );
define( 'THEME_URL', get_bloginfo('template_directory') );
define( 'THEME_AVATAR_URL', get_bloginfo('template_directory').'/images/avatar.jpg' );
define( 'THEME_DEFAULT_URL', get_bloginfo('template_directory').'/images/default.jpg' );

/**
 * 载入功能组件
 */
include THEME_DIR . '/modules/setting/cs-framework.php';
include THEME_DIR . '/theme-diy.php';
include THEME_DIR . '/modules/object.php';
include THEME_DIR . '/modules/base.php';
include THEME_DIR . '/modules/loop.php';
include THEME_DIR . '/modules/meta.php';
include THEME_DIR . '/modules/style.php';
include THEME_DIR . '/modules/widget.php';
include THEME_DIR . '/modules/depot.php';
include THEME_DIR . '/modules/callback.php';
include THEME_DIR . '/modules/notify.php';
include THEME_DIR . '/modules/plugins/ua.php';
include THEME_DIR . '/modules/player/player.php';
include THEME_DIR . '/modules/plugins/catimage.php';
include THEME_DIR . '/modules/plugins/sitemap.php';
add_action( 'wp_enqueue_scripts', 'theme_scripts' );
function theme_scripts() {
	wp_enqueue_style( 'theme', get_stylesheet_uri(), array(), THEME_VERSION );
	wp_enqueue_style( 'mobile', THEME_URL . '/style.mobile.css', array(), THEME_VERSION, 'all' );
	wp_enqueue_style( 'tips', THEME_URL . '/assets/css/cue.css', array(), THEME_VERSION, 'all' );
	wp_enqueue_style( 'code', THEME_URL . '/assets/css/code.css', array(), THEME_VERSION, 'all' );
	wp_enqueue_script( 'init', THEME_URL . '/assets/js/init.js', array(), THEME_VERSION, true );
	wp_enqueue_script( 'support', THEME_URL . '/assets/js/support.js', array(), THEME_VERSION, true );
    wp_enqueue_script( 'project', THEME_URL . '/assets/js/project.js', array(), THEME_VERSION, true );
    wp_enqueue_script( 'input', THEME_URL . '/assets/js/input.min.js', array(), THEME_VERSION, true );
    wp_enqueue_script( 'headroom', THEME_URL . '/assets/js/headroom.min.js', array(), THEME_VERSION, false );
	wp_enqueue_script( 'app', THEME_URL . '/assets/js/app.js', array(), THEME_VERSION, true );
    wp_enqueue_script( 'player', THEME_URL . '/assets/js/player.js', array(), THEME_VERSION, true );
	wp_localize_script( 'app', 'E' , array(
		'screen' => equipment(),
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'bgm' => array( 'audio' => audioReady(), 'autoplay' => autoplay(), 'shuffle' => shuffleplay(), 'url' => jsonurl() ),
        'bgv' => bgvideo(),
        'comment' => array( 'edit' => commentEdit() ),
	));
}