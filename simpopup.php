<?php

/**
 * Simpopup
 * 
 * @package ch.lasym.simpopup
 * @author Maël Imhof
 * @copyright 2022 Maël Imhof
 * @license MIT License
 * 
 * @wordpress-plugin
 * Plugin Name: Simpopup
 * Plugin URI: https://www.mael-imhof.ch
 * Description: Displays some HTML, CSS and JS to a new session user
 * Version: 1.0.0
 * Author: Maël Imhof
 * Author URI: https://www.mael-imhof.ch
 * Text Domain: simpopup
 * Domain Path: /languages
 * License: MIT License
 * License URI: https://spdx.org/licenses/MIT.html
 * 
 * License : MIT License 
 * 
 * Copyright (c) 2022 Maël Imhof
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

$simpopup = new Simpopup();

/**
 * The main instance of this plugin, containing all sorts of useful functions to use
 * from the other parts of the plugin
 */
class Simpopup {

   /**
    * This constructor will be called directly when launching the file,
    * meaning this will be run at activation but also at every page load
    */
   public function __construct() {
      
      // Register an activation hook to make sure we initialize the plugin correctly
      register_activation_hook( __FILE__, array($this, 'activation') );

      // Define the name of the used options
      define('SIMPOPUP_COOKIE_NAME', 'simpopup-cookie-value');
      define('SIMPOPUP_ENABLED_VALUE', 'enable');
      define('SIMPOPUP_ADMINONLY_VALUE', 'adminonly');
      define('SIMPOPUP_REQUIRED_PERM', 'manage_options');

      add_action( 'init', array($this, 'load_text_domain') );

      // Execution if an admin page is being loaded
      if (is_admin()) {
         require_once plugin_dir_path( __FILE__ ) . '/simpopup-admin.php';
      }

      // Execution if a public page is being loaded
      else {
         require_once plugin_dir_path( __FILE__ ) . '/simpopup-public.php';
      }

   }

   /**
    * This hook will be called on activation of the plugin
    */
   public function activation() {
      // Adds the needed "cookie value" option
      add_option( SIMPOPUP_COOKIE_NAME, 'default' );
      $this->new_cookie_value();
   }

   /**
    * Make Wordpress load the text domain of the plugin so it
    * can be translated automatically
    */
   public function load_text_domain() {
      load_plugin_textdomain( 'simpopup', false, 'simpopup/languages' );
   }

   /**
    * Get the name of the cookie to look for to know if the current
    * user already saw the popup
    */
   public static function get_cookie_name() {
      return 'simpopup';
   }

   /**
    * Get the currently valid cookie value, that's to say the value
    * that indicates that the current user has seen the last version
    * of the popup
    */
   public static function get_cookie_value() {
      return get_option( SIMPOPUP_COOKIE_NAME, 'default');
   }

   /**
    * Redefines the value the cookie must have to be valid (not to
    * show the popup to the user again)
    */
   public static function new_cookie_value() {
      // Generate a random value for the cookie
      update_option( SIMPOPUP_COOKIE_NAME, bin2hex(openssl_random_pseudo_bytes(5)) );
   }

}