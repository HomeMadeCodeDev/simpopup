<?php

$simpopup = new Simpopup_Admin();

add_action( 'admin_init', array($simpopup, 'register_settings') );
add_action( 'admin_menu', array($simpopup, 'register_page') );

/**
 * Contains and executes all the necessary operations to
 * create administration pages for Simpopup and manage
 * the settings
 */
class Simpopup_Admin {

    /**
     * Register the admin pages of Simpopup
     */
    public function register_page() {
        add_menu_page(
            __( 'Simpopup', 'simpopup' ),                                       // Title of the page
            __( 'Simpopup', 'simpopup' ),                                       // Title of the menu
            SIMPOPUP_REQUIRED_PERM,                                             // Required permission
            'simpopup',                                                         // Menu slug
            array($this, 'display_menu_page_main'),                             // Display callback
                                                                                // Url of the icon of the menu item, or svg URI here
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgc3R5bGU9ImZpbGw6IHJnYmEoMCwgMCwgMCwgMSk7dHJhbnNmb3JtOiA7bXNGaWx0ZXI6OyI+PHBhdGggZD0iTTQgMjFoMTZjMS4xMDMgMCAyLS44OTcgMi0yVjVjMC0xLjEwMy0uODk3LTItMi0ySDRjLTEuMTAzIDAtMiAuODk3LTIgMnYxNGMwIDEuMTAzLjg5NyAyIDIgMnptMC0yVjdoMTZsLjAwMSAxMkg0eiI+PC9wYXRoPjwvc3ZnPg=='
        );
    }

    /**
     * Register the settings of Simpopup
     */
    public function register_settings() {
        register_setting(
            'simpopup_group',                   // New setting's group
            'simpopup_enabled',                 // New setting's name
            array($this, 'sanitize_enabled')    // New setting's sanitize callback
        );
        register_setting(
            'simpopup_group',
            'simpopup_adminonly',
            array($this, 'sanitize_adminonly')
        );
        register_setting(
            'simpopup_group',
            'simpopup_html',
            array($this, 'sanitize_html')
        );
        register_setting(
            'simpopup_group',
            'simpopup_css',
            array($this, 'sanitize_css')
        );
        register_setting(
            'simpopup_group',
            'simpopup_js',
            array($this, 'sanitize_js')
        );

        add_settings_section(
            'simpopup_section',                     // Section's ID
            __( 'Simpopup Settings', 'simpopup' ),  // Section's title
            array($this, 'simpopup_section_infos'), // Section's infos callback
            'simpopup'                              // Section's page
        );

        add_settings_field(
            'simpopup_enabled_field',               // Field's ID
            __( 'Enabled', 'simpopup' ),            // Field's title
            array($this, 'display_field_enabled'),  // Field's callback
            'simpopup',                             // Field's page
            'simpopup_section'                      // Field's section
        );
        add_settings_field(
            'simpopup_adminonly_field',
            __( 'Admin only', 'simpopup' ),
            array($this, 'display_field_adminonly'),
            'simpopup',
            'simpopup_section'
        );
        add_settings_field(
            'simpopup_html_field',
            __( 'HTML code', 'simpopup' ),
            array($this, 'display_field_html'),
            'simpopup',
            'simpopup_section'
        );
        add_settings_field(
            'simpopup_css_field',
            __( 'CSS code', 'simpopup' ),
            array($this, 'display_field_css'),
            'simpopup',
            'simpopup_section'
        );
        add_settings_field(
            'simpopup_js_field',
            __( 'JS code', 'simpopup' ),
            array($this, 'display_field_js'),
            'simpopup',
            'simpopup_section'
        );
    }

