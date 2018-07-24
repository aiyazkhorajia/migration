<?php
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db(NEW_DB_NAME);
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Movies Review</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT * FROM `dbo_editorreviewmaster` WHERE MovieId = '21286'");

foreach($result_old as $result){
	$old_movieid[] = $result['MovieId'];
}

$map_post_id = get_new_movieid_array($old_movieid);
//pr($result_old); die;
$movies = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModifyDate",
    "post_date_gmt" => "LastModifyDate",
    "post_content" => "Review",
    "post_title" => "Title",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "Title",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModifyDate",
    "post_modified_gmt" => "LastModifyDate",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "Title",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "ReviewId");


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
    "post_type" => "movie-reviews",
    "post_mime_type" => "",
    "comment_count" => "");

	$movies_meta = array(
		"wpcf-movie-rating" => "Rate",
		"wpcf-movie-review-date" => "ReviewDate",
		"wpcf-movieid-review" => "MovieId",
		"wpcf-artistid-movie-review" => "ArtistIds",
		"wpcf-current-movie-review" => "CR",
		"wpcf-review-view-count" => "ReviewViewCount",
	);

	$movies_meta_default_values = array();


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
                case "post_content":
                    $movies_main[$mapKey] = review_merged_content($row);
                    break;
				case "guid":
                    $movies_main[$mapKey] = WEBSITE_URL .'movies-reviews/'. clean(strtolower($row[$mapValue]));
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

            switch ($mapKey) {
                case 'wpcf-movie-review-date':
					$row[$mapValue] = strtotime($row[$mapValue]);
                    break;
				case 'wpcf-movieid-review':
					$row[$mapValue] = $map_post_id[$row[$mapValue]];
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
//echo "INSERT INTO m8t7_posts (".implode(",", array_keys($movies_main)).") values (:".implode(",:", array_keys($movies_main)).")" ; die;
    $db_new->bindMore($movies_main);
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($movies_main)).") values (:".implode(",:", array_keys($movies_main)).")";

    $result = $db_new->query($insert_artist);

    if($result > 0 ) {
        $insert_id = $db_new->lastInsertId();
        echo "<br> Inserted review id: ".$insert_id;
		
		if(!empty($row['ThumbPath']) && $insert_id)
		{
			import_featured_image($row['ThumbPath'], $insert_id);  //bollywood/reviews/2015/hero-1a.jpg
		}
        $field_value = array();
		$field_relation_value = array();

			$field_value = array();
			foreach($movies_meta_default as $key_meta => $row_meta) {
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


function review_merged_content($content){
	
	$upload_dir = UPLOAD_DIR_URL;
	$img1 = $img2 = $img3 = $img4 = $img5 = $img6 = $img7 = $img8 = $img9 = $img10 = $vid = '';
	if(!empty($content['ImgPath'])){
		$img1 = "<img src='". $upload_dir . $content['ImgPath']."'/>"; 
	}
	if(!empty($content['ImgPath2'])){
		$img2 =  "<img src='". $upload_dir . $content['ImgPath2']."'/>"; 
	}
		if(!empty($content['ImgPath3'])){
		$img3 =  "<img src='". $upload_dir . $content['ImgPath3']."'/>"; 
	}
		if(!empty($content['ImgPath4'])){
		$img4 =  "<img src='". $upload_dir . $content['ImgPath4']."'/>"; 
	}
		if(!empty($content['ImgPath5'])){
		$img5 =  "<img src='". $upload_dir . $content['ImgPath5']."'/>"; 
	}
		if(!empty($content['ImgPath6'])){
		$img6 =  "<img src='". $upload_dir . $content['ImgPath6']."'/>";  
	}
		if(!empty($content['ImgPath7'])){
		$img7 =  "<img src='". $upload_dir . $content['ImgPath7']."'/>"; 
	}	
	if(!empty($content['ImgPath8'])){
		$img8 =  "<img src='". $upload_dir . $content['ImgPath8']."'/>"; 
	}
	if(!empty($content['ImgPath9'])){
		$img9 =  "<img src='". $upload_dir . $content['ImgPath9']."'/>"; 
	}
	if(!empty($content['ImgPath10'])){
		$img9 =  "<img src='". $upload_dir . $content['ImgPath10']."'/>"; 
	}
	if(!empty($content['VideoPath'])){
		$vid = '[video width="560" height="320" mp4="'.$upload_dir . $content['VideoPath'].'"][/video]'; 
	}
	
	$row = '';
	$row .= '<p>'.$img1.'</p>'; 
	$row .= '<p>'.$vid.'</p>'; 
	$row .= '<p>'.$content['Review'].'</p>'; 
	$row .= '<p>'.$img2.'</p>';  
	$row .= '<p>'.$content['Review2'].'</p>'; 
	$row .= '<p>'.$img3.'</p>'; 
	$row .= '<p>'.$content['Review3'].'</p>'; 
	$row .= '<p>'.$img4.'</p>'; 
	$row .= '<p>'.$content['Review4'].'</p>'; 
	$row .= '<p>'.$img5.'</p>'; 
	$row .= '<p>'.$content['Review5'].'</p>'; 
	$row .= '<p>'.$img6.'</p>'; 
	$row .= '<p>'.$content['Review6'].'</p>'; 
	$row .= '<p>'.$img7.'</p>'; 
	$row .= '<p>'.$content['Review7'].'</p>'; 
	$row .= '<p>'.$img8.'</p>'; 
	$row .= '<p>'.$content['Review8'].'</p>'; 
	$row .= '<p>'.$img9.'</p>'; 
	$row .= '<p>'.$content['Review9'].'</p>'; 
	$row .= '<p>'.$img10.'</p>'; 
	$row .= '<p>'.$content['Review10'].'</p>'; 

	
	return $row;
}

exit;