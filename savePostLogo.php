<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ant
 * Date: 11/03/11
 * Time: 22:52
 * To change this template use File | Settings | File Templates.
 */

include_once('PostLogo.php');
$postLogo = new PostLogo();
$pid = $_GET['post_id'];
$filename = $_GET['imagepath'];
echo $filename;
$imageid = $postLogo->getImageId($filename);

$postLogo->addPostLogo($imageid, $pid);