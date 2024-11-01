<?php 

    wp_cache_flush();
    $first_time = false;
    $pro = true;

    if (get_option('WN_Flickr_Embed') != '') {
        $general_options = get_option('WN_Flickr_Embed'); 
        $estoy_virando = $general_options['estoy_virando'];
    }
    else
        {            
            $general_options = ['last_modified' => '0', 'version' => WN_FLICKR_EMBED_VERSION];
            $embed = ['shortcode' => '0'];
            $embeds = ['0' => $embed];
            $general_options['embeds'] = $embeds;
            $general_options['create_as_draft'] = 'create_as_draft';
            $general_options['create_as_a_page'] = '';
            $general_options['do_not_create_post'] = 'checked';
            $general_options['selected_width'] = 8;
            $general_options['image_width'] = ['Small' => 3, 'Small 320' => 4, 'Small 400' => 5,
            'Medium' => 6, 'Medium 640' => 7, 'Medium 800' => 8, 'Large' => 9, 'Large 1600' => 10,
            'Large 2048' => 11];            
            $first_time = true;
            $last_modified = 'null';
            $estoy_virando = false;
            update_option('WN_Flickr_Embed', $general_options);            
        }

    if($estoy_virando == true) {
        $embed = $general_options['embeds'][$general_options['last_modified']]; 
        update_option('WN_Flickr_Embed', $general_options);         
    }
    else {

        if(isset($_POST['content_button_edit'])) {
            $embed = $general_options['embeds'][sanitize_text_field($_POST['content_shortcode'])];
        } else {
            $embeds = $general_options['embeds'];
            $last_embed = end($embeds);
            $var1 = $last_embed['shortcode'];
            if ($first_time == false) $var1++;  
            $embed = [
                'shortcode' => $var1, 
                'create_as_draft' => $general_options['create_as_draft'],
                'create_as_a_page' => $general_options['create_as_a_page'], 
                'do_not_create_post' => $general_options['do_not_create_post'], 
                'image_width' => $general_options['image_width']
            ];
            $embeds[$var1] = $embed;
            $general_options['embeds'] = $embeds;
            update_option('WN_Flickr_Embed', $general_options);
        }        
    }

