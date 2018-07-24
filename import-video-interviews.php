<?php
require("db/Db.class.php");
include_once("common-function.php");

// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db(NEW_DB_NAME);
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Video News</h1>";
$db_old = new Db("mt_main");
//$result_old = $db_old->query("SELECT * FROM `dbo_newsmaster` WHERE NewsId = '20224'");

$result_old = $db_old->query("SELECT * FROM `dbo_brodbandmovies` WHERE `Type` = '3' LIMIT 5");


 foreach($result_old as $result){
	$old_movieid[] = $result['MovieId'];
}
//pr($result_old); die();
$map_post_id = get_new_movieid_array($old_movieid);
//pr($map_post_id); die;
$movies = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModify",
    "post_date_gmt" => "LastModify",
    "post_content" => "Description",
    "post_title" => "Title",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "Title",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModify",
    "post_modified_gmt" => "LastModify",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "Title",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "BrodbandMovieId");


$movies_main_default_values = array("ID" => "",
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
    "post_type" => "movie-interview",
    "post_mime_type" => "",
    "comment_count" => "");

	$movies_meta = array(
		"old-id" => "BrodbandMovieId",
		"wpcf-movie-id-for-movie-interview" => "",  // need to check
		
        "wpcf-interview-file-name" => "MovieVideoFileName",
		"wpcf-youtube-link-for-movie-interview" => "YouTubeLink",
		//"wpcf-artistes-id-for-movie-interview" => "ArtistIds",
		"wpcf-keywords-for-movie-interview" => "Keywords",
		"wpcf-location-for-movie-interview" => "Location",
		"wpcf-credit-for-movie-interview" => "Credit",
		"wpcf-date-for-movie-interview" => "ViewDate",
		"videoint_views_count" => "ViewCount",
		//"_wpcf_belongs_movie_id" => "MovieId",
		
	);

	$movies_meta_default_values = array("wpcf-artistes-id-for-movie-interview" => "");


foreach ($result_old as $key => $row) {
	
    $movies_main = $movies_main_default_values;
    $movies_meta_default = $movies_meta_default_values;
    $count = 0;
    $fields = '';
	
    foreach ($movies as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case "post_name":
                    $movies_main[$mapKey] = clean(strtolower($row[$mapValue]));
                    break;
                case "guid":
                    $movies_main[$mapKey] = WEBSITE_URL .'videos/interviews/'. clean(strtolower($row[$mapValue]));
                    break;
                default;
                    $movies_main[$mapKey] = $row[$mapValue];
                    break;
            }
        }
    }
    
    //pr($movies_main, 1); die;
    foreach ($movies_meta as $mapKey => $mapValue) {
        if (!empty($mapKey)) {
			//echo $mapValue.'<br>';
            switch ($mapKey) {
                case 'wpcf-interview-file-name':
                        $row[$mapValue] = UPLOAD_DIR_URL .''.$row['MovieYearFolder'] .'/'. $row['MovieNameFolder'] .'/'. $row['MovieVideoFileName'];
                    break;
                case 'wpcf-date-for-movie-interview':
                                $row[$mapValue] = strtotime($row[$mapValue]);
                    break;
                /* case '_wpcf_belongs_movie_id':
                                $row[$mapValue] = $map_post_id[$row[$mapValue]];
                    break; */
                default:
                    break;
            }
            $movies_meta_default[$mapKey] = $row[$mapValue];
        } else {
            $movies_meta_default[$mapKey] = $movies_main_default_values[$mapKey];
        }
    }

    /* ================= */
	//echo "INSERT INTO m8t7_posts (".implode(",", array_keys($movies_main)).") values (:".implode(",:", array_keys($movies_main)).")" ; die;
    $db_new->bindMore($movies_main);
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($movies_main)).") values (:".implode(",:", array_keys($movies_main)).")";
    $result = $db_new->query($insert_artist);

	
    if($result > 0 ) {
		//print_r($row);
        $insert_id = $db_new->lastInsertId();
        echo "<br> Inserted Video Interview id: ".$insert_id;

		if(!empty($row['MovieImageFileName']) && $insert_id)
		{
			import_featured_image(UPLOAD_DIR_URL .''.$row['MovieYearFolder'] .'/'. $row['MovieNameFolder'] .'/'. $row['MovieImageFileName'], $insert_id);
		}
        $field_value = array();
		$field_relation_value = array();

			$field_value = array();
			foreach($movies_meta_default as $key_meta => $row_meta) {

				if( $key_meta == 'wpcf-artistes-id-for-movie-interview'){
					$old_artist_ids = get_brodband_artist_relation($row['BrodbandMovieId']); 
					//pr($old_artist_ids); die;
					$new_artist_ids = get_new_artist_id( $old_artist_ids, $type = 'array' );

					foreach($new_artist_ids as $id){
						insert_relation_meta_for_movie($insert_id, 'artistes-id-for-movie-interview', $id, 'movie-interview');
					}
					$row_meta = implode(',', $new_artist_ids);
				}
				
				$field_value[] = "'".$insert_id."','".addslashes($key_meta)."','".addslashes($row_meta)."'";
			}

			$insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".implode("),(", $field_value).")";
			//echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta);
			echo ", Meta Inserted. <br>";
	}
    //exit; 
}
exit;
