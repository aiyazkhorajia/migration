<?php
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db(NEW_DB_NAME);
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>News</h1>";
$db_old = new Db("mt_main");
$old_movieid = array();
$result_old = $db_old->query("SELECT * FROM `dbo_brodbandmovies` WHERE type = '1' AND MovieId IN(21072)");
foreach($result_old as $result){
	$old_movieid[] = $result['MovieId'];
}
//pr($result_old); die;
$map_post_id = get_new_movieid_array($old_movieid);



$trailars = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModify",
    "post_date_gmt" => "",
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
    "post_modified" => "",
    "post_modified_gmt" => "",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "Title",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "BrodbandMovieId");


$trailars_main_default_values = array("ID" => "",
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
    "post_type" => "movie-trailer",
    "post_mime_type" => "",
    "comment_count" => "");

	$trailars_meta = array(
		"wpcf-movie-trailor-id" => "BrodbandMovieId",
		//"wpcf-artistes" => "1",
		"wpcf-keywords" => "Keywords",
		"wpcf-youtube-link" => "YouTubeLink",
		"wpcf-trailor-date" => "VideoDate",
		"wpcf-location" => "Location",
		"wpcf-credit" => "Credit",
		"wpcf-trailer-file-name" => "MovieVideoFileName",
		"_wpcf_belongs_movie_id" => "MovieId",
	);

	$trailars_meta_default_values = array(
	"wpcf-artistes" => ""
	);


foreach ($result_old as $key => $row) {
	
    $trailars_main = $trailars_main_default_values;
    $trailars_meta_default = $trailars_meta_default_values;
    $count = 0;
    $fields = '';
	
    foreach ($trailars as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case "post_name":
                    $trailars_main[$mapKey] = clean(strtolower($row[$mapValue]));
                    break;
				case "guid":
                    $trailars_main[$mapKey] = WEBSITE_URL ."trailers/".clean(strtolower($row[$mapValue]));
                    break;
                default;
                    $trailars_main[$mapKey] = $row[$mapValue];
                    break;
            }
        }
    }
    
    //pr($trailars_main, 1); die;
	
    foreach ($trailars_meta as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case '_wpcf_belongs_movie_id':
                    $row[$mapValue] = $map_post_id[$row['MovieId']];
                    break;
				case 'wpcf-trailor-date':
						$row[$mapValue] = strtotime($row[$mapValue]);
                    break;
				case 'wpcf-trailer-file-name':
						$row[$mapValue] = UPLOAD_DIR_URL .$row['MovieYearFolder'].'/'.$row['MovieNameFolder'].'/'.$row[$mapValue];
                    break;
                default:
                    break;
            }
            $trailars_meta_default[$mapKey] = $row[$mapValue];
        } else {
            $trailars_meta_default[$mapKey] = $trailars_main_default_values[$mapKey];
        }
    }

    $db_new->bindMore($trailars_main);
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($trailars_main)).") values (:".implode(",:", array_keys($trailars_main)).")";

    $result = $db_new->query($insert_artist);
	
    if($result > 0 ) {
		//print_r($row);
        $insert_id = $db_new->lastInsertId();
        echo "trailar inserted: ".$insert_id;
		if(!empty($row['MovieLargeImageFName']) && !empty($row['MovieYearFolder'] && $row['MovieNameFolder']) &&  $insert_id)
		{
			import_featured_image($row['MovieYearFolder'].'/'.$row['MovieNameFolder'].'/'.$row['MovieLargeImageFName'], $insert_id);
		}
        $field_value = array();
		$field_relation_value = array();

			$field_value = array();
			foreach($trailars_meta_default as $key_meta => $row_meta) {
				
				if( $key_meta == 'wpcf-artistes'){
					$old_artist_ids = get_brodband_artist_relation($row['BrodbandMovieId']); 
					$new_artist_ids = get_new_artist_id( $old_artist_ids, $type = 'array' );

					foreach($new_artist_ids as $id){
						insert_relation_meta_for_movie($insert_id, 'artistes', $id, 'movie-trailer');
					}
					$row_meta = implode(',', $new_artist_ids);
				}
			
				$field_value[] = "'".$insert_id."','".addslashes($key_meta)."','".addslashes($row_meta)."'";
			}
			//pr($field_value,1);
			$insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".implode("),(", $field_value).")";
			//echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta);
			echo ", Meta Inserted. <br>";
	}
    //exit;
}




exit;