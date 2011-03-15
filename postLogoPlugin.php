<?php
/*
Plugin Name: Post Logo
Plugin URI: http://antgrimmitt.co.uk/blog/wordpress-plugin-post-logo
Description: This allows a user to a single "logo" image to a post using an existing image or a new image
Version: 1.1b
Author: Ant Grimmitt
Author URI: http://antgrimmitt.co.uk/
License: GPL2
*/

/*  Copyright 2011  Ant Grimmitt (email : ant@antgrimmitt.co.uk)
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

include_once('PostLogo.php'); // PostLogo class, InstallState class
/* hook up the install method */
register_activation_hook(__FILE__, 'runInstall');
add_action('admin_init', 'addMetaBox', 1);



function runInstall()
{
    $postLogo = new PostLogo();

    switch ($postLogo->isInstalled()) {
        case InstallState::NOT_INSTALLED:
            $postLogo->postLogoInstall();
            break;
        case InstallState::NOT_CURRENT:
            $postLogo->updgrade();
            break;
        case InstallState::CURRENT_INSTALLED:
            //all ok here nothing to do
            break;
        default:
            //do nothing
            break;
    }
}


/**
 * adds ui to the edit/add post area
 * @return void
 */function addMetaBox()
{
    add_meta_box("postlogo", "Add Post Logo", 'addUI', 'post');
}

/**
 * UI components html and js
 * @return void
 */
function AddUI()
{
    $postLogo = new PostLogo();
    $imgpath = $postLogo->getPostLogoFilename(get_the_ID());
    ?>
<tr valign="top">
    <td>
        <p>Select an image then hit save!</p>
    </td>
    <td><label for="upload_image">
        <input id="imagepostid" type="hidden"  value="0"/>
        <input id="upload_image" type="text" size="36" name="upload_image" value="<?php echo $imgpath ?>"/>
        <input id="upload_image_button" type="button" class="button" value="Select Image"/>
        <input id="save_button" class="button-primary" type="button" value="Save"/>
        <br/><span id="results"></span>
        <br/><span id="urlsave"></span>
    </label></td>
    <td valign="top">Current logo:</td>
    <td class="the_post_image"><?php the_post_logo() ?></td>
</tr>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#upload_image_button').click(function() {
            formfield = jQuery('#upload_image').attr('name');
            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
            return false;
        });
        window.send_to_editor = function(html) {
            var classes = jQuery('img',html).attr('class');
            var items = classes.split(" ");
            String.prototype.startsWith = function(str){
                return (this.indexOf(str) === 0);
            }
            var imagepostid = null;
            for(var i =0; i < items.length; i++) {
                var item = items[i];
                if(item.startsWith("wp-image-")) {
                    imagepostid = item.split("-")[2];
                    break;
                }
            }
            jQuery('#imagepostid').val(imagepostid);
            imgurl = jQuery('img', html).attr('src');
            jQuery('#upload_image').val(imgurl);
            tb_remove();
        }
    });
</script>
<?php
}
add_action('admin_head', 'save_post_logo');
function save_post_logo()
{

    $_ajax = admin_url('admin-ajax.php'); //set ajax url
    ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#save_button').click(function() {
            var data = {
                action: 'save_post_logo',
//                filename: jQuery('#upload_image').val(),
                imagepostid: jQuery('#imagepostid').val(),
                postid: '<?php echo get_the_ID(); ?>'
            };
            jQuery.post('<?php echo $_ajax; ?>', data, function(response) {
                console.log('Got this from the server: ' + response);

            });
        });
    });
</script>
<?php
}

//add ajax method to WP
add_action('wp_ajax_save_post_logo', 'post_logo_callback');

//ajax callback method
function post_logo_callback()
{
    global $wpdb; // this is how you get access to the database
//    $filename = $_POST['filename'];
    $imagepostid = $_POST['imagepostid'];
    $postid = $_POST['postid'];
    $postLogo = new PostLogo();
    /**    $fileid = $postLogo->getImageId($filename); - not needed
     as I'm getting the image post id from the class attribute of the img return by media library**/
    $postLogo->addPostLogo($imagepostid, $postid);
    echo "success";
    die(); // this is required to return a proper result
}

/**
 * prints the img tags with desired content0
 * @return void
 */
function the_post_logo($size) {
    $postlogo = new PostLogo();
    print wp_get_attachment_image($postlogo->getPostLogo(get_the_ID()),$size);
}

/**
 * Retrieves the postlogo id
 * @param  $postid
 * @return string
 */

function getPostLogo($postid){
    $postlogo = new PostLogo();
    return  wp_get_attachment_image($postlogo->getPostLogo($postid));
}

?>