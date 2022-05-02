<?php

/**
 * @link              https://www.cogdigital.com.au
 * @since             1.0.0
 * @package           Fron
 *
 * @wordpress-plugin
 * Plugin Name:       CF7 Front end submission to CPT
 * Plugin URI:        https://www.cogdigital.com.au/
 * Description:       Creates CPT "stories " and add stories post when  cf7 form is submitted
 * Version:           0.0.1
 * Author:            COG Digital
 * Author URI:        https://www.cogdigital.com.au/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       frontend-sub-cf7
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('CF7_FRONTEND_SUBMISSION', '0.0.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cf-frontendsub-activator.php
 */
function activate_cf_frontendsub()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-cf-frontendsub-activator.php';
	Cf_Frontendsub_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cf-frontendsub-deactivator.php
 */
function deactivate_cf_frontendsub()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-cf-frontendsub-deactivator.php';
	Cf_Frontendsub_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_cf_frontendsub');
register_deactivation_hook(__FILE__, 'deactivate_cf_frontendsub');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-cf-frontendsub.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cf_frontendsub()
{

	$plugin = new Cf_Frontendsub();
	$plugin->run();
}
run_cf_frontendsub();
