<?php

// If the file was called directly without the main one,
// do not do anything
if (!defined('SIMPOPUP_COOKIE_NAME')) {
    exit;
}

// We need to wait until wordpress loads completely
// in order to initialize the plugin, because we need
// Wordpress built-in functions
$simpopup_public = new Simpopup_Public();
add_action( 'init', array($simpopup_public, 'init'));

class Simpopup_Public {

    /**
     * By initializing, we're checking the user has not already
     * seen the popup. We also make sure the plugin is enabled and
     * not only visible to the admin, or that the user IS an admin
     */
    public function init() {
        // If the plugin is not enabled, do not show the popup
        $enabled = $this->enabled();
        if ( ! $enabled ) {
            return;
        }

        // If the plugin is currently admin-only and the user is not an admin, do not show the popup
        $adminonly = $this->adminonly();
        if ($adminonly && ! current_user_can( SIMPOPUP_REQUIRED_PERM )) {
            return;
        }

        // If the cookie is set, do not show the popup
        $cookieset = $this->cookieSet();
        if ($cookieset) {
            return;
        }

        // Set the cookie so the popup is shown only once, but only if admin only mode is not enabled
        // This allows admins to test the popup several times
        if ( ! $adminonly) {
            $this->setCookie();
        }

        // If all the previous steps are ok, show the popup
        add_action(
            'wp_head',                  // The name of the hook
            array($this, 'print_css'),  // The callback for this method
            100,                        // The priority (the bigger the lower)
            0                           // Accepted arguments count
        );
        add_action(
            'wp_body_open',
            array($this, 'print_html'),
            100,
            0
        );
        add_action(
            'wp_footer',
            array($this, 'print_js'),
            100,
            0
        );
    }

    /**
     * Retrieves and returns the current enabled state
     * 
     * @return boolean True if the plugin is enabled, false otherwise
     */
    private function enabled() {
        return get_option( 'simpopup_enabled', '') == SIMPOPUP_ENABLED_VALUE;
    }

    /**
     * Retrieves and returns the current admin-only state
     * 
     * @return boolean True if the plugin's action should only be visible to the admins, false otherwise
     */
    private function adminonly() {
        return get_option( 'simpopup_adminonly', '' ) == SIMPOPUP_ADMINONLY_VALUE;
    }

    /**
     * Retrieves the value of the Simpopup's cookie and check
     * its value to make sure the user hasn't seen the popup in the
     * current session
     * 
     * @return boolean True if the cookie is set to "already seen" value for this user, false otherwise
     */
    private function cookieSet() {
        global $simpopup;
        return isset($_COOKIE[SIMPOPUP_COOKIE_NAME]) && $_COOKIE[SIMPOPUP_COOKIE_NAME] == $simpopup->get_cookie_value();
    }

    /**
     * Sets the Simpopup's cookie to the right value so the popup
     * is not displayed the next time the user comes back in the
     * same session
     */
    private function setCookie() {
        global $simpopup;
        setcookie( SIMPOPUP_COOKIE_NAME, $simpopup->get_cookie_value());
    }

    /**
     * This method will be called when the page's body gets
     * opened and will print the plugin's HTML code.
     * 
     * Please note that this is a very unproper manner to add code to the page
     * in Wordpress plugin design, but as it is just for me, I decided to go
     * the simple way.
     * 
     * To learn how to properly enqueue your scripts, please see
     * https://www.wpbeginner.com/wp-tutorials/how-to-properly-add-javascripts-and-styles-in-wordpress/
     */
    public function print_html() {
        echo $this->get_html();
    }

    /**
     * Retrieves and returns the HTML code the plugin will write on the page
     * 
     * @return string The litteral code to write
     */
    private function get_html() {
        return get_option( 'simpopup_html', '' );
    }

    /**
     * This method will be called when the page's header gets
     * opened and will print the plugin's CSS code.
     * 
     * Please note that this is a very unproper manner to add code to the page
     * in Wordpress plugin design, but as it is just for me, I decided to go
     * the simple way.
     * 
     * To learn how to properly enqueue your scripts, please see
     * https://www.wpbeginner.com/wp-tutorials/how-to-properly-add-javascripts-and-styles-in-wordpress/
     */
    public function print_css() {
        echo '<style>' . $this->get_css() . '</style>';
    }

    /**
     * Retrieves and returns the CSS code the plugin will write on the page
     * 
     * @return string The litteral code to write
     */
    private function get_css() {
        return get_option( 'simpopup_css', '' );
    }

    /**
     * This method will be called when the page's footer gets
     * opened and will print the plugin's JavaScript code.
     * 
     * Please note that this is a very unproper manner to add code to the page
     * in Wordpress plugin design, but as it is just for me, I decided to go
     * the simple way.
     * 
     * To learn how to properly enqueue your scripts, please see
     * https://www.wpbeginner.com/wp-tutorials/how-to-properly-add-javascripts-and-styles-in-wordpress/
     */
    public function print_js() {
        echo '<script>' . $this->get_js() . '</script>';
    }

    /**
     * Retrieves and returns the JS code the plugin will write on the page
     * 
     * @return string The litteral code to write
     */
    private function get_js() {
        return get_option( 'simpopup_js', '' );
    }
}