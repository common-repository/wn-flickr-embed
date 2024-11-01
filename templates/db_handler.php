<?php

/**
 * @package WN Flicker Images Downloader
 * version 1.0
 */
/*
*/

$general_options = get_option('WN_Flickr_Embed');

function wn_flickr_embed_create_wp_post($name, $create_as_draft, $create_as_a_page, $shortcode, $data, $do_not_create_post, $photo_prefix) {
    
    $cadena;
    foreach ($data as $imagen) {
        $cadena = $cadena . '<img id="wn_fe_img_' . $imagen['photo_id'] . '" class="wn_fe_img wn_fe_img_'. $shortcode . '" src="' . $imagen['image_local_source'] . '"><br><span id="wn_fe_name1_' . $imagen['photo_id'] . '" class="wn_fe_name1 wn_fe_name1_'. $shortcode . '">[' . $imagen['id'] . ']</span><br><br>';
    };
    foreach ($data as $imagen) {
        $cadena = $cadena . '<span id="wn_fe_name2_' . $imagen['photo_id'] . '" class="wn_fe_name2 wn_fe_name2_'. $shortcode . '">[' . $imagen['id'] . ']</span><br><span id="wn_fe_lic_' . $imagen['photo_id'] . '" class="wn_fe_lic wn_fe_lic_'. $shortcode . '"><a href="' . $imagen['url'] . '" target="_blank">' . $imagen['photo_title_in_flickr'] . '</a> <a href="' . $imagen['license_url'] . '" target="_blank">(' . $imagen['license_abb'] . ')</a> <a href="https://www.flickr.com/people/' . $imagen['owner_path'] . '" target="_blank">' . $imagen['owner_username'] . '</a></span><br><br>';
    };  

    if ($do_not_create_post != 'checked') {

        if($create_as_draft == 'checked') {
            $post_status = 'draft';
        } else {
            $post_status = 'publish';
        };
        if($create_as_a_page == 'checked') {
            $post_type = 'page';
        } else {
            $post_type = 'post';
        };
        $values = [
            'post_title' => $name,
            'post_status' => $post_status,
            'post_content' => $cadena,
            'post_type' => $post_type
        ];
        wp_insert_post($values);
    }
    return $cadena;
}


function wn_flickr_embed_store_image_in_library($image_url, $title) {

    // Gives us access to the download_url() and wp_handle_sideload() functions

    require_once( ABSPATH . 'wp-admin/includes/file.php' );

    // URL to the WordPress logo
    $url = $image_url;
    $timeout_seconds = 5;

    // Download file to temp dir
    $temp_file = download_url( $url, $timeout_seconds );

    if ( !is_wp_error( $temp_file ) ) {
        
        $file = array(
            'name'     => $title . substr(basename($url), -4), // ex: wp-header-logo.png
            'type'     => 'image/png',
            'tmp_name' => $temp_file,
            'error'    => 0,
            'size'     => filesize($temp_file),
        );

        $overrides = array(
            'test_form' => false,
            'test_size' => true,
        );

        // Move the temporary file into the uploads directory
        $results = wp_handle_sideload( $file, $overrides );

        if ( !empty( $results['error'] ) ) {
            // Insert any error handling here;
        } else {

            $local_url = $results['url'];  // URL to the file in the uploads dir
            $type      = $results['type']; // MIME type of the file
            
            $attachment = array(
                'post_mime_type' => $type,
                'post_title' => $title,
                'post_content' => '',
                'post_status' => 'inherit',
            );
        
            $attach_id = wp_insert_attachment( $attachment, $local_url );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $local_url );
            wp_update_attachment_metadata( $attach_id, $attach_data );
        }
    }
    return $local_url;
}


if(!isset($_POST['savechanges'])) {
    $someJSON = file_get_contents('php://input');
    $someArray = json_decode($someJSON, true);
    $general_options['ready_to_insert'] = $someArray;
    update_option('WN_Flickr_Embed', $general_options); 
}


