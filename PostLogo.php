<?php
class PostLogo
{
    private static $postLogoVersion = "1.0b";
    private $table_name = 'postlogo';

    /**
     * constructor sets  up table_name
     */
    function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $this->table_name;
    }

    /**
     * installs the table
     * @return void
     */

    public function postLogoInstall()
    {
        $sql = "CREATE TABLE " . $this->table_name . " (
        id INT NOT NULL AUTO_INCREMENT,
        image_attachment_id INT NOT NULL,
        post_id INT NOT NULL,
        UNIQUE KEY id (id));";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        add_option("postlogoversion", $this->postLogoVersion);
    }

    /**
     * Checks the status of the plugins install
     *
     * @return ENUM InstallState
     */

    public function isInstalled()
    {
        $current_postlogo_version = get_option("postlogoversion");

        if ($current_postlogo_version == null) {
            return InstallState::NOT_INSTALLED;
        } else if ($current_postlogo_version < $this->postLogoVersion) {
            return InstallState::NOT_CURRENT;
        } else if ($current_postlogo_version == $this->postLogoVersion) {
            return InstallState::CURRENT_INSTALLED;
        }
    }

    /**
     * upgrades the database 
     * @return void
     */
    public function updgrade()
    {
        $sql = "CREATE TABLE $this->table_name (
        id INT NOT NULL AUTO_INCREMENT,
        image_attachment_id INT NOT NULL,
        post_id INT NOT NULL ";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option("postlogoversion", $postLogoVersion);
    }

    /**
     * Add logo or update if it already exists
     * @param  $imageId
     * @param  $postId
     * @return void
     */

    public function addPostLogo($imageId, $postId)
    {
//        echo "new image postid =".$postId." imageid=".$imageId;
        global $wpdb;
        $sql = "SELECT id AS postlogoid FROM $this->table_name WHERE post_id = $postId";
        $res = $wpdb->get_row($sql,ARRAY_A);
        $postLogoId = -1;
        if($res['postlogoid'] != null) {
            $postLogoId = $res['postlogoid'];
        }
        if ($postLogoId != -1) {
            $wpdb->update($this->table_name,
                          array('image_attachment_id' => $imageId,
                               'post_id' => $postId),
                          array('id' => $postLogoId));
        } else {

            $wpdb->insert($this->table_name,
                          array('image_attachment_id' => $imageId,
                               'post_id' => $postId));
        }
    }

    /**
     * gets the postlogo id for a specified post
     * @param  $postId
     * @return postlogoid
     */

    public function getPostLogo($postId)
    {
        global $wpdb;
        $sql ="SELECT image_attachment_id AS image FROM $this->table_name WHERE post_id = $postId;";
        $res = $wpdb->get_row($sql, ARRAY_A);
        return $res['image'];
    }
    /**
     * gets the postlogo image filename for a specified post
     * @param  $postId
     * @return postlogo image path
     */

    public function getPostLogoFilename($postId)
    {
        global $wpdb;
        $sql ="SELECT post.guid AS imagepath FROM $this->table_name AS postlogo
        INNER JOIN ". $wpdb->prefix."posts AS post ON post.id = postlogo.image_attachment_id
        WHERE post_id = $postId;";
        $res = $wpdb->get_row($sql, ARRAY_A);
        return $res['imagepath'];
    }

    /**********
     *  not needed in 1.1b
     * gets the post id given a image path
     * post id because WP stores images as a post of post_type attachment
     * @param  $imagepath
     * @return image id (postid)
     *
    public function getImageId($imagepath)
    {
        global $wpdb;
        global $wplogger;
        $filenameParts = pathinfo($imagepath);
        print_r($filenameParts);
        echo $filenameParts['extension'];
        $rpstr = ".".$filenameParts['extension'];
        echo "replace".$rpstr;
        $imagepath2 = str_replace($rpstr,"", $imagepath);
        echo "img path".$imagepath2 ;
        $sql = "SELECT id AS imageid FROM " . $wpdb->prefix . "posts WHERE post_type = 'attachment' AND guid LIKE '$imagepath';";
        $res = $wpdb->get_row($sql,ARRAY_A);
        return $res['imageid'];
    } ***********/
}

class InstallState
{
    const NOT_INSTALLED = 1; // no postlogo table
    const NOT_CURRENT = 2; // out of data postlogo table
    const CURRENT_INSTALLED = 1; //up to date post logo table
}