?>
<div class="wrap" style=""> 
    
    <div class="wn_fl_header">WN Flicker Images Downloader</div>

    <div id="principal" class="wn_fe_principal">
        <form id="submit_changes" action="<?php echo admin_url('admin.php?page=wn_flickr_embed_db_handler'); ?>" method="POST">

            <div class="main_content">            
                    <br>
                    <div class="tab">
                        <button id="default_button" type='button' style="margin-left: 0px;" class="tablinks active" onclick="wn_fe_openTab(event, 'Front')">Flickr API</button>
                        <button type='button' class="tablinks" onclick="wn_fe_openTab(event, 'Back')">Global Settings</button>
                    </div>
                    <div id="Front" class="tabcontent activo_default">
                        <div class="columna1">
                            <label class="wn_fe_labels" for="input_urls">Flickr URLs</label>
                            <textarea style="width: 100%;" placeholder="E.g. https://www.flickr.com/photos/jfingas/18314701540/" name="input_urls" id="input_urls" class="input_urls"><?php echo esc_textarea($embed['input_urls']); ?></textarea>
                            <br><br>
                            <div id="post_title_input" style="display:<?php if ($general_options['do_not_create_post'] == 'checked') echo esc_attr('none'); else echo esc_attr('block'); ?>">
                                <label class="wn_fe_labels" for="post_title">Post Title</label>
                                <input style="width: 100%; margin-top: 0px" type="text" id="post_title" value="<?php if ($general_options['do_not_create_post'] == 'checked') echo esc_attr('none'); else echo esc_attr($embed['post_title']); ?>" class="inputs post_title" name="post_title">
                                <br><br>
                            </div>
                            <label class="wn_fe_labels" for="image_tittle">Photo Prefix</label>
                            <input style="width: 100%; margin-top: 0px" type="text" id="image_tittle" value="<?php echo esc_attr($embed['photo_prefix']); ?>" class="inputs image_tittle" name="image_tittle">                            
                        </div>
                        <div class="columna2">
                            <label class="wn_fe_labels" for="fetch_log">Fetch Log</label>
                            <textarea name="fetch_log" id="fetch_log" class="fetch_log" readonly><?php echo esc_textarea($embed['fetch_log']); ?></textarea>
                                                        
                        </div>
                        <div class="final"></div>
                    </div>

                    <div id="Back" class="tabcontent extra_margin">
                        <div class="columna1">               
                            <label class="switch" style="">
                                <input type="checkbox" name="do_not_create_post" id="do_not_create_post" <?php echo esc_attr($general_options['do_not_create_post']); ?>>
                                <span class="chucho round"></span>
                            </label>
                            <label class="switch_label" for="do_not_create_post">Do not create a Post/Page Automatically</label>
                            <br><br>

                            <div id="create_new_post_page_div" style="display:<?php if ($general_options['do_not_create_post'] == 'checked') echo esc_attr('none'); else echo esc_attr('block'); ?>">
                                <label class="switch" style="">
                                    <input type="checkbox" name="create_as_draft" id="create_as_draft" <?php echo  esc_attr($general_options['create_as_draft']); ?>>
                                    <span class="chucho round"></span>
                                </label>
                                <label class="switch_label" for="create_as_draft">Create New Posts/Page as 'Draft'</label>
                                <br><br>
                                <label class="switch" style="">
                                    <input type="checkbox" name="create_as_a_page" id="create_as_a_page" <?php echo  esc_attr($general_options['create_as_a_page']); ?>>
                                    <span class="chucho round"></span>
                                </label>
                                <label class="switch_label" for="create_as_a_page">Create a New Page instead of a Post</label>
                            </div>
                        </div>

                        <div class="columna2">
                            <label class="switch_label select_width_label" for="flickr_api_key">Flickr API key</label>
                            <input type="text" id="flickr_api_key" class="flickr_api_key inputs_to_right" name="flickr_api_key" value="<?php echo esc_attr($general_options['flickr_api_key']); ?>">
                            <div class="final"></div>
                            <br>

                            <label class="switch_label select_width_label" for="flickr_api_secret">Flickr API Secret Key</label>
                            <input type="text" id="flickr_api_secret" class="flickr_api_secret inputs_to_right" name="flickr_api_secret" value="<?php echo esc_attr($general_options['flickr_api_secret']); ?>">
                            <div class="final"></div>
                            <br>
                            <div>
                                <a href="https://www.flickr.com/services/api/misc.api_keys.html" target="_blank">Request your API key and Secret key on Flickr</a>
                            </div>

                            <hr>
                            <label class="switch_label select_width_label" for="image_width">Image Width </label>                            
                            <select name="image_width" id="image_width" class="image_width inputs_to_right">
                                <?php foreach ($general_options['image_width'] as $size => $valor) { ?>
                                    <option value="<?php echo  esc_attr($valor); ?>" <?php if($valor == $general_options['selected_width']) echo ' selected ' ?> ><?php echo  esc_attr($size); ?></option>       
                                <?php }; ?>                     
                            </select>                        
                            <br><br>                            
                        </div>                        
                        <div class="final"></div>
                        <div class=save_settings_container>
                            <button type="submit" name="save_settings" class="button-primary save_settings">Save Settings</button>
                        </div>
                    </div>  
            </div>   
            <div id="" class="default_box sidebar">        
                <label>Shortcode</label>
                <input type="hidden"  id="shortcode" class="shortcode" name="shortcode" value="<?php echo  esc_attr($embed['shortcode']); ?>">
                <input type="text" style="text-align: center;" id="shortcodeshow" class="shortcodeshow" name="shortcodeshow" value='[wn-flickbed id="<?php echo  esc_attr($embed['shortcode']); ?>"]' readonly>
                <br>
                <br>
                <label>Name</label>
                <input type="text" style="text-align: center;" placeholder="Embed's Name" id="embed_name" class="embed_name" name="embed_name" value="<?php echo  esc_attr($embed['embed_name']); ?>">
            </div>

            <div id="" class="default_box sidebar submit_section">   
                <div id="fetch_photos" class="submit_button fetch_button deactivated_submit">Fetch Photos</div>     
                <br>
                <input type="button" id="savechanges" name="savechanges" class="submit_button deactivated_submit" value="<?php if ($general_options['do_not_create_post'] == 'checked') echo  esc_attr('Save'); else echo esc_attr('Save & Create Post'); ?>"> 
            </div>
            <div class="final"></div>
            <input type="hidden" id="images_array" name="images_array">
        </form>  
    </div>

    <div class="tab">
        <button id="default_button_two" type='button' style="margin-left: 0px;" class="tablinks2 active" onclick="wn_fe_openTab2(event, 'preview')">Content Preview</button>
        <button type='button_two' class="tablinks2" onclick="wn_fe_openTab2(event, 'preview2')">Html Code</button>    
    </div>
    <div id="preview" class="tabcontent2 default_box visual_preview "> 
        <br>                   
        <?php echo $embed['data']['cadena']; ?>
    </div>
    <div id="preview2" class="tabcontent2 default_box preview_html_code"> 
        <br>  
        <div class="copy_code_button_container">
            <div class="tooltip">
                <button class="button-primary copy_code_button" onclick="wn_flickr_embed_copy_to_cipboard()" onmouseout="wn_flickr_embed_outFunc()">
                    <span class="tooltiptext" id="myTooltip">Copy to clipboard</span>
                        Copy Code
                </button>
            </div>
        </div>                
        <textarea id="html_code" class="html_code" readonly><?php echo  esc_attr($embed['data']['cadena']); ?></textarea>
    </div>
</div>

<script>
    jQuery('#do_not_create_post').click(function(){
        if(jQuery('#do_not_create_post').prop('checked') == true) {
            jQuery('#create_new_post_page_div').css("display", "none");
            jQuery('#savechanges').val('Save');
            jQuery('#post_title').val("none");
            jQuery('#post_title_input').css("display", "none");

        } else {
            jQuery('#create_new_post_page_div').css("display", "block");
            jQuery('#savechanges').val('Save & Create Post');
            jQuery('#post_title').val("");
            jQuery('#post_title_input').css("display", "block");
        }
    })
    function wn_flickr_embed_copy_to_cipboard() {
        var copyText = document.getElementById("html_code");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");     
        var tooltip = document.getElementById("myTooltip");
        tooltip.innerHTML = "Copied";
    }

    function wn_flickr_embed_outFunc() {
        var tooltip = document.getElementById("myTooltip");
        tooltip.innerHTML = "Copy to clipboard";
    }

    function wn_fe_openTab(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
}

function wn_fe_openTab2(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent2");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks2");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
}

    var new_flipbox = <?php echo  json_encode($embed); ?>; 
    var objetos_imagenes;
    var photo_id;
    var photo_title;
    var photo_url;
    var photo_title_in_flickr;
    var flickr_info_url;
    var flickr_getsize_url;
    var flicr_info_url_clean;
    var owner_path;
    var owner_username;
    var license_types;
    var license_name;
    var license_url;
    var texts;
    var fetched = false;
    var skipped_licenses = 0;

    license_types = "https://api.flickr.com/services/rest/?method=flickr.photos.licenses.getInfo&api_key=<?php echo esc_attr($general_options['flickr_api_key']); ?>&&format=json&nojsoncallback=1";

    jQuery.getJSON(license_types, function(photo_license){        
        license_types = photo_license.licenses.license;
        license_types[license_types.findIndex(x => x.name == "All Rights Reserved")]['abb'] = 'All Rights Reserved';
        license_types[license_types.findIndex(x => x.name == "Attribution License")]['abb'] = 'CC BY 2.0';
        license_types[license_types.findIndex(x => x.name == "Attribution-NoDerivs License")]['abb'] = 'CC BY-ND 2.0';
        license_types[license_types.findIndex(x => x.name == "Attribution-NonCommercial-NoDerivs License")]['abb'] = 'CC BY-NC-ND 2.0';
        license_types[license_types.findIndex(x => x.name == "Attribution-NonCommercial License")]['abb'] = 'CC BY-NC 2.0';
        license_types[license_types.findIndex(x => x.name == "Attribution-NonCommercial-ShareAlike License")]['abb'] = 'CC BY-NC-SA 2.0';
        license_types[license_types.findIndex(x => x.name == "Attribution-ShareAlike License")]['abb'] = 'CC BY-SA 2.0';
        license_types[license_types.findIndex(x => x.name == "No known copyright restrictions")]['abb'] = 'Public Domain';
        license_types[license_types.findIndex(x => x.name == "United States Government Work")]['abb'] = 'United States Government Work';
        license_types[license_types.findIndex(x => x.name == "Public Domain Dedication (CC0)")]['abb'] = 'Public Domain';
        license_types[license_types.findIndex(x => x.name == "Public Domain Mark")]['abb'] = 'Public Domain';
    });

    //enabling and disabling create post button
    jQuery('#post_title, #image_tittle').on('input', function(){
        if(jQuery('#post_title').val() != '' && jQuery('#image_tittle').val() != '' && fetched == true) {
            jQuery('#savechanges').attr("type", "submit");
            jQuery('#savechanges').removeClass("deactivated_submit");
        }
        else {
            jQuery('#savechanges').attr("type", "button");
            jQuery('#savechanges').addClass("deactivated_submit");
        }
    });

    if(jQuery('#flickr_api_key').val() != "" && jQuery('#flickr_api_secret').val() != "") {
        jQuery('#fetch_photos').removeClass('deactivated_submit');
    } else {
        jQuery('#fetch_photos').addClass('deactivated_submit');
    }

    jQuery('#fetch_photos').click(function(){ 
        if (!jQuery('#fetch_photos').hasClass('deactivated_submit')) {
            objetos_imagenes = [];
            var lines = jQuery('#input_urls').val().split(/\n/);  
            texts = [];
            for (var i=0; i < lines.length; i++) {
                // only push this line if it contains a non whitespace character.
                if (/\S/.test(lines[i])) {
                    texts.push(jQuery.trim(lines[i]));
                };
            };
            wn_flickr_embed_get_info(license_types);
        }
        
    });    

    function wn_flickr_embed_get_info(license_types) {
        fetched = false;
        skipped_licenses = 0;
        jQuery('#fetch_log').html("");
        jQuery('#savechanges').attr("type", "button");
        jQuery('#savechanges').addClass("deactivated_submit");

        for (var i= 0; i < texts.length; i++) {

            var objeto_imagen = {
                url: "",
                photo_id: "",
                photo_title_in_flickr: "",
                id: "",
                photo_title: "",
                owner_username: "",
                owner_path: "",
                license_name: "",
                license_url: ""
            }

            
            objeto_imagen['url'] = texts[i];
            if (texts[i][texts[i].length - 1] == "/") {
                flicr_info_url_clean = texts[i].substring(0, texts[i].length - 1);            
            } else {
                flicr_info_url_clean = texts[i];
            };            

            photo_id = flicr_info_url_clean.substring(flicr_info_url_clean.lastIndexOf("/") + 1, flicr_info_url_clean.length);

            objeto_imagen['photo_id'] = photo_id;
            objeto_imagen['id'] = i + 1; //+1 Moving the index to start with 1

            flickr_info_url = "https://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=<?php echo esc_attr($general_options['flickr_api_key']); ?>&photo_id=" + photo_id + "&secret=<?php echo esc_attr($general_options['flickr_api_secret']); ?>&format=json&nojsoncallback=1";

            jQuery.ajax({
                type: 'GET',
                url: flickr_info_url,
                dataType: 'json',
                success: function(photo_info) {

                    if(photo_info.photo != undefined)
                    {                        
                        license_name = license_types[license_types.findIndex(x => x.id == photo_info.photo.license)].name;
                        license_abb = license_types[license_types.findIndex(x => x.id == photo_info.photo.license)].abb;

                        if(license_name != 'All Rights Reserved' && license_name != 'United States Government Work') {

                            license_url = license_types[license_types.findIndex(x => x.id == photo_info.photo.license)].url; 
                            owner_path = photo_info.photo.owner.path_alias;
                            owner_username = photo_info.photo.owner.username;
                            objeto_imagen['owner_username'] = owner_username;
                            objeto_imagen['photo_title_in_flickr'] = photo_info.photo.title._content;
                            objeto_imagen['owner_path'] = owner_path;
                            objeto_imagen['license_name'] = license_name;
                            objeto_imagen['license_abb'] = license_abb;
                            objeto_imagen['license_url'] = license_url;

                            jQuery('#fetch_log').html(jQuery('#fetch_log').html() + photo_id + ' --> ' + photo_info.stat + '--> license: ' + license_abb + '\n');

                            if(photo_info.stat == "ok" && fetched == false) {
                                fetched = true;
                                if(jQuery('#post_title').val() != "" && jQuery('#image_tittle').val() != "") {
                                    jQuery('#savechanges').attr("type", "submit");
                                    jQuery('#savechanges').removeClass("deactivated_submit");
                                };
                            }
                            objeto_imagen['id'] = i - skipped_licenses + 1; //+1 Moving the index to start with 1
                            photo_title = jQuery('#image_tittle').val() + (i - skipped_licenses + 1); //+1 Moving the index to start with 1
                            objeto_imagen['photo_title'] = photo_title;
                        }
                        else {
                            if(license_name == 'All Rights Reserved') {
                                jQuery('#fetch_log').html(jQuery('#fetch_log').html() + photo_id + " -->  SKIPPED --> (All rights reserved) \n");
                            } else {
                                jQuery('#fetch_log').html(jQuery('#fetch_log').html() + photo_id + " -->  SKIPPED --> (United States Government Work) \n");
                            }
                        }                        

                    } else {
                        jQuery('#fetch_log').html(jQuery('#fetch_log').html() + texts[i] + " --> Error!!! (Can't get the photo) \n");
                    }                  
                },
                data: {},
                async: false
            });

            flickr_getsize_url = "https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=<?php echo esc_attr($general_options['flickr_api_key']); ?>&secret=<?php echo esc_attr($general_options['flickr_api_secret']); ?>&photo_id=" + photo_id + "&format=json&nojsoncallback=1";

            jQuery.ajax({
                type: 'GET',
                url: flickr_getsize_url,
                dataType: 'json',
                success: function(photo_sizes) {
                    if(photo_sizes.sizes != undefined) {
                        if(objeto_imagen['license_name'] != 'All Rights Reserved' && objeto_imagen['license_name'] != 'United States Government Work'){
                        
                            if(photo_sizes.sizes.size[jQuery('#image_width').val()] != undefined) {
                                objeto_imagen['image_source'] = photo_sizes.sizes.size[jQuery('#image_width').val()].source;
                            }
                            else {
                                var depressor = jQuery('#image_width').val();
                                while(photo_sizes.sizes.size[depressor] == undefined) {
                                    depressor--;
                                };                           
                                objeto_imagen['image_source'] = photo_sizes.sizes.size[depressor].source;
                            }
                        }                        
                    }    
                },
                data: {},
                async: false
            });

            if(license_name != 'All Rights Reserved' && license_name != 'United States Government Work') {
                objetos_imagenes.push(objeto_imagen);
            }
            else {
                skipped_licenses++;
            }
        }
        var JSONobjeto_imagenes = JSON.stringify(objetos_imagenes);

        jQuery.ajax({
            url: '<?php echo admin_url('admin.php?page=wn_flickr_embed_db_handler'); ?>',
            type: 'POST',
            contentType: 'application/json',
            data: JSONobjeto_imagenes,
            dataType: 'json'
        });
    }
</script>

