<?php
/**
 * Plugin Name: JSON Basic Authentication
 * Description: Basic Authentication handler for the JSON API, used for development and debugging purposes
 * Author: :)
 * Author URI: https://github.com/WP-API
 * Version: 0.2
 * Plugin URI: https://github.com/david1311/Basic-Auth
 */

class BasicAuth {
    public function __construct()
    {
        add_filter( 'determine_current_user', [$this, 'basicAuthHandler'], 20 );
        add_filter( 'rest_authentication_errors', [$this, 'restAccessOnlyAllowToLoggedUsers' ]);
    }

    public function basicAuthHandler($user) {
        if ( !isset( $_SERVER['PHP_AUTH_USER'] ) || !empty( $user ) ) {
            return $user;
        }

        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        remove_filter( 'determine_current_user', [$this, 'basicAuthHandler'], 20 );

        $user = wp_authenticate( $username, $password );

        add_filter( 'determine_current_user', [$this, 'basicAuthHandler'], 20 );

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

