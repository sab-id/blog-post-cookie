<?php
/**
Plugin Name: Blog Post Cookie (Cache-Resistant)
Description: Adds a cookie named 'cookie_blog' only on blog posts, resistant to various caching scenarios
Plugin URI: https://github.com/sab-id/blog-post-cookie
Requires at least: 5.0
Author: sab.id
Version: 1.0
Author URI: https://sab.id/
*/

// Hook into WordPress
add_action('init', 'set_blog_post_cookie');
add_action('send_headers', 'send_blog_post_cookie');
add_action('wp_enqueue_scripts', 'enqueue_cookie_script');
add_action('wp_footer', 'output_cookie_script');

function set_blog_post_cookie() {

    // remove cookie if it's in except on single post
    if ( !is_single() || get_post_type() !== 'post') {
        if (isset($_COOKIE['cookie_blog'])) {
            unset($_COOKIE['cookie_blog']);
            setcookie('cookie_blog', '', time() - 3600, '/');
        }
    }

    if (is_single() && get_post_type() === 'post') {
        if (!isset($_COOKIE['cookie_blog'])) {
            $cookie_value = 'blog_visited_' . time();
            $_COOKIE['cookie_blog'] = $cookie_value;
        }
    }
}

function send_blog_post_cookie() {
    if (is_single() && get_post_type() === 'post') {
        if (isset($_COOKIE['cookie_blog'])) {
            $cookie_value = $_COOKIE['cookie_blog'];
            $expiration = time() + (86400 * 30); // 30 days
            setcookie('cookie_blog', $cookie_value, $expiration, '/', '', true, true);
            header('Set-Cookie: cookie_blog=' . $cookie_value . '; Expires=' . gmdate('D, d M Y H:i:s', $expiration) . ' GMT; Path=/; SameSite=Strict; HttpOnly; Secure', false);
        }
    }
}

function enqueue_cookie_script() {
    if (is_single() && get_post_type() === 'post') {
        wp_enqueue_script('jquery');
    }
}

function output_cookie_script() {
    if (is_single() && get_post_type() === 'post') {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            if (document.cookie.indexOf('cookie_blog=') === -1) {
                var cookieValue = 'blog_visited_' + Date.now();
                var expirationDate = new Date();
                expirationDate.setDate(expirationDate.getDate() + 30);
                document.cookie = 'cookie_blog=' + cookieValue + '; expires=' + expirationDate.toUTCString() + '; path=/; secure; samesite=strict';
            }
        });
        </script>
        <?php
    }
}

// Add a must-use plugin to ensure early execution
register_activation_hook(__FILE__, 'create_mu_plugin');
function create_mu_plugin() {
    $mu_plugin_dir = WPMU_PLUGIN_DIR;
    if (!file_exists($mu_plugin_dir)) {
        wp_mkdir_p($mu_plugin_dir);
    }
    $mu_plugin_file = $mu_plugin_dir . '/blog-post-cookie-mu.php';
    $mu_plugin_content = <<<EOT
<?php
add_action('send_headers', function() {
    if (is_single() && get_post_type() === 'post') {
        if (!isset(\$_COOKIE['cookie_blog'])) {
            \$cookie_value = 'blog_visited_' . time();
            \$expiration = time() + (86400 * 30);
            setcookie('cookie_blog', \$cookie_value, \$expiration, '/', '', true, true);
            header('Set-Cookie: cookie_blog=' . \$cookie_value . '; Expires=' . gmdate('D, d M Y H:i:s', \$expiration) . ' GMT; Path=/; SameSite=Strict; HttpOnly; Secure', false);
        }
    }
}, 0);
EOT;
    file_put_contents($mu_plugin_file, $mu_plugin_content);
}

// Fixin compattibility section

// Fixin compattibility with WP Rocket
add_action('rocket_after_output_buffer', 'send_blog_post_cookie');

// Fixin compattibility with WP Super Cache
add_action('wp_super_cache_after_output_buffer', 'send_blog_post_cookie');

// add the cookie on the reponse header if it's in apache or litespeed
if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false || strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false) {
    add_action('send_headers', 'send_blog_post_cookie');
}