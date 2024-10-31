<?php

/*
Plugin Name: Options Management
Description: A small plugin for developers to manage manually wordpress options.
Version: 1.1
Author: Hive 4 Apps
Author URI: https://hive-4-apps.org
License: GPLv2 or later
*/

namespace H4APlugin\OptionsManagement;

use function H4APlugin\Core\securize_data;

if (!defined('ABSPATH')) {
    die();
}

include_once "core/init.php";

define( "H4A_OM_PLUGIN_DIR_URL", plugin_dir_url( __FILE__ ) );

class H4A_OptionsManagement
{
    const menu_slug = "options-management";

    public function __construct(){
        if( is_admin() ){
            add_action( "admin_menu", array( $this, "init_menu" ) );
            if( isset( $_GET['page'] ) && $_GET['page'] === self::menu_slug  ){
                if( isset( $_POST ) && !empty( $_POST['option_name'] ) ){
                    delete_option( securize_data( $_POST['option_name'], "delete_option" ) );
                }
                add_action( "admin_enqueue_scripts", array( $this, "set_scripts" ) );
            }
        }
    }

    public function init_menu(){
        add_submenu_page(
            "tools.php",
            _( "Options Management" ),
            _( "Options Management" ),
            "manage_options",
            self::menu_slug,
            array("H4APlugin\\OptionsManagement\\H4A_OptionsManagement", "init_page" )
        );
    }

    public function set_scripts(){
        wp_enqueue_style( "h4aoptionmanagementstyle", H4A_OM_PLUGIN_DIR_URL . "css/options-management.css" );
    }

    public static function init_page(){
        echo '<div class="wrap">';
        printf( "<h1>%s</h1>", __( "Options Management") );
        $options = wp_load_alloptions();
        echo '<table class="form-table">';
        echo "<tbody>";
        foreach ($options as $option_name => $option_value ) {
            if ( is_serialized( $option_value ) ) {
                if ( is_serialized_string( $option_value ) ) {
                    $value = sprintf( '<input type="text" value="%s" readonly/>',
                        maybe_unserialize( $option_value )
                    );
                } else {
                    $values = maybe_unserialize( $option_value );
                    $value = self::generateList( $values );
                 }
            }
            else {
                $value = sprintf( '<input type="text" value="%s" readonly/>',
                    maybe_unserialize( $option_value )
                );
            }

            printf( '<tr><th scope="row">%s</th><td>%s</td><td class="action"><form method="post"><input type="hidden" name="option_name" value="%s"/><button class="button" type="submit" ><span class="dashicons dashicons-trash" ></span>%s</button></form></td></tr>',
                $option_name,
                print_r( $value, true ),
                $option_name,
                __( "Delete option" )
            );
        }
        echo "</table>";
        echo "</tbody>";
        echo "</div>";

    }

    public static function generateList( $values ){
        if( empty( $values ) ){
            return '<span class="dashicons dashicons-dismiss"></span>';
        }else if( is_array( $values ) ){
            $value = "<table><tbody>";
            foreach ( $values as $key => $val ){
                if( is_string( $val ) ){
                    $input = sprintf( '<input type="text" value="%s" readonly/>',
                        $val
                    );
                    $value .= sprintf( '<tr class="row" ><td class="label">%s</td><td class="input">%s</td></tr>',
                        $key,
                        $input
                    );
                }else{
                    if(  empty( $val ) ){
                        $class = "row";
                        $input = '<span class="dashicons dashicons-dismiss"></span>';
                    }else{
                        if( is_array( $val ) ){
                            $class = "column";
                            $input = self::generateList( $val );
                        }else{
                            $class = "row";
                            $input = sprintf( '<input type="text" value="%s" readonly/>',
                                $val
                            );
                        }

                    }
                    $value .= sprintf( '<tr class="%s" ><td class="label">%s</td><td class="input">%s</td></tr>',
                        $class,
                        $key,
                        $input
                    );
                }
            }
            $value .= "</tbody></table>";
            return $value;
        }
        return "";
    }
}
new H4A_OptionsManagement();