if (isset($_POST['savechanges'])) {

    $embeds = $general_options['embeds'];
    $embed = $embeds[sanitize_text_field($_POST['shortcode'])];
    $ready = $general_options['ready_to_insert'];
    $embed['post_title'] = sanitize_text_field($_POST['post_title']);
    $embed['photo_prefix'] = sanitize_text_field($_POST['image_tittle']);
    $prefix_plus_title = $embed['photo_prefix'] . $embed['post_title'];


    if($ready != "") {
        foreach ($ready as &$value) {
            $value['image_local_source'] = wn_flickr_embed_store_image_in_library($value["image_source"], $value['photo_title']);
        }
    }

    $embed['data'] = $ready;
    $embed['shortcode'] = sanitize_text_field($_POST['shortcode']);
    $embed['input_urls'] = sanitize_textarea_field($_POST['input_urls']);
    $embed['fetch_log'] = sanitize_textarea_field($_POST['fetch_log']);
    $embed['embed_name'] = sanitize_text_field($_POST['embed_name']);

    if (isset($_POST['create_as_draft'])) {
        $general_options['create_as_draft'] = 'checked';
    } else {
        $general_options['create_as_draft'] = '';
    } 

    if(isset($_POST['create_as_a_page'])) {
        $general_options['create_as_a_page'] = 'checked';
    } else {
        $general_options['create_as_a_page'] = '';
    } 

    if(isset($_POST['do_not_create_post'])) {
        $general_options['do_not_create_post'] = 'checked';
    } else {
        $general_options['do_not_create_post'] = '';
    } 

    $general_options['selected_width'] = sanitize_text_field($_POST['image_width']); 
    $general_options['flickr_api_secret'] = sanitize_text_field($_POST['flickr_api_secret']);
    $general_options['flickr_api_key'] = sanitize_text_field($_POST['flickr_api_key']);
    
    $embed['data']['cadena'] = wn_flickr_embed_create_wp_post(sanitize_text_field($_POST['post_title']), $general_options['create_as_draft'], $general_options['create_as_a_page'], $embed['shortcode'], $embed['data'], $general_options['do_not_create_post'], $embed['photo_prefix']);
    $embeds[sanitize_text_field($_POST['shortcode'])] = $embed;
    $general_options['ready_to_insert'] = "";
    $general_options['embeds'] = $embeds;
    $general_options['estoy_virando'] = true;
    $general_options['last_modified'] = sanitize_text_field($_POST['shortcode']);
    update_option('WN_Flickr_Embed', $general_options);  
    $return_to = 'wn_flickr_embed_create_new';      
};

if (isset($_POST['content_button_delete'])) {  
    $embeds = $general_options['embeds'];
    unset($embeds[sanitize_text_field($_POST['content_shortcode'])]);
    $general_options['embeds'] = $embeds;
    update_option('WN_Flickr_Embed', $general_options);   
    $return_to = 'wn_flickr_embed';   
};

if (isset($_POST['save_settings'])) {  

    if(isset($_POST['create_as_draft'])) {
        $general_options['create_as_draft'] = 'checked';
    } else {
        $general_options['create_as_draft'] = '';
    } 

    if(isset($_POST['create_as_a_page'])) {
        $general_options['create_as_a_page'] = 'checked';
    } else {
        $general_options['create_as_a_page'] = '';
    } 

    if(isset($_POST['do_not_create_post'])) {
        $general_options['do_not_create_post'] = 'checked';
    } else {
        $general_options['do_not_create_post'] = '';
    } 

    $general_options['selected_width'] = sanitize_text_field($_POST['image_width']);
    $general_options['flickr_api_secret'] = sanitize_text_field($_POST['flickr_api_secret']);
    $general_options['flickr_api_key'] = sanitize_text_field($_POST['flickr_api_key']);
    $general_options['estoy_virando'] = true;
    $general_options['last_modified'] = sanitize_text_field($_POST['shortcode']);
    update_option('WN_Flickr_Embed', $general_options);  
    $return_to = 'wn_flickr_embed_create_new'; 
}



?>
<script type="text/javascript">
   document.location.href = "<?php echo admin_url('admin.php?page=' . $return_to); ?>";
</script>