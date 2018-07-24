<?php

require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db(NEW_DB_NAME);
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Interviews</h1>";
$db_old = new Db("mt_main");
$old_artistId = array();
$result_old = $db_old->query("SELECT * FROM `dbo_interviewsmaster` WHERE InterviewId = '571'");
foreach($result_old as $result){
	$old_artistId[] = $result['ArtistId'];
}
$map_artist_id = get_new_artistid_array($old_artistId);
//pr($result_old); die;

$movies = array("ID" => "",
    "post_author" => "EditorID",
    "post_date" => "LastModifyDate",
    "post_date_gmt" => "LastModifyDate",
    "post_content" => "Review",
    "post_title" => "InterviewTitle",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "InterviewTitle",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModifyDate",
    "post_modified_gmt" => "LastModifyDate",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "InterviewTitle",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "InterviewId");


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
    "post_type" => "interviews",
    "post_mime_type" => "",
    "comment_count" => "");

	$movies_meta = array(
		"wpcf-interview-id" => "InterviewId",
		"wpcf-interview-date" => "InterviewDate",
		"wpcf-interview-artiste-id" => "ArtistId",
		"interviews_view_count" => "InterviewViewCount",
		"wpcf-interviews-video-type"=>"Artiste_Movies_Type",
		//"wpcf-interviews-video-id"=> "VideoId"
		
		
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
				case "post_author":
                    $movies_main[$mapKey] = new_author_id($row[$mapValue]);
                    break;
				case "guid":
                    $movies_main[$mapKey] = WEBSITE_URL .'interviews/'. clean(strtolower($row[$mapValue]));
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
                case 'wpcf-interview-date':
					$row[$mapValue] = strtotime($row[$mapValue]);
                    break;
				case 'wpcf-interview-artiste-id':
					$row[$mapValue] = $map_artist_id[$row['ArtistId']];
					break;
                case 'wpcf-interviews-video-type':
					if($row[$mapValue] == "Artiste")
					{
							$val = "2"; 
					}
					else if($row[$mapValue] == "Movie")
					{
						$val = "1";
					}
					else
					{
						$val = "";
					}
					$row[$mapValue] = $val; 
					break;
			    default:
                    break;
            }
            $movies_meta_default[$mapKey] = $row[$mapValue];
        } else {
            $movies_meta_default[$mapKey] = $movies_main_default_values[$mapKey];
        }
    }
#echo $map_artist_id[$row['ArtistId']]; die;
#pr($movies_meta_default); die;
    /* ================= */
//echo "INSERT INTO m8t7_posts (".implode(",", array_keys($movies_main)).") values (:".implode(",:", array_keys($movies_main)).")" ; die;
    $db_new->bindMore($movies_main);
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($movies_main)).") values (:".implode(",:", array_keys($movies_main)).")";

    $result = $db_new->query($insert_artist);

    if($result > 0 ) {
        $insert_id = $db_new->lastInsertId();
        echo "<br> Inserted Interview id: ".$insert_id;
		
		if(!empty($row['ThumbPath']) && $insert_id)
		{
			import_featured_image('bollywood/stock/'.$row['ThumbPath'], $insert_id);
		}
        $field_value = array();
		$field_relation_value = array();

			$field_value = array();
			foreach($movies_meta_default as $key_meta => $row_meta) {
				$field_value[] = "'".$insert_id."','".addslashes($key_meta)."','".addslashes($row_meta)."'";
			}
			//pr($field_value,1);
			$insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".implode("),(", $field_value).")";
			#echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta);
			echo ", Meta Inserted. <br>";
	}
    //exit;
}


function review_merged_content($content){
	
	$upload_dir = UPLOAD_DIR_URL .'bollywood/stock/';
	$img1 = $img2 = $img3 = $img4 = $img5 = $vid = '';
	$vid = $vid1 = $vid2 = $vid3 = $vid4  = '' ;
	if(!empty($content['ImagePath'])){
		$img1 = "<img src='". $upload_dir . $content['ImagePath']."'/>"; 
	}
	if(!empty($content['ImagePath2'])){
		$img2 =  "<img src='". $upload_dir . $content['ImagePath2']."'/>"; 
	}
		if(!empty($content['ImagePath3'])){
		$img3 =  "<img src='". $upload_dir . $content['ImagePath3']."'/>"; 
	}
		if(!empty($content['ImagePath4'])){
		$img4 =  "<img src='". $upload_dir . $content['ImagePath4']."'/>"; 
	}
		if(!empty($content['ImagePath5'])){
		$img5 =  "<img src='". $upload_dir . $content['ImagePath5']."'/>"; 
	}
	if(!empty($content['VideoLink1'])){
		$vid = '[video width="560" height="320" mp4="'.$upload_dir . $content['VideoLink1'].'"][/video]'; 
	}
	if(!empty($content['VideoLink2'])){
		$vid1 = '[video width="560" height="320" mp4="'.$upload_dir . $content['VideoLink2'].'"][/video]'; 
	}
	if(!empty($content['VideoLink3'])){
		$vid2 = '[video width="560" height="320" mp4="'.$upload_dir . $content['VideoLink3'].'"][/video]'; 
	}
	if(!empty($content['VideoLink4'])){
		$vid3 = '[video width="560" height="320" mp4="'.$upload_dir . $content['VideoLink4'].'"][/video]'; 
	}
	if(!empty($content['VideoLink5'])){
		$vid4 = '[video width="560" height="320" mp4="'.$upload_dir . $content['VideoLink5'].'"][/video]'; 
	}
	
	$row = '';
	$row .= '<p>'.$img1.'</p>'; 
	$row .= '<p>'.$vid.'</p>'; 
	$row .= '<p>'.$content['InterviewMatter'].'</p>'; 
	$row .= '<p>'.$img2.'</p>';  
	$row .= '<p>'.$content['InterviewMatter2'].'</p>'; 
	$row .= '<p>'.$img3.'</p>'; 
	$row .= '<p>'.$content['InterviewMatter3'].'</p>'; 
	$row .= '<p>'.$img4.'</p>'; 
	$row .= '<p>'.$content['InterviewMatter4'].'</p>'; 
	$row .= '<p>'.$img5.'</p>'; 
	$row .= '<p>'.$content['InterviewMatter5'].'</p>'; 
	
	
	return $row;
}




exit;


