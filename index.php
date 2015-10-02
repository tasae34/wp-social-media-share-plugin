<?php
/**

* Plugin Name: Social media share
* Plugin URI:
 * Description: This plugin shows public posts from social media.
 * Version: 1.0.0
* Author:
 * Author URI:
 * License: GPL2
*/

require_once 'functions/functions.php';
require_once 'functions/fb_do.php';
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
register_activation_hook(__FILE__,'sm_create_post_table');
register_uninstall_hook(__FILE__, 'sm_drop_post_table');
add_shortcode('twitter','get_tweet_html');
add_shortcode('instagram','get_inst_html');
add_shortcode('facebook','call_fb');
?>