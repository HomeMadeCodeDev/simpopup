<?php

/**
 * Simpopup
 * 
 * @package ch.homemadecode.simpopup
 * @author Maël Imhof
 * @copyright 2022 Maël Imhof
 * @license GNU GPL v3
 * 
 * @wordpress-plugin
 * Plugin Name:   Simpopup
 * Plugin URI:    https://www.mael-imhof.ch
 * Description:   Displays some HTML, CSS and JS to a new session user
 * Version:       1.0.0
 * Author:        Maël Imhof
 * Author URI:    https://www.mael-imhof.ch
 * Text Domain:   simpopup
 * Domain Path:   /languages
 * License:       GNU GPL v3
 * License URI:   https://www.gnu.org/licenses/gpl-3.0.en.html
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