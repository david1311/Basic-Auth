<?php
/**
 * Plugin Name: JSON Basic Authentication
 * Description: Basic Authentication handler for the JSON API, used for development and debugging purposes
 * Author: :)
 * Author URI: https://github.com/WP-API
 * Version: 0.2
 * Plugin URI: https://github.com/david1311/Basic-Auth
 */

namespace Auth;

use WP_Error;

if ( file_exists( $composer_autoload = __DIR__ . '/vendor/autoload.php' )) {
    require_once $composer_autoload;
}

class RestApi {
    public function __construct()
    {
        add_filter( 'determine_current_user', [BasicAuth::class, 'basicAuthHandler'], 20 );
    }

    public static function restAccessOnlyAllowToLoggedUsers() {
        if( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
            return new WP_Error( 'rest_cannot_access', __( 'Only authenticated users can access the REST API.', 'disable-json-api' ), array( 'status' => rest_authorization_required_code() ) );
        }
    }
}

new RestApi();
