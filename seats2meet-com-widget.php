<?php
/*
Plugin Name: Seats2meet Booking Widget
Plugin URI: https://seats2meet.com/
Description: Shortcode to embed the Seats2meet Booking Widget.
Author: Seats2meet
Version: 1.2
Author URI: https://seats2meet.com/
*/

/* The shortcode */
function s2m_widget_func($atts) {
    $a = shortcode_atts( array(
        'name' => '',
        'location' => '',
	'title' => 'Zoeken',
    ), $atts );
	return '<script async id="s2m-widget" src="https://now.seats2meet.com/js/frontend/widget/widget.js" data-widget-width="90%" data-search-term="'.$a['name'].'" data-locations="'.$a['location'].'" data-widgetType="21 data-title="'.$a['title'].'"></script>';
}
add_shortcode( 's2m-widget', 's2m_widget_func' );

/* EMBED editor button */
function enqueue_plugin_scripts($plugin_array)
{
    //enqueue TinyMCE plugin script with its ID.
    $plugin_array["s2m_widget_plugin"] =  plugin_dir_url(__FILE__) . "seats2meet-com-widget.js";
    return $plugin_array;
}

add_filter("mce_external_plugins", "enqueue_plugin_scripts");

function register_buttons_editor($buttons)
{
    //register buttons with their id.
    array_push($buttons, "s2m_widget_button");
    return $buttons;
}

add_filter("mce_buttons", "register_buttons_editor");

/* GET S2M Locations */
add_action( 'wp_ajax_s2m_locations', 's2m_locations_callback' );

function s2m_locations_callback() {
	// define('DEBUG');
	require(dirname(__FILE__) . '/seats2meet-com-locations.php');
	wp_die(); // this is required to terminate immediately and return a proper response
}

function s2m_get_locations($requestId = '/locations') {

    $parameters = new stdClass();
    $parameters->Page = 1;
    $parameters->ItemsPerPage  = 9999;
    $parameters->MeetingTypeIds = array(1,2);

    $parameters = json_encode($parameters);
    
    $requestUrl = 'https://www.seats2meet.com/api/locations?' . $parameters;
    $curl = curl_init($requestUrl);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/json', 'Content-Type:application/json;', 'token:62296866'));
    curl_setopt($curl,CURLOPT_POST,false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl,CURLOPT_ENCODING , "");
    $result = curl_exec($curl);
    $headerSent = curl_getinfo($curl, CURLINFO_HEADER_OUT ); // request headers
    $content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

    curl_close($curl);
    
    $res = json_decode($result);
    return $res;
}

function prettyPrint( $json, $indent = 0 )
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".@str_repeat( "\t", $indent ).@str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}