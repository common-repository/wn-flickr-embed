

<?php

/**
 * @package WN Flicker Images Downloader
 * version 1.0
 */
/*
*/

    $front_end = false;
    if(!isset($general_options)) {
        $general_options = get_option('WN_Flickr_Embed');
        $embed = $general_options['embeds'][$shortcode];       
        $front_end = true;
    }
    else {        
        $front_end = false;
    }  
?>
<div class="wn_fe_images_container">
    <?php foreach($embed['data'] as $image_data) { ?>
        <img id="<?php echo $image_data['photo_title']; ?>" src="<?php echo $image_data['image_local_source']; ?>">
        <br>
        <span>[<?php echo $image_data['photo_title']; ?>]</span>
        <br>
        <br>
    <?php }; ?>

    <?php foreach($embed['data'] as $image_data) { ?>
        <span>[<?php echo $image_data['photo_title']; ?>]</span>
        <br>
        <span>"<a href="<?php echo $image_data['url']; ?>" target="_blank"><?php echo $image_data['photo_title_in_flickr']; ?></a>"</span> 
        <span>(<a href="<?php echo $image_data['license_url']; ?>" target="_blank"> <?php echo $image_data['license_abb']; ?></a>) </span>
        <span>by <a href="https://www.flickr.com/people/<?php echo $image_data['owner_path']; ?>" target="_blank"><?php echo $image_data['owner_username']; ?></a></span>
        <br>
        <br>
    <?php }; ?>
</div>






    