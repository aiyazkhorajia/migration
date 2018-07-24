<?php
echo " start time ". date('Y-m-d H:i:s') ."<br>";
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
echo "<h1>Artist</h1>";

$db_new = new Db(NEW_DB_NAME);
$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Artist Broadband Comments</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT * FROM dbo_artistmaster order by ArtistId limit 70001, 20000");
//pr($result_old,0);

$artist = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModifyDate",
    "post_date_gmt" => "LastModifyDate",
    "post_content" => "Profile",
    "post_title" => "ArtistName",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "ArtistName",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModifyDate",
    "post_modified_gmt" => "LastModifyDate",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "ArtistName",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "ArtistId");


$artist_main_default_values = array("ID" => "",
    "post_author" => "1",
    "post_date" => "",
    "post_date_gmt" => "",
    "post_content" => "",
    "post_title" => "",
    "post_status" => "publish",
    "post_excerpt" => "",
    "comment_status" => "closed",
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
    "post_type" => "artiste",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "");

$artist_meta = array("wpcf-artistid" => "ArtistId",
    "wpcf-artist-also-known-as" => "AlsoKnowAs",
    "wpcf-date-of-birth" => "DOB",
    "wpcf-place-of-birth" => "Placeofbirth",
    "wpcf-date-of-death" => "DOD",
    "wpcf-official-web-site" => "OfficialSite",
    "wpcf-official-facebook" => "OfficialFacebook",
    "wpcf-official-twitter" => "OfficialTwitter",
    "wpcf-official-youtube" => "OfficialYouTube",
    "wpcf-official-blog" => "OfficialBlog",
    "wpcf-official-google-plus" => "OfficialGPlus",
    "wpcf-official-instagram" => "OfficialInstagram",
    "wpcf-official-tumblr" => "OfficialTumblr",
    "wpcf-place-of-death" => "PlaceofDeath",
    "wpcf-artist-language" => "Language",
    "wpcf-gender" => "Gender",
    "wpcf-nationality" => "Nationality",
    "wpcf-marital-status" => "MaritalStatus",
    "wpcf-search-view-count" => "SearchViewCount",
    "wpcf-search-keywords" => "ASearchKeywords");

$artist_meta_default_values = array("wpcf-artistid" => "",
    "wpcf-artist-also-known-as" => "",
    "wpcf-date-of-birth" => "",
    "wpcf-place-of-birth" => "",
    "wpcf-date-of-death" => "",
    "wpcf-official-web-site" => "",
    "wpcf-official-facebook" => "",
    "wpcf-official-twitter" => "",
    "wpcf-official-youtube" => "",
    "wpcf-official-blog" => "",
    "wpcf-official-google-plus" => "",
    "wpcf-official-instagram" => "",
    "wpcf-official-tumblr" => "",
    "wpcf-place-of-death" => "",
    "wpcf-artist-language" => "",
    "wpcf-gender" => "",
    "wpcf-nationality" => "",
    "wpcf-marital-status" => "");


foreach ($result_old as $key => $row) {
    $artist_main = $artist_main_default_values;
    $artist_meta_default = $artist_meta_default_values;
    $count = 0;
    $fields = '';
    foreach ($artist as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case "post_name":
                    $artist_main[$mapKey] = clean(strtolower($row[$mapValue]));
                    break;
                case "guid":
                    $artist_main[$mapKey] = "http://localhost/movietalkies/artiste/".clean(strtolower($row[$mapValue]));
                    break;
                default;
                    $artist_main[$mapKey] = $row[$mapValue];
                    break;
            }
        }
    }
    
    //pr($artist_main, 1);
    foreach ($artist_meta as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            switch ($mapValue) {
                case 'wpcf-date-of-birth':
                case 'wpcf-date-of-death':
                    $row[$mapValue] = strtotime($row[$mapValue]);
                    break;
                default:
                    break;
            }
            $artist_meta_default[$mapKey] = $row[$mapValue];
        } else {
            $artist_meta_default[$mapKey] = $artist_meta_default_values[$mapKey];
        }
    }

    //pr($artist_meta_default, 0);
    
    /* ================= */
    $db_new->bindMore($artist_main);
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($artist_main)).") values (:".implode(",:", array_keys($artist_main)).")";
    //echo $insert_artist; exit;
    $result = $db_new->query($insert_artist);
    //exit;
    //$result = 1;
    if($result > 0 ) {
        $insert_id = $db_new->lastInsertId();
        echo "<br> artist id ".$insert_id;
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
echo "<br> end time ". date('Y-m-d H:i:s') ."\n";
exit;