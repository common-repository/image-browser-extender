<?php
/**
    Plugin Name: Image Browser Extender
    Plugin URI: http://benjaminsterling.com/wordpress-plugins/wordpress-image-browser-extender/
    Description: Extends the features of the rich text editor for Wordpress by adding a new button that will allow you to easily browse all you image attachments
    Author: Benjamin Sterling
    Version: 0.3.5
    Author URI: http://benjaminsterling.com
    
Copyright 2012  Benjamin Sterling  (benjamin.sterling@kenzomedia.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
add_action('wp_ajax_ibe_action', 'ibe_action_callback');

function ibe_action_callback () {
    global $wpdb;

    $offset = ($_GET['offset']) ? $_GET['offset'] : 0;
    $max_per_page = ($_GET['limit']) ? $_GET['limit'] : 42;
    $limit_args = sprintf("LIMIT %d, %d", $offset, $max_per_page);
    $sql = "SELECT 
                post_title, post_excerpt, 
                post_content, ID, 
                post_name 
            FROM $wpdb->posts 
            WHERE post_type = 'attachment' 
            AND post_mime_type LIKE '%image/%' 
            $limit_args";

    $attachment_posts = $wpdb->get_results($sql, OBJECT);

    if( $attachment_posts ){
        foreach ($attachment_posts as $a => $v ){
            $thumbnail                          = wp_get_attachment_image_src($v->ID, 'thumbnail', false, true);
            $attachment_posts[$a]->thumbnail    = $thumbnail;
            $intermediate                       = wp_get_attachment_image_src($v->ID, 'medium', false, true);
            $attachment_posts[$a]->intermediate = $intermediate;
            $attachment_posts[$a]->url          = wp_get_attachment_url($v->ID);
        }
        echo json_encode($attachment_posts);
    }
    else{
        echo '[]';
    }
    die();
}

// grab the blogs url once
$blogsurl = plugins_url('',__FILE__) . '/plugins';
$serverpath = dirname(__FILE__) . '\plugins';

global $ee_plugins, $ibe_plugins_lang, $ee_buttons_3;
$ibe_plugins = array(
	'wpmedialibrary' => $blogsurl . '/wpmedialibrary/editor_plugin_src.js'
);

$ibe_plugins_lang = array(
	'wpmedialibrary'  => $serverpath . '/wpmedialibrary/langs/lang.php'
);

$ibe_buttons_3 = array(
	'wpmedialibrary'
);

function ibe_mce_external_plugins($array = array()){
	global $ibe_plugins;
	return array_merge($array, $ibe_plugins);
}
add_filter('mce_external_plugins', 'ibe_mce_external_plugins', 1, 1);


function ibe_mce_external_languages($array = array()){
	global $ibe_plugins_lang;
	return array_merge($array, $ibe_plugins_lang);
}
add_filter('mce_external_languages', 'ibe_mce_external_languages', 10, 1);


function ibe_mce_buttons_3($array = array()){
	global $ibe_buttons_3;
	return array_merge($array, $ibe_buttons_3);
}
add_filter('mce_buttons', 'ibe_mce_buttons_3', 1, 1);
?>