<?php
function wpthemeseo_option_name() {
	$themename = WPTHEMESEO_THEME_NAME;
	$themename = preg_replace("/\W/", "_", strtolower($themename) );
	$wpthemeseo_settings = get_option('wpthemeseo');
	$wpthemeseo_settings['id'] = $themename;
	update_option('wpthemeseo', $wpthemeseo_settings);
}
function wpthemeseo_options() {
	global $wpthemeseo_theme_colors;
	$options[] = array(
		'name' => __(strtoupper(WPTHEMESEO_THEME_NAME) . ' Admin Theme Settings', 'wpthemeseo'),
		'type' => 'heading');	
	$options[] = array(
		'name' => __('Color'),
		'desc' => __('Choose Style'),
		'id' => 'color',
		'std' => WPTHEMESEO_THEME_DEFAULT_COLOR,
		'type' => 'select',
		'options' => $wpthemeseo_theme_colors);
	return $options;
}