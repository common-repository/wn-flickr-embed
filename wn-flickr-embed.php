<?php

/**
 * @package WN Flickr Embed
 */
/*
Plugin Name: WN Flickr Image Downloader
Plugin URI: https://wirenomads.com
Description: Download batch images from flickr, store them in the Wordpress Media Library, create a post automatically with the downloaded images and reference the author and the license. Just with one button!
Author: Yaidier Perez
Version: 1.0
Author URI: 
License: GPLv2 or later
*/
/*
Copyright (C) 2020  Yaidier Perez

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if (!defined('ABSPATH')) {
    exit;
}


define('WN_FLICKR_EMBED_DIR', __DIR__);
define('WN_FLICKR_EMBED_VERSION', '1.01');
define('WN_FLICKR_EMBED_URL', plugin_dir_url(__FILE__));

class WirenomadsFlickrEmbed
{
    public  $my_plugin_name;
    function __construct()
    {
        $this->my_plugin_name = plugin_basename(__FILE__);
    }

    function register()
    {        
        add_action('admin_menu', array($this, 'add_admin_pages'));
        // add_filter("plugin_action_links_{$this->my_plugin_name}", array($this, 'settings_link'));
        add_action('admin_enqueue_scripts', array($this, 'load_wp_media_files'));
        add_action('admin_enqueue_scripts', array($this, 'load_my_styles'));
        // add_action('init', array($this, 'zz_shortcode_resource'));

        // add_action('wp_ajax_myprefix_get_image', 'myprefix_get_image');
        add_shortcode('wn-flickbed', array($this, 'includeme_call'));
    }

    function includeme_call($atts = array(), $content = null)
    {

        if (!is_admin()) {            
            $shortcode = $atts['id'];
            $file = strip_tags(WN_FLICKR_EMBED_DIR . '/templates/output.php');
            ob_start();
            include $file;
            $buffer = ob_get_clean();
            $options = get_option('includeme', array());
            if (isset($options['shortcode'])) {
                $buffer = do_shortcode($buffer);
            }
            return $buffer;
        }
    }

    // Ajax action to refresh the user image
    function myprefix_get_image()
    {

        if (isset($_GET['id'])) {
            $image = wp_get_attachment_image(
                filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT),
                'medium',
                false,
                array(
                    'id' => 'myprefix-preview-image',
                )
            );
            $data = array(
                'image' => $image,
            );
            wp_send_json_success($data);
        } else {
            wp_send_json_error();
        }
    }

    function load_my_styles($hook)
    {        
        if ($hook == 'toplevel_page_wn_flickr_embed' || $hook == 'admin_page_wn_flickr_embed_create_new') {
            wp_enqueue_style('wn_flickr_embed_css_styles', WN_FLICKR_EMBED_URL . 'css/mycss.css', array(), current_time( 'mysql' ));
        }
    }

    function load_wp_media_files($hook)
    {

        if ($hook == 'admin_page_wn_flickr_embed_create_new' || $hook == 'toplevel_page_wn_flickr_embed') {
            wp_enqueue_script(
                'wn_flickr_embed_script_admin',
                plugins_url('/js/admin_section.js', __FILE__),
                array('jquery'),
                current_time( 'mysql' )                
            );
        } 
    }

    public function settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=wn_image_hover"> Settings</a>';
        array_push($links, $settings_link);
        return $links;
    }

    public function add_admin_pages()
    {        
        add_menu_page(
            'WN Flickr Embed',
            'WN Flickr Embed',
            'manage_options',
            'wn_flickr_embed',
            array($this, 'admin_index'),
            WN_FLICKR_EMBED_URL . 'images/wn-flicker-embed-icon.svg',
            110
        );

        add_submenu_page(
            null,
            'Create New',
            "Create New",
            'manage_options',
            'wn_flickr_embed_create_new',
            array($this, 'submenu_index')
        );
        add_submenu_page(
            null,
            'wn_flickr_embed_db_handler',
            "Save Handler",
            'manage_options',
            'wn_flickr_embed_db_handler',
            array($this, 'submenu_save_handler')
        );
    }

    public function admin_index()
    {
        include_once plugin_dir_path(__FILE__) . '/templates/main.php';  
    }

    public function submenu_index()
    {
        include_once plugin_dir_path(__FILE__) . '/templates/wn_ih_admin.php';
    }

    public function submenu_save_handler()
    {
        include_once plugin_dir_path(__FILE__) . '/templates/db_handler.php';
    }

    function activate()
    {
        
    }

    function deactivate()
    {
    }

    function uninstall()
    {
        delete_option('WN_Flickr_Embed');
    }
}

if (class_exists('WirenomadsFlickrEmbed')) {
    $wn_flickr_embed = new WirenomadsFlickrEmbed();
    $wn_flickr_embed->register();
}

//activation
register_activation_hook(__FILE__, array($wn_flickr_embed, 'activate'));
//deactivation
register_deactivation_hook(__FILE__, array($wn_flickr_embed, 'deactivate'));
//uninstall
// register_unisntall_hook( __FILE__, array($wn_flickr_embed, 'uninstall'));