    /**
     * Display the settings page of the plugin
     */
    public function display_menu_page_main() {
        
        //Check user capabilities
        if ( ! current_user_can( SIMPOPUP_REQUIRED_PERM ) ) {
            return;
        }

        //If the settings have been updated successfully, display a success message
        if ( isset( $_GET['settings-updated'] ) ) {

            //Despite its name, this function won't always display an error
            add_settings_error(
                'simpopup_messages',
                'simpopup_success_message',
                __( 'Settings saved', 'simpopup' ),
                'updated'
            );
        }

        //Show the messages if any
        settings_errors( 'simpopup_messages' );

        ?>
        <div class="wrap">
            <h1> <?php echo esc_html( get_admin_page_title() ); ?> </h1>
            <form action="options.php" method="post">
                <?php
                    settings_fields( 'simpopup_group' );
                    //Display the settings sections of the page
                    do_settings_sections( 'simpopup' );
                    //Display a submit button to save everything
                    submit_button( __( 'Save settings', 'simpopup' ) );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Sanitize the Enabled setting's input
     * 
     * @param $input The inputs automatically generated by Wordpress
     * @return string The sanitized value to assign to the setting
     */
    public function sanitize_enabled($input) {
        if (is_array($input)) {
            // If modifications were made and the popup is enabled,
            // then change the value of the cookie to make sure
            // the users will see the new version of the popup
            if ($input['simpopup_enabled_field']) {
                global $simpopup;
                $simpopup->new_cookie_value();
            }
            return $input['simpopup_enabled_field'];
        }
        else {
            return $input;
        }
    }

    /**
     * Sanitize the Admin Only setting's input
     * 
     * @param $input The inputs automatically generated by Wordpress
     * @return string The sanitized value to assign to the setting
     */
    public function sanitize_adminonly($input) {
        if (is_array($input)) {
            return $input['simpopup_adminonly_field'];
        }
        else {
            return $input;
        }
    }

    /**
     * Sanitize the HTML code setting's input
     * 
     * @param $input The inputs automatically generated by Wordpress
     * @return string The sanitized value to assign to the setting
     */
    public function sanitize_html($input) {
        if (is_array($input)) {
            return $input['simpopup_html_field'];
        }
        else {
            return $input;
        }
    }

    /**
     * Sanitize the CSS code setting's input
     * 
     * @param $input The inputs automatically generated by Wordpress
     * @return string The sanitized value to assign to the setting
     */
    public function sanitize_css($input) {
        if (is_array($input)) {
            return $input['simpopup_css_field'];
        }
        else {
            return $input;
        }
    }

    /**
     * Sanitize the JS code setting's input
     * 
     * @param $input The inputs automatically generated by Wordpress
     * @return string The sanitized value to assign to the setting
     */
    public function sanitize_js($input) {
        if (is_array($input)) {
            return $input['simpopup_js_field'];
        }
        else {
            return $input;
        }
    }

    /**
     * Will give the informations to display above the section
     */
    public function simpopup_section_infos() {
        ?>
        <p>
            <?php _e( 'Quickly edit the settings of Simpopup here. Simpopup is a plugin meant to display a popup when a user visits your website, on whatever public page, but only if the popup was not shown to this particular user just before. If you want more information, please check', 'simpopup' ); ?>
            <?php echo '<a href="https://github.com/MaelImhof/simpopup"> ' . __( 'this link', 'simpopup' ) . '</a>'; ?>
        </p>
        <?php
    }

    /**
     * Displays the field to set whether the plugin is enabled or not
     */
    public function display_field_enabled() {
        ?>
        <input type="checkbox" id="simpopup_enabled_field" name="simpopup_enabled[simpopup_enabled_field]" value="enable" <?php if (get_option('simpopup_enabled') == SIMPOPUP_ENABLED_VALUE) { echo 'checked'; } ?> />
        <?php
    }

    /**
     * Displays the field to set whether the plugin is only visible to the admin, for test purposes for example
     */
    public function display_field_adminonly() {
        ?>
        <input type="checkbox" id="simpopup_adminonly_field" name="simpopup_adminonly[simpopup_adminonly_field]" value="adminonly" <?php if (get_option('simpopup_adminonly') == SIMPOPUP_ADMINONLY_VALUE) { echo 'checked'; } ?> />
        <?php
    }

    /**
     * Displays the field to insert HTML code in
     */
    public function display_field_html() {
        ?>
        <textarea id="simpopup_html_field" name="simpopup_html[simpopup_html_field]"><?php echo htmlspecialchars(get_option( 'simpopup_html', '' )); ?></textarea>
        <?php
    }

    /**
     * Displays the field to insert CSS code in
     */
    public function display_field_css() {
        ?>
        <textarea id="simpopup_css_field>" name="simpopup_css[simpopup_css_field]"><?php echo htmlspecialchars(get_option( 'simpopup_css', '' )); ?></textarea>
        <?php
    }

    /**
     * Displays the field to insert JS code in
     */
    public function display_field_js() {
        ?>
        <textarea id="simpopup_js_field" name="simpopup_js[simpopup_js_field]"><?php echo htmlspecialchars(get_option( 'simpopup_js', '' )); ?></textarea>
        <?php
    }
}

$simpopup_privacy = new Simpopup_Privacy();
add_action( 'admin_init', array($simpopup_privacy, 'add_privacy_policy') );
add_filter( 'wp_privacy_personal_data_erasers', array($simpopup_privacy, 'privacy_personal_data_erasers') );

/**
 * Handles the privacy and personal data operations, links
 * the plugin to Wordpress default APIs to manage personal data
 * listing, erasing and privacy policy default text
 */
class Simpopup_Privacy {

    /**
     * Each Wordpress website, by default, has a privacy policy.
     * Plugins can give some advice on how to insert them into it,
     * for example, Simpopup uses a cookie, therefore it seems better to
     * notify the admin that there must be a part of the privacy policy mentioning it
     */
    public function add_privacy_policy() {
        $simpopup_privacy_policy_content =
            '<p>' . __('Simpopup', 'simpopup' ) . '</p>' .
            '<p>' . __( 'Simpopup is a very simple plugin that only uses a cookie to avoid showing a popup to a user each time he/she comes on the website', 'simpopup' ) . '</p>';
        wp_add_privacy_policy_content( __( 'Simpopup', 'simpopup' ), $simpopup_privacy_policy_content );
    }

    /**
     * With the RGPD, every website should include the possibility for a user
     * to get or erase its personal data. That's what we're doing here, we register
     * in the Website as a plugin that has data to erase
     */
    public function privacy_personal_data_erasers( $erasers ) {
        $erasers['simpopup'] = array(
            'eraser_friendly_name'  => __( 'Simpopup', 'simpopup' ),
            'callback'              => array($this, 'erase_user_data')
        );
        return $erasers;
    }

    /**
     * This method will be called by Wordpress in case a user wants to erase
     * its personal data. Simpopup just has a cookie to delete
     */
    public function erase_user_data() {

        // If Simpopup's cookie is set, just delete it
        if ( isset($_COOKIE[ SIMPOPUP_COOKIE_NAME ]) ) {

            //To delete a cookie, just set the expiration date to a time in the past
            setcookie( SIMPOPUP_COOKIE_NAME, 'false', time() - 1);
        }

        //Notify Wordpress that Simpopup finished erasing personal data
        return array(
            'items_removed'     => true,
            'items_retained'    => false,
            'messages'          => array(),
            'done'              => true
        );
    }
}