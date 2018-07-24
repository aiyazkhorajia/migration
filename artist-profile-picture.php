<?php
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Users</h1>";
$db_new = new Db(NEW_DB_NAME);
$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><hr><br><h1>Artist Broadband Comments</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT * FROM dbo_artistprofilepicture order by ArtistId limit 1, 20 ");
//pr($result_old,0);

$artist = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModify",
    "post_date_gmt" => "LastModify",
    "post_content" => "",
    "post_title" => "ArtistFileName",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "ArtistFileName",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModify",
    "post_modified_gmt" => "LastModify",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "ArtistFolderName",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "ArtistProfilePictureId");


$artist_main_default_values = array("ID" => "",
    "post_author" => "1",
    "post_date" => "",
    "post_date_gmt" => "",
    "post_content" => "",
    "post_title" => "",
    "post_status" => "inherit",
    "post_excerpt" => "",
    "comment_status" => "open",
    "ping_status" => "closed",
    "post_password" => "",
    "post_name" => "",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "",
    "post_modified_gmt" => "",
    "post_content_filtered" => "",
    "post_parent" => "0",
    "guid" => "",
    "menu_order" => "0",
    "post_type" => "attachment",
    "post_mime_type" => "image/jpeg",
    "comment_count" => "",
    "old_id" => "");

$artist_meta = array("_wp_attached_file" => "",
    "_wp_attachment_metadata" => "",
    "_wp_attachment_image_alt" => "");

$artist_meta_default_values = array("_wp_attached_file" => "ArtistFolderName",
    "_wp_attachment_metadata" => "",
    "_wp_attachment_image_alt" => "");
/*
$mimeType = generateUpToDateMimeArray(APACHE_MIME_TYPES_URL);

echo $mimeType['123'];
pr($mimeType,1);
 */
foreach ($result_old as $key => $row) {
    $artist_main = $artist_main_default_values;
    $artist_meta_default = $artist_meta_default_values;
    $count = 0;
    $fields = '';
      
    $fullImgPath = "http://media.movietalkies.com/profilepicture/".$row["ArtistFolderName"]."/".$row["ArtistFileName"];
    $mimetype = image_type_to_mime_type(exif_imagetype($fullImgPath));   
    $ext = substr($row["ArtistFileName"], -3);
    
    foreach ($artist as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case "post_name":
                    $artist_main[$mapKey] = clean(strtolower(substr($row["ArtistFileName"],0, -4)));
                    break;
                case "post_title":
                    $artist_main[$mapKey] = substr($row["ArtistFileName"],0, -4);
                    break;
                case "guid":
                    $artist_main[$mapKey] = "http://localhost/movietalkies/wp-content/uploads/profilepicture/".$row["ArtistFolderName"]."/".$row["ArtistFileName"];
                    break;
                case "post_mime_type":
                    $artist_main[$mapKey] = $mimetype;
                    break;
                
                default;
                    $artist_main[$mapKey] = $row[$mapValue];
                    break;
            }
        }
    }
  
    //pr($artist_main, 0);
    
    $attachment_metadata = Array(
    "width" => 2880,
    "height" => 7404,
    "file" => "profilepicture/".$row["ArtistFolderName"]."/".$row["ArtistFileName"],
    "sizes" => Array
        (
            "thumbnail" => Array
                (
                    "file" => $row["ArtistFileName"],
                    "width" => 150,
                    "height" => 150,
                    "mime-type" => $mimetype,
                ),
            "post-thumbnail" => Array
                (
                    "file" => $row["ArtistFileName"],
                    "width" => 500,
                    "height" => 545,
                    "mime-type" => $mimetype,
                )
        ),
    "image_meta" => Array(
            "aperture" => 0,
            "credit" => "",
            "camera" => "",
            "caption" => "",
            "created_timestamp" => 0,
            "copyright" => "",
            "focal_length" => 0,
            "iso" => 0,
            "shutter_speed" => 0,
            "title" => "",
            "orientation" => 0,
            "keywords" => Array()
        ));
    foreach ($artist_meta as $mapKey => $mapValue) {
        if (empty($mapValue)) {
            
            switch ($mapKey) {
                case '_wp_attached_file':
                    $row[$mapValue] =  "profilepicture/".$row["ArtistFolderName"]."/".$row["ArtistFileName"];
                    break;
                case '_wp_attachment_metadata':
                    $row[$mapValue] = serialize($attachment_metadata);
                    break;
                case '_wp_attachment_image_alt':
                    $row[$mapValue] = substr($row["ArtistFileName"],0, -4);
                    break;
                
                default:
                    break;
            }
            $artist_meta_default[$mapKey] = $row[$mapValue];
            
        } else {
            $artist_meta_default[$mapKey] = $artist_meta_default_values[$mapKey];
        }
    }
//pr($artist_meta_default,0);
    //pr($artist_meta_default, 0);
    
    /* ================= */
    $db_new->bindMore($artist_main);
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($artist_main)).") values (:".implode(",:", array_keys($artist_main)).")";
    //echo "<br>".$insert_artist; 
    $result = $db_new->query($insert_artist);
    //exit;
    //$result = 1;
    
    
    
    if($result > 0 ) {
        $insert_id = $db_new->lastInsertId();
        echo "<br> new post id ".$insert_id. " == artist id ".$row['ArtistId'];
        $result_post_id = $db_new->query("SELECT ID FROM m8t7_posts where post_type = 'artiste' and old_id = ".$row['ArtistId']." limit 1");
        //pr($result_post_id,0);
        $post_artist_id = $result_post_id[0]['ID'];
        $insert_artist = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values ('".$post_artist_id."','_thumbnail_id','".$insert_id."')";
        //echo $insert_artist; exit;
        $result = $db_new->query($insert_artist);
    
        $field_value = array();
        foreach($artist_meta_default as $key_meta => $row_meta) {
            $field_value[] = "'".$insert_id."','".addslashes($key_meta)."','".addslashes($row_meta)."'";
        }
        //pr($field_value,1);
        $insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".implode("),(", $field_value).")";
        //echo "<br>".$insert_artist_meta; exit;
        $result_meta = $db_new->query($insert_artist_meta);
    }
    //exit;
}


exit;
