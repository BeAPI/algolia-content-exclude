<?php
/*
Plugin Name: Algolia Content Exclude
Version: 1.0.0
Version Boilerplate: 3.3.0
Plugin URI: https://beapi.fr
Description: Exclude specific content from algolia indexation
Author: Be API Technical team
Author URI: https://beapi.fr
Domain Path: languages
Text Domain: algolia-content-exclude

----

Copyright 2021 Be API Technical team (human@beapi.fr)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Plugin constants
define( 'ALGOLIA_CONTENT_EXCLUDE_VERSION', '1.0.0' );
define( 'ALGOLIA_CONTENT_EXCLUDE_MIN_PHP_VERSION', '7.2' );
define( 'ALGOLIA_CONTENT_EXCLUDE_VIEWS_FOLDER_NAME', 'algolia-content-exclude' );

// Plugin URL and PATH
define( 'ALGOLIA_CONTENT_EXCLUDE_URL', plugin_dir_url( __FILE__ ) );
define( 'ALGOLIA_CONTENT_EXCLUDE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ALGOLIA_CONTENT_EXCLUDE_PLUGIN_DIRNAME', basename( rtrim( dirname( __FILE__ ), '/' ) ) );

// Check PHP min version
if ( version_compare( PHP_VERSION, ALGOLIA_CONTENT_EXCLUDE_MIN_PHP_VERSION, '<' ) ) {
	require_once ALGOLIA_CONTENT_EXCLUDE_DIR . 'classes/Compatibility.php';

	// Possibly display a notice, trigger error
	add_action( 'admin_init', array( 'BEAPI\Algolia_Content_Exclude\Compatibility', 'admin_init' ) );

	// Stop execution of this file
	return;
}

require ALGOLIA_CONTENT_EXCLUDE_DIR . 'vendor/autoload.php';

// Plugin activate/deactivate hooks
register_activation_hook( __FILE__, array( '\BEAPI\Algolia_Content_Exclude\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\BEAPI\Algolia_Content_Exclude\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', 'init_algolia_content_exclude_plugin' );
/**
 * Init the plugin
 */
function init_algolia_content_exclude_plugin(): void {
	// Client
	\BEAPI\Algolia_Content_Exclude\Main::get_instance();

	// Admin
	if ( is_admin() ) {
		\BEAPI\Algolia_Content_Exclude\Admin\Main::get_instance();
	}
}
