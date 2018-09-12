<?php
/**
 * Plugin Name: JSON Basic Authentication
 * Description: Basic Authentication handler for the JSON API, used for development and debugging purposes
 * Author: WordPress API Team
 * Author URI: https://github.com/WP-API
 * Version: 0.1
 * Plugin URI: https://github.com/WP-API/Basic-Auth
 */

class BasicAuth {
    public function __construct()
    {
        add_filter( 'determine_current_user', [$this, 'basicAuthHandler'], 20 );
        add_filter( 'rest_authentication_errors', [$this, 'restAccessOnlyAllowToLoggedUsers' ]);
    }

    public function basicAuthHandler($user) {
        global $wp_json_basic_auth_error;

        $wp_json_basic_auth_error = null;

        if ( !empty( $user ) ) {
            return $user;
        }

        if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
            return $user;
        }

        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        remove_filter( 'determine_current_user', [$this, 'basicAuthHandler'], 20 );

        $user = wp_authenticate( $username, $password );

        add_filter( 'determine_current_user', [$this, 'basicAuthHandler'], 20 );

        if ( is_wp_error( $user ) ) {
            $wp_json_basic_auth_error = $user;
            return null;
        }

        $wp_json_basic_auth_error = true;

        return $user->ID;
    }

    public function restAccessOnlyAllowToLoggedUsers( $access ) {
        if( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
            return new WP_Error( 'rest_cannot_access', __( 'Only authenticated users can access the REST API.', 'disable-json-api' ), array( 'status' => rest_authorization_required_code() ) );
        }
        return $access;
    }
}

new BasicAuth();

