<?php
/*
Plugin Name: F13 Prettyprint
Plugin URI: https://f13.dev/wordpress-plugin-prettyprint/
Description: PrettyPrint any code in "pre" tags
Version: 1.0.0
Author: f13dev
Author URI: http://f13.dev
Text Domain: f13-prettyprint
License: GPLv3
*/

namespace F13\Prettyprint;

if (!function_exists('get_plugins')) require_once(ABSPATH.'wp-admin/includes/plugin.php');
if (!defined('F13_PRETTYPRINT')) define('F13_PRETTYPRINT', get_plugin_data(__FILE__, false, false));
if (!defined('F13_PRETTYPRINT_PATH')) define('F13_PRETTYPRINT_PATH', realpath(plugin_dir_path( __FILE__ )));
if (!defined('F13_PRETTYPRINT_URL')) define('F13_PRETTYPRINT_URL', plugin_dir_url(__FILE__));

class Plugin
{
    public function init()
    {
        spl_autoload_register(__NAMESPACE__.'\Plugin::loader');
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
    }

    public static function loader($name)
    {
        $name = trim(ltrim($name, '\\'));
        if (strpos($name, __NAMESPACE__) !== 0) {
            return;
        }
        $file = str_replace(__NAMESPACE__, '', $name);
        $file = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $file), DIRECTORY_SEPARATOR);
        $file = plugin_dir_path(__FILE__).strtolower($file).'.php';

        if ($file !== realpath($file) || !file_exists($file)) {
            wp_die('Class not found: '.htmlentities($name));
        } else {
            require_once $file;
        }
    }

    public function enqueue()
    {
        wp_enqueue_style('prettify', F13_PRETTYPRINT_URL.'inc/google-code-prettify/prettify.css');
        wp_enqueue_style('f13-prettyprint', F13_PRETTYPRINT_URL.'css/f13-prettyprint.css');
        wp_enqueue_script('prettyprint', F13_PRETTYPRINT_URL.'inc/google-code-prettify/prettify.js');
        wp_enqueue_script('prettyprint-lang-php', F13_PRETTYPRINT_URL.'inc/google-code-prettify/lang-php.js');
        wp_enqueue_script('prettyprint-lang-css', F13_PRETTYPRINT_URL.'inc/google-code-prettify/lang-css.js');
        wp_enqueue_script('f13-prettyprint', F13_PRETTYPRINT_URL.'js/f13-prettyprint.js', array('jquery'), F13_PRETTYPRINT['Version']);
    }
}

$p = new Plugin();
$p->init();
