<?php

/**
 * @package WN Flickr Embed
 */
/*
/*
*/

if (!get_option('WN_Flickr_Embed')) {

    //Nothing here

} else {
    $general_options = get_option('WN_Flickr_Embed');
    $general_options['estoy_virando'] = false;
    update_option('WN_Flickr_Embed', $general_options);
};

if (!empty($general_options['embeds'])) $embeds = $general_options['embeds'];

?>

<div class="wrap" style="">

<div class="wn_fl_header">WN Flicker Images Downloader</div>


    <div class="content_table">
        <table class="flipboxes_table">
            <tr>
                <th>
                    <label>ID</label>
                </th>
                <th>
                    <label>Names</label>
                </th>
                <th>
                    <label>Post/Page Title</label>
                </th>
                <th>
                    <label>Shortcodes</label>
                </th>
                <th>
                    <label>Edit</label>
                </th>
            </tr>
            <?php if (isset($embeds)) foreach ($embeds as $embed) { ?>
                <tr>
                    <td>
                        <label><?php echo esc_attr($embed['shortcode']); ?></label>
                    </td>
                    <td>
                        <label><?php echo esc_attr($embed['embed_name']); ?></label>
                    </td>
                    <td>
                        <label><?php echo esc_attr($embed['post_title']); ?></label>
                    </td>
                    <td>
                        <input type="text" name="content_shortcode" id="content_shortcode" class="content_shortcode" value='[wn-embed id="<?php echo esc_attr($embed['shortcode']); ?>"]' readonly>
                    </td>
                    <td>
                        <form class="content_form" action="<?php echo esc_url(admin_url('admin.php?page=wn_flickr_embed_create_new')); ?>" method="POST">
                            <input type="hidden" name="content_shortcode" value="<?php echo esc_attr($embed['shortcode']); ?>">
                            <input type="submit" class="content_button_edit" name="content_button_edit" value="Edit">
                        </form>

                        <form class="content_form" action="<?php echo esc_url(admin_url('admin.php?page=wn_flickr_embed_db_handler')); ?>" method="POST">
                            <input type="hidden" name="content_shortcode" value="<?php echo esc_attr($embed['shortcode']); ?>">

                            <input type="submit" class="content_button_delete" name="content_button_delete" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php }; ?>
        </table>


    </div>

    <form name="" id="add_new_image_hover_form" method="POST" action="<?php echo esc_url(admin_url('admin.php?page=wn_flickr_embed_create_new')); ?>">
        <div id="add_new_image_hover" class="add_new_image_hover">
            <div class="border_interior">
                <svg style="margin-left:auto; margin-right:auto; display:block" id="Capa_1" enable-background="new 0 0 515.556 515.556" viewBox="0 0 515.556 515.556" width="20%" xmlns="http://www.w3.org/2000/svg">
                    <path d="m257.778 0c-142.137 0-257.778 115.641-257.778 257.778s115.641 257.778 257.778 257.778 257.778-115.641 257.778-257.778-115.642-257.778-257.778-257.778zm128.889 290h-96.667v96.667h-64.444v-96.667h-96.667v-64.444h96.667v-96.667h64.444v96.667h96.667z" /></svg>
                <label>Add New Embed</label>
            </div>
        </div>
    </form>
</div>