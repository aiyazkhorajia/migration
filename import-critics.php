<?php
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db(NEW_DB_NAME);
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new); die();

echo "<hr><br><h1>News</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT * FROM `dbo_criticmaster` WHERE CriticReviewId = '256'");

foreach($result_old as $result){
	$old_movieid[] = $result['MovieId'];
}

$map_post_id = get_new_movieid_array($old_movieid);
//pr($map_post_id); die;
$movies = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModify",
    "post_date_gmt" => "LastModify",
    "post_content" => "CriticReview",
    "post_title" => "ReviewTitle",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "ReviewTitle",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModify",
    "post_modified_gmt" => "LastModify",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "ReviewTitle",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "CriticReviewId");


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
    "post_type" => "critic-reviews",
    "post_mime_type" => "",
    "comment_count" => "");

	$movies_meta = array(
		"wpcf-critic-name" => "CriticName",
		"wpcf-critic-rating" => "CriticRating",
		"wpcf-critic-review-type" => "ReviewType",
		"wpcf-critic-link" => "CriticLink",
		"wpcf-movieid-criticreviews" => "MovieId",
	);

	$movies_meta_default_values = array();


foreach ($result_old as $key => $row) {
	
    $movies_main = $movies_main_default_values;
    $movies_meta_default = $movies_meta_default_values;
    $count = 0;
    $fields = '';
	//pr($movies_main); die;
    foreach ($movies as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case "post_name":
                    $movies_main[$mapKey] = clean(strtolower($row[$mapValue]));
                    break;
				case "guid":
                    $movies_main[$mapKey] = WEBSITE_URL .'critic-reviews/'. clean(strtolower($row[$mapValue]));
                    break;
                default;
                    $movies_main[$mapKey] = $row[$mapValue];
                    break;
            }
        }
    }
    
    //pr($movies_main, 1); die;
    foreach ($movies_meta as $mapKey => $mapValue) {
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
            $movies_meta_default[$mapKey] = $row[$mapValue];
        } else {
            $movies_meta_default[$mapKey] = $movies_main_default_values[$mapKey];
        }
    }

    /* ================= */
    $db_new->bindMore($movies_main);
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($movies_main)).") values (:".implode(",:", array_keys($movies_main)).")";

    $result = $db_new->query($insert_artist);
	
    if($result > 0 ) {
		//print_r($row);
        $insert_id = $db_new->lastInsertId();
        echo "<br> Inserted News id: ".$insert_id;

        $field_value = array();
		$field_relation_value = array();

			$field_value = array();
			foreach($movies_meta_default as $key_meta => $row_meta) {
				$field_value[] = "'".$insert_id."','".addslashes($key_meta)."','".addslashes($row_meta)."'";
			}
			//pr($field_value,1);
			$insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".implode("),(", $field_value).")";
			$result_meta = $db_new->query($insert_artist_meta);
			echo ", Meta Inserted. <br>";
	}

}