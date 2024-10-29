<?php
/*
use Options Framework Plugin from  http://www.wptheming.com
by Devin Price
*/
/* Basic plugin definitions */
define( 'OPTIONS_FRAMEWORK_VERSION', '1.6' );
define( 'OPTIONS_FRAMEWORK_URL', plugin_dir_url( __FILE__ ) );
load_plugin_textdomain( 'wpthemeseo', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

/* Make sure we don't expose any info if called directly */

if ( !function_exists( 'add_action' ) ) {
	exit;
}
add_action( 'init', 'wpthemeseo_rolescheck', 20 );
function wpthemeseo_rolescheck() {
	if ( current_user_can( 'edit_theme_options' ) ) {
		$options =& _wpthemeseo_options();
		if ( $options ) {
			add_action( 'admin_menu', 'wpthemeseo_add_page' );
			add_action( 'admin_init', 'wpthemeseo_init' );
			add_action( 'wp_before_admin_bar_render', 'wpthemeseo_adminbar' );
		}
	}
}
register_activation_hook( __FILE__,'wpthemeseo_activation_hook' );
function wpthemeseo_activation_hook() {
	register_uninstall_hook( __FILE__, 'wpthemeseo_delete_options' );
}
register_uninstall_hook( __FILE__, 'wpthemeseo_delete_options' );
function wpthemeseo_delete_options() {
	$wpthemeseo_settings = get_option( 'wpthemeseo' );
	$knownoptions = $wpthemeseo_settings['knownoptions'];
	if ( $knownoptions ) {
		foreach ( $knownoptions as $key ) {
			delete_option( $key );
		}
	}
	delete_option( 'wpthemeseo' );
}

/* Loads the file for option sanitization */

add_action( 'init', 'wpthemeseo_load_sanitization' );

function wpthemeseo_load_sanitization() {
	require_once dirname( __FILE__ ) . '/options-sanitize.php';
}
function wpthemeseo_init() {
	require_once dirname( __FILE__ ) . '/options-interface.php';
	require_once dirname( __FILE__ ) . '/options-media-uploader.php';
	require_once(WPTHEMESEO_PATH .'options.php');
	$wpthemeseo_settings = get_option( 'wpthemeseo' );
	if ( function_exists( 'wpthemeseo_option_name' ) ) {
		wpthemeseo_option_name();
	}
	elseif ( has_action( 'wpthemeseo_option_name' ) ) {
		do_action( 'wpthemeseo_option_name' );
	}
	else {
		$default_themename = WPTHEMESEO_THEME_NAME;
		$default_themename = preg_replace("/\W/", "_", strtolower($default_themename) );
		$default_themename = 'wpthemeseo_' . $default_themename;
		if ( isset( $wpthemeseo_settings['id'] ) ) {
			if ( $wpthemeseo_settings['id'] == $default_themename ) {
			} else {
				$wpthemeseo_settings['id'] = $default_themename;
				update_option( 'wpthemeseo', $wpthemeseo_settings );
			}
		}
		else {
			$wpthemeseo_settings['id'] = $default_themename;
			update_option( 'wpthemeseo', $wpthemeseo_settings );
		}
	}
	if ( ! get_option( $wpthemeseo_settings['id'] ) ) {
		wpthemeseo_setdefaults();
	}
	register_setting( 'wpthemeseo', $wpthemeseo_settings['id'], 'wpthemeseo_validate' );
	add_filter( 'option_page_capability_wpthemeseo', 'wpthemeseo_page_capability' );
}
function wpthemeseo_page_capability( $capability ) {
	return 'edit_theme_options';
}
function wpthemeseo_setdefaults() {
	$wpthemeseo_settings = get_option( 'wpthemeseo' );
	$option_name = $wpthemeseo_settings['id'];
	if ( isset( $wpthemeseo_settings['knownoptions'] ) ) {
		$knownoptions =  $wpthemeseo_settings['knownoptions'];
		if ( !in_array( $option_name, $knownoptions ) ) {
			array_push( $knownoptions, $option_name );
			$wpthemeseo_settings['knownoptions'] = $knownoptions;
			update_option( 'wpthemeseo', $wpthemeseo_settings);
		}
	} else {
		$newoptionname = array($option_name);
		$wpthemeseo_settings['knownoptions'] = $newoptionname;
		update_option('wpthemeseo', $wpthemeseo_settings);
	}
	$options =& _wpthemeseo_options();
	$values = ws_get_default_values();
	if ( isset($values) ) {
		add_option( $option_name, $values ); // Add option with default settings
	}
}
function wpthemeseo_menu_settings() {
	$this_theme = WPTHEMESEO_THEME_NAME;
	$menu_title = 'Admin Theme Options';
	$menu = array(
		'page_title' => __( $menu_title, 'wpthemeseo'),
		'menu_title' => __($menu_title, 'wpthemeseo'),
		'capability' => 'edit_theme_options',
		'menu_slug' => 'wpthemeseo-options-framework',
		'callback' => 'wpthemeseo_page'
	);	
	return apply_filters( 'wpthemeseo_menu', $menu );
}
function wpthemeseo_add_page() {
	$menu = wpthemeseo_menu_settings();
	$ws_page = add_theme_page( $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], $menu['callback'] );
	add_action( 'admin_enqueue_scripts', 'wpthemeseo_load_scripts' );
	add_action( 'admin_print_styles-' . $ws_page, 'wpthemeseo_load_styles' );
}
function wpthemeseo_load_styles() {
	wp_enqueue_style( 'wpthemeseo', OPTIONS_FRAMEWORK_URL.'css/wpthemeseo.css' );
	if ( !wp_style_is( 'wp-color-picker','registered' ) ) {
		wp_register_style('wp-color-picker', OPTIONS_FRAMEWORK_URL.'css/color-picker.min.css');
	}
	wp_enqueue_style( 'wp-color-picker' );
}
function wpthemeseo_load_scripts( $hook ) {
	$menu = wpthemeseo_menu_settings();
	if ( 'appearance_page_' . $menu['menu_slug'] != $hook )
        return;
	if ( !wp_script_is( 'wp-color-picker', 'registered' ) ) {
		wp_register_script( 'iris', OPTIONS_FRAMEWORK_URL . 'js/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
		wp_register_script( 'wp-color-picker', OPTIONS_FRAMEWORK_URL . 'js/color-picker.min.js', array( 'jquery', 'iris' ) );
		$colorpicker_l10n = array(
			'clear' => __( 'Clear' ),
			'defaultString' => __( 'Default' ),
			'pick' => __( 'Select Color' )
		);
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
	}
	wp_enqueue_script( 'options-custom', OPTIONS_FRAMEWORK_URL . 'js/options-custom.js', array( 'jquery','wp-color-picker' ) );
	add_action( 'admin_head', 'ws_admin_head' );
}

function ws_admin_head() {
	do_action( 'wpthemeseo_custom_scripts' );
}
if ( !function_exists( 'wpthemeseo_page' ) ) :
function wpthemeseo_page() {
	settings_errors(); ?>

	<div id="wpthemeseo-wrap" class="wrap">
    <?php screen_icon( 'themes' ); ?>
    <h2 class="nav-tab-wrapper">
        <?php echo wpthemeseo_tabs(); ?>
    </h2>

    <div id="wpthemeseo-metabox" class="metabox-holder">
	    <div id="wpthemeseo" class="postbox">
			<form action="options.php" method="post">
			<?php settings_fields( 'wpthemeseo' ); ?>
			<?php wpthemeseo_fields(); /* Settings */ ?>
			<div id="wpthemeseo-submit">
				<input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'wpthemeseo' ); ?>" />
				<input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e( 'Restore Defaults', 'wpthemeseo' ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'wpthemeseo' ) ); ?>' );" />
				<div class="clear"></div>
			</div>
			</form>
		</div> <!-- / #container -->
	</div>
	<?php do_action( 'wpthemeseo_after' ); ?>
	</div> <!-- / .wrap -->
	
