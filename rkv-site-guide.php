<?php
/**
 * Adds the guide to the site.
 *
 * Plugin Name: The Guide by Reaktiv
 * Plugin URI: https://github.com/reaktivstudios/rkv-starter
 * Description: Adds the site guide to the site.
 * Author: Reaktiv Studios
 * Author URI: https://reaktivstudios.com
 * Version: 1.1.1
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package rkv-guide
 */

define( 'RKV_SITE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
define( 'RKV_SITE_GUIDE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Initialize the language files.
 *
 * @return void
 */
function rkv_site_guide_lang() {
	load_plugin_textdomain( 'rkv-guide', false, RKV_SITE_GUIDE_PATH . '/languages/' . get_locale() );
}
add_action( 'init', 'rkv_site_guide_lang' );

if ( defined( 'RKV_SITE_GUIDE_API_KEY' ) ) {
	// Autoloader.
	require RKV_SITE_GUIDE_PATH . '/vendor/autoload.php';

	new RKV\Site_Guide\Core();
}
