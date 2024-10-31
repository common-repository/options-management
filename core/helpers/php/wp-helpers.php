<?php

namespace H4APlugin\Core;

/**
 * Table of Contents
 *
 * 2.0 - Database
 * -----------------------------------------------------------------------------
 */

/**
 * 1.0 - Database
 * -----------------------------------------------------------------------------
 */

/**
 *
 * To SANITIZE, VALIDATE and ESCAPE data
 *
 * @param $data
 * @param $action
 * @return string|\WP_Error
 */
if( !function_exists( "H4APlugin\Core\securize_data" ) ) {
    function securize_data( $data, $action/*, $type = "string"*/ ) {
        if ( $action === "delete_option" ) {
            return esc_attr( sanitize_key( $data ) );
        } else {
            $message       = sprintf( "Impossible to secure '%s' before doing the action '%s'",
                (string) $data,
                $action
            );
            $wp_error_data = array(
                'data'   => $data,
                'action' => $action
            );
            $errors        = new \WP_Error();
            $errors->add( "s_data_failed", $message, $wp_error_data );

            return $errors;
        }
    }
}


