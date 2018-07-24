<?php
// Artist Trivia
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Users</h1>";
$db_new = new Db(NEW_DB_NAME);

echo "<hr><br><h1>Artist Broadband Comments</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT * FROM dbo_artistetrivia order by ArtisteTriviaId ");
//pr($result_old,0);

$artist = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModifyDate",
    "post_date_gmt" => "LastModifyDate",
    "post_content" => "ArtisteTriviaText",
    "post_title" => "",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModifyDate",
    "post_modified_gmt" => "LastModifyDate",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "ArtisteTriviaId");


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
    "post_type" => "artiste-trivia",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "");

$artist_meta = array("_wpcf_belongs_artiste_id" => "");
$artist_meta_default_values = array("_wpcf_belongs_artiste_id" => "");

foreach ($result_old as $key => $row) {
    $artist_main = $artist_main_default_values;
    $artist_meta_default = $artist_meta_default_values;
    
    foreach ($artist as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            //echo "<br>mapValue ".$mapValue;
            if($mapValue == 'LastModifyDate' && $row[$mapValue] == "")
            {
                $row[$mapValue] = date("Y-m-d H:i:s");
            }
            //echo " -- rowValue ".$row[$mapValue];
            $artist_main[$mapKey] = $row[$mapValue];
        }
    }
  
    //pr($artist_main, 0);
    $db_new->bindMore($artist_main);
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($artist_main)).") values (:".implode(",:", array_keys($artist_main)).")";
    //echo "<br>".$insert_artist; 
    $result = $db_new->query($insert_artist);
    //exit;
    //$result = 1;
    
    if($result > 0 ) {
        $insert_id = $db_new->lastInsertId();
        echo "<br> new post id ".$insert_id. " == artist id ".$row['ArtistId'];
        
        //pr($field_value,1);
        $insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values ('".$insert_id."','_wpcf_belongs_artiste_id','".$row['ArtistId']."')";
        //echo "<br>".$insert_artist_meta; //exit;
        $result_meta = $db_new->query($insert_artist_meta);
    }
    //exit;
}


exit;


/*
 * 
 * After execution above script below query needs to be updated
 * update m8t7_posts set guid = concat("http://localhost/movietalkies/?post_type=artiste-trivia&#038;p=",ID) where post_type = 'artiste-trivia'
 * 
 * 
 * select query to get artist name
 * select p1.post_title, p.post_content,pm.meta_value artist_id from m8t7_posts p
inner join m8t7_postmeta pm on p.ID = pm.post_id and pm.meta_key = '_wpcf_belongs_artiste_id'
inner join m8t7_posts p1 on p1.id = pm.meta_value and p1.post_type = 'artiste' 
where p.post_type = 'artiste-trivia'
 * 
 * 
 * Update query to update post_title and post_name of all artiste-trivia
 * update  m8t7_posts p
inner join m8t7_postmeta pm on p.ID = pm.post_id and pm.meta_key = '_wpcf_belongs_artiste_id'
inner join m8t7_posts p1 on p1.id = pm.meta_value and p1.post_type = 'artiste' 
set p.post_title = concat(p1.post_title,' - trivia'),
    p.post_name = concat(replace(p1.post_title,' ','-'),'-trivia')
where p.post_type = 'artiste-trivia';
 */