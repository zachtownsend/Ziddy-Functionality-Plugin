<?php
/**
 * Plugin Name:     Ziddy Funk
 * Plugin URI:      https://www.example.com/
 * Description:     Starter plugin for adding functionality to themes
 * Author:          Zach Townsend
 * Author URI:      https://zachtownsend.net/
 * Text Domain:     ziddy-funk
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Ziddy_Funk
 */

namespace ZiddyFunk;

require_once 'vendor/autoload.php';
require_once 'includes/helpers.php';
require_once 'includes/class-ziddy-funk.php';

define('ZF_PLUGIN_ROOT', plugin_dir_path(__FILE__));
define('ZF_PLUGIN_ROOT_URL', plugin_dir_url(__FILE__));

/**
 * Ziddy Funk functionality plugin
 */
function run_plugin()
{
    new ZiddyFunk();
}

run_plugin();