<?php
}
endif;

function wpthemeseo_validate( $input ) {
	if ( isset( $_POST['reset'] ) ) {
		add_settings_error( 'wpthemeseo-options-framework', 'restore_defaults', __( 'Default options restored.', 'wpthemeseo' ), 'updated fade' );
		return ws_get_default_values();
	}
	
	/*
	 * Update Settings
	 *
	 * This used to check for $_POST['update'], but has been updated
	 * to be compatible with the theme customizer introduced in WordPress 3.4
	 */
	 
	$clean = array();
	$options =& _wpthemeseo_options();
	foreach ( $options as $option ) {

		if ( ! isset( $option['id'] ) ) {
			continue;
		}

		if ( ! isset( $option['type'] ) ) {
			continue;
		}

		$id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

		// Set checkbox to false if it wasn't sent in the $_POST
		if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) {
			$input[$id] = false;
		}

		// Set each item in the multicheck to false if it wasn't sent in the $_POST
		if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) {
			foreach ( $option['options'] as $key => $value ) {
				$input[$id][$key] = false;
			}
		}

		// For a value to be submitted to database it must pass through a sanitization filter
		if ( has_filter( 'ws_sanitize_' . $option['type'] ) ) {
			$clean[$id] = apply_filters( 'ws_sanitize_' . $option['type'], $input[$id], $option );
		}
	}
	
	// Hook to run after validation
	do_action( 'wpthemeseo_after_validate', $clean );
	
	return $clean;
}

/**
 * Display message when options have been saved
 */
 
function wpthemeseo_save_options_notice() {
	add_settings_error( 'wpthemeseo-options-framework', 'save_options', __( 'Options saved.', 'wpthemeseo' ), 'updated fade' );
}

add_action( 'wpthemeseo_after_validate', 'wpthemeseo_save_options_notice' );

/**
 * Format Configuration Array.
 *
 * Get an array of all default values as set in
 * options.php. The 'id','std' and 'type' keys need
 * to be defined in the configuration array. In the
 * event that these keys are not present the option
 * will not be included in this function's output.
 *
 * @return    array     Rey-keyed options configuration array.
 *
 * @access    private
 */

function ws_get_default_values() {
	$output = array();
	$config =& _wpthemeseo_options();
	foreach ( (array) $config as $option ) {
		if ( ! isset( $option['id'] ) ) {
			continue;
		}
		if ( ! isset( $option['std'] ) ) {
			continue;
		}
		if ( ! isset( $option['type'] ) ) {
			continue;
		}
		if ( has_filter( 'ws_sanitize_' . $option['type'] ) ) {
			$output[$option['id']] = apply_filters( 'ws_sanitize_' . $option['type'], $option['std'], $option );
		}
	}
	return $output;
}

/**
 * Add Theme Options menu item to Admin Bar.
 */

function wpthemeseo_adminbar() {

	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array(
			'parent' => 'appearance',
			'id' => 'ws_theme_options',
			'title' => __( 'Theme Options', 'wpthemeseo' ),
			'href' => admin_url( 'themes.php?page=wpthemeseo-options-framework' )
		));
}

/**
 * Get Option.
 *
 * Helper function to return the theme option value.
 * If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 */

if ( ! function_exists( 'ws_get_option' ) ) :

	function ws_get_option( $name, $default = false ) {
		$config = get_option( 'wpthemeseo' );

		if ( ! isset( $config['id'] ) ) {
			return $default;
		}

		$options = get_option( $config['id'] );

		if ( isset( $options[$name] ) ) {
			return $options[$name];
		}

		return $default;
	}
	
endif;

/**
 * Wrapper for wpthemeseo_options()
 *
 * Allows for manipulating or setting options via 'ws_options' filter
 * For example:
 *
 * <code>
 * add_filter('ws_options', function($options) {
 *     $options[] = array(
 *         'name' => 'Input Text Mini',
 *         'desc' => 'A mini text input field.',
 *         'id' => 'example_text_mini',
 *         'std' => 'Default',
 *         'class' => 'mini',
 *         'type' => 'text'
 *     );
 *
 *     return $options;
 * });
 * </code>
 *
 * Also allows for setting options via a return statement in the
 * options.php file.  For example (in options.php):
 *
 * <code>
 * return array(...);
 * </code>
 *
 * @return array (by reference)
 */
function &_wpthemeseo_options() {
	static $options = null;
	if ( !$options ) {
		// Load options from options.php file (if it exists)
		if ( $optionsfile = WPTHEMESEO_PATH .'options.php' ) {
			$maybe_options = require_once $optionsfile;
			if (is_array($maybe_options)) {
				$options = $maybe_options;
			} else if ( function_exists( 'wpthemeseo_options' ) ) {
					$options = wpthemeseo_options();
				}
		}

		// Allow setting/manipulating options via filters
		$options = apply_filters('ws_options', $options);
	}

	return $options;
}