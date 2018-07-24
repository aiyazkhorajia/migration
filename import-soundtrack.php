<?php
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db("movie_local");
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new); die();

echo "<hr><br><h1>News</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT album.MovieId, album.ThumbPath, album.LastModify,  songs.* FROM `dbo_moviealbummaster` as album 
RIGHT JOIN `dbo_musicclipsmaster` as songs ON album.MovieID = songs.MovieID WHERE songs.MovieID = '20739

'");

/* foreach($result_old as $result){
	$old_movieid[] = $result['MovieId'];
}

$map_post_id = get_new_movieid_array($old_movieid); */


$album = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModified",
    "post_date_gmt" => "LastModified",
    "post_content" => "Lyrics",
    "post_title" => "SongName",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "SongName",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModified",
    "post_modified_gmt" => "LastModified",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "AlbumId");


$album_main_default_values = array("ID" => "",
    "post_author" => "",
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
    "post_type" => "music-clips",
    "post_mime_type" => "",
    "comment_count" => "");

	$album_meta = array(
		"wpcf-music-clips-id" => "CriticName",
		"wpcf-song-file-name" => "CriticRating",
		"wpcf-playback-singers" => "ReviewType",
		"wpcf-lyricists" => "ReviewType",
		"wpcf-music-directors" => "ReviewType",
		"wpcf-music-companys" => "ReviewType",
		"wpcf-track-runtime" => "ReviewType",
		"wpcf-date-of-releases" => "ReviewType",
		"wpcf-download-link" => "ReviewType",
		"wpcf-itune-link-s" => "ReviewType",
		"wpcf-google-play-links" => "ReviewType",
		"wpcf-amazon-links" => "ReviewType",
		"wpcf-ganna-links" => "ReviewType",
		"wpcf-video-image" => "ReviewType",
		"wpcf-video-file-name" => "ReviewType",
		"wpcf-youtube-links" => "ReviewType",
		"_wpcf_belongs_movie_id" => "ReviewType",
	);

	$album_meta_default_values = array();


foreach ($result_old as $key => $row) {
	
    $album_main = $album_main_default_values;
    $album_meta_default = $album_meta_default_values;
    $count = 0;
    $fields = '';
	//pr($album_main); die;
    foreach ($album as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case "post_name":
                    $album_main[$mapKey] = clean(strtolower($row[$mapValue]));
                    break;
                default;
                    $album_main[$mapKey] = $row[$mapValue];
                    break;
            }
        }
    }
    
    //pr($album_main, 1); die;
    foreach ($album_meta as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
			//echo $mapValue.'<br>';
            switch ($mapKey) {
                case 'wpcf-critic-review-type':
					$row[$mapValue] = 1;
                    break;
				case 'wpcf-movieid-criticreviews':
					$row[$mapValue] = $map_post_id[$row['MovieId']];
                    break;
				case 'wpcf-movieid-criticreviews':
					$row[$mapValue] = 1;
                    break;
                default:
                    break;
            }
            $album_meta_default[$mapKey] = $row[$mapValue];
        } else {
            $album_meta_default[$mapKey] = $album_main_default_values[$mapKey];
        }
    }

    /* ================= */
    $db_new->bindMore($album_main);
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($album_main)).") values (:".implode(",:", array_keys($album_main)).")";

    $result = $db_new->query($insert_artist);
	
    if($result > 0 ) {
		//print_r($row);
        $insert_id = $db_new->lastInsertId();
        echo "<br> Inserted News id: ".$insert_id;

        $field_value = array();
		$field_relation_value = array();

			$field_value = array();
			foreach($album_meta_default as $key_meta => $row_meta) {
				$field_value[] = "'".$insert_id."','".addslashes($key_meta)."','".addslashes($row_meta)."'";
			}
			//pr($field_value,1);
			$insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".implode("),(", $field_value).")";
			$result_meta = $db_new->query($insert_artist_meta);
			echo ", Meta Inserted. <br>";
	}

}


function get_music_clip_artist( $MusicClipId, $type ){
	
	global $db_old;
	$result_old = $db_old->query("SELECT * FROM `dbo_MusicClipsArtistRelation` WHERE `MusicClipId` = '{$MusicClipId}' AND `ArtistType` = '{$type}'");
	pr($result_old); die;
}