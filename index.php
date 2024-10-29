<?php
/*
Plugin Name: ALMAR
Plugin URI: http://wpthemeseo.com
Description: Admin Theme for Wordpress
Author: WP THEME SEO
Version: 1.0
Author URI: http://wpthemeseo.com
*/
define( 'WPTHEMESEO_PATH', plugin_dir_path(__FILE__) );
define( 'WPTHEMESEO_THEME_NAME', 'almar' );
define( 'WPTHEMESEO_THEME_DEFAULT_COLOR', 'grey' );
$wpthemeseo_theme_colors = array(
		'grey' => __('Grey'),
		'blue' => __('Blue')
);
if ( !function_exists( 'ws_get_option' ) ) {
		include(WPTHEMESEO_PATH .'assets/plugins/wpthemeseo/index.php');
}
function wpthemeseo_load_admin_style() {
		$color = ws_get_option('color', WPTHEMESEO_THEME_DEFAULT_COLOR);
		wp_register_style( 'wpthemeseo_admin_css',  plugins_url( '/assets/css/style.css', __FILE__ ));
        wp_register_style( 'wpthemeseo_admin_default_color',  plugins_url( '/assets/css/default.css', __FILE__ ));
		if ($color != WPTHEMESEO_THEME_DEFAULT_COLOR) wp_register_style( 'wpthemeseo_admin_color',  plugins_url( '/assets/css/'.$color.'.css', __FILE__ ));
        
		wp_enqueue_style( 'wpthemeseo_admin_css' );
        wp_enqueue_style( 'wpthemeseo_admin_default_color' );
		if ($color != WPTHEMESEO_THEME_DEFAULT_COLOR) wp_enqueue_style( 'wpthemeseo_admin_color' );
		
}
function wpthemeseo_load_login_style() {
        wp_register_style( 'wpthemeseo_login_css',  plugins_url( '/assets/css/login.css', __FILE__ ));
        wp_enqueue_style( 'wpthemeseo_login_css' );
}
add_action( 'admin_enqueue_scripts', 'wpthemeseo_load_admin_style' );
add_action( 'login_head', 'wpthemeseo_load_login_style' );	
add_filter('welcome_panel', 'wpthemeseo_welcome_panel' );
function wpthemeseo_welcome_panel() {
	if ( is_blog_admin() && current_user_can('edit_posts') ) {
		global $wp_registered_sidebars;
		$num_posts = wp_count_posts( 'post' );
		$num_pages = wp_count_posts( 'page' );
		$num_cats  = wp_count_terms('category');
		$num_tags = wp_count_terms('post_tag');
		$num_comm = wp_count_comments( );
		$num_user = count_users();

	?>
 <div class="wpthemeseo_section wpthemeseo_group hidden-480 hidden-320">
	<div class="wpthemeseo_col wpthemeseo_span_1_of_4 wpthemeseo_blue">
		<div class="wpthemeseo_pico wpthemeseo_comments"></div>
		<div class="wpthemeseo_info">
					<div class="wpthemeseo_result"><?php echo number_format_i18n($num_comm->total_comments); ?></div>
					<div class="wpthemeseo_title"><?php echo _n( 'Comment', 'Comments', $num_comm->total_comments ); ?></div>
		</div>
	</div>
	<div class="wpthemeseo_col wpthemeseo_span_1_of_4 wpthemeseo_green">
		<div class="wpthemeseo_pico wpthemeseo_posts"></div>
		<div class="wpthemeseo_info">
					<div class="wpthemeseo_result"><?php echo number_format_i18n( $num_posts->publish ); ?></div>
					<div class="wpthemeseo_title"><?php echo _n( 'Post', 'Posts', intval($num_posts->publish) ); ?></div>
		</div>
	</div>
	<div class="wpthemeseo_col wpthemeseo_span_1_of_4 wpthemeseo_violet">
		<div class="wpthemeseo_pico wpthemeseo_pages"></div>
		<div class="wpthemeseo_info">
					<div class="wpthemeseo_result"><?php echo number_format_i18n( $num_pages->publish ); ?></div>
					<div class="wpthemeseo_title"><?php echo _n( 'Page', 'Pages', $num_pages->publish ); ?></div>
		</div>
	</div>
	<div class="wpthemeseo_col wpthemeseo_span_1_of_4 wpthemeseo_jaune">
		<div class="wpthemeseo_pico wpthemeseo_users"></div>
		<div class="wpthemeseo_info">
					<div class="wpthemeseo_result"><?php echo number_format_i18n( $num_user['total_users'] ); ?></div>
					<div class="wpthemeseo_title"><?php echo _n( 'User', 'Users', $num_user['total_users'] ); ?></div>
		</div>
	</div>
</div>
<?php	
	}
}
?>