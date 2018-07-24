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
$result_old = $db_old->query("SELECT * FROM `dbo_newsmaster` WHERE NewsId = 31284");
pr($result_old); die;

/* $old_movieid = array();
foreach($result_old as $result){
	$old_movieId = explode(',', $result['MovieId']);
	foreach($old_movieId as $tempid){
		$old_movieid[] = $tempid;
	}
}
//pr($old_movieid); die;

$map_post_id = get_new_movieid_array($old_movieid);  */


//pr($map_post_id); die;
$movies = array("ID" => "",
    "post_author" => "EditorID",
    "post_date" => "NewsDate",
    "post_date_gmt" => "",
    "post_content" => "NewsMatter",
    "post_title" => "NewsSubject",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "NewsSubject",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "",
    "post_modified_gmt" => "",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "NewsSubject",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "NewsId");


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
    "post_type" => "news",
    "post_mime_type" => "",
    "comment_count" => "");

	$movies_meta = array(
		"wpcf-news-id" => "NewsId",
		"wpcf-txt-artist-id" => "ArtistId",
		"wpcf-txt-movie-id" => "MovieId",
		"wpcf-txt-news-video-id" => "VideoId",
		"wpcf-is-what-hot-image" => "IsWhatHot",
		"wpcf-is-what-new-image" => "IsWhatNew",
		"wpcf-news-videotype" => "Artiste_Movies_Type",
		"wpcf-news-viewcount" => "NewsViewCount"
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
				case "post_author":
                    $movies_main[$mapKey] = new_author_id($row[$mapValue]);
                    break;
                case "post_content":
                    $movies_main[$mapKey] = news_merged_content($row);
                    break;
				case "guid":
                    $movies_main[$mapKey] = WEBSITE_URL ."news/".clean(strtolower($row[$mapValue]));
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
                case 'wpcf-news-videotype':
					$row[$mapValue] = ( $row[$mapValue] !='Movie') ? 2 : 1;
                    break;
				case 'wpcf-txt-movie-id':
					$row[$mapValue] =   get_comma_separated_newmovieId($row[$mapValue]); //$map_post_id[$row[$mapValue]];
					//echo $row[$mapValue]; die;
                    break;
				case 'wpcf-txt-artist-id':
					$newid = get_new_artist_id( $row[$mapValue], 'string' );
					//pr($newid); die;
					$row[$mapValue] = !empty($newid) ? implode( ', ', $newid ) : '' ;
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
	
	$cat_mapping = array('1'=>'135', '2'=>'137', '3'=>'138', '4'=>'139', '5'=>'140', '6'=>'141' );
	$cat_types = array('News'=>'157', 'Television'=>'144', 'Highlights'=>'145', 'Box Office'=>'143');
	
    if($result > 0 ) {
		//print_r($row);
        $insert_id = $db_new->lastInsertId();
        echo "<br> Inserted News id: ".$insert_id;
		$new_cat_id = $cat_mapping[$row['NewsIndustry']];
		$new_cat_type = $cat_types[$row['Category']];
		$insert_category = $db_new->query("INSERT INTO m8t7_term_relationships (object_id, term_taxonomy_id , term_order) values ('{$insert_id}', '{$new_cat_id}', 0)");
		$insert_category1 = $db_new->query("INSERT INTO m8t7_term_relationships (object_id, term_taxonomy_id , term_order) values ('{$insert_id}', '{$new_cat_type}', 0)");
		if(!empty($row['NewsThumbpath']) && $insert_id)
		{
			import_featured_image('images/bollywood/stock/'.$row['NewsThumbpath'], $insert_id);
		}
        $field_value = array();
		$field_relation_value = array();

			$field_value = array();
			//pr($movies_meta_default);
			foreach($movies_meta_default as $key_meta => $row_meta) {
				if( $key_meta == 'wpcf-txt-movie-id' && !empty($row_meta)){
					
					$old_movie_ids = explode(',', $row_meta);
					foreach($old_movie_ids as $id) {
						insert_relation_meta_for_movie($insert_id, 'txt-movie-id', $id, 'news');
					}
				}
				if( $key_meta == 'wpcf-txt-artist-id' && !empty($row_meta)){
					
					$artist_ids = explode(',', $row_meta);
					foreach($artist_ids as $id) {
						insert_relation_meta_for_movie($insert_id, 'txt-artist-id', $id, 'news');
					}
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


function news_merged_content($content){
	
	$upload_dir = UPLOAD_DIR_URL .'bollywood/stock/';
	$youtubeLink = $img1 = $img2 = $img3 = $img4 = $img5 = $img6 = $img7 = $img8 = $img9 = $img10 = $vid = '';
		
	$row = '';

	
	if(!empty($content['YoutubeLink'])){
		$youtubeLink = '<iframe id="ytplayer" type="text/html" width="560" height="315" src="https://www.youtube.com/embed/"'.$content['YoutubeLink'].'"?rel=0&showinfo=0&color=white&iv_load_policy=3" frameborder="0" allowfullscreen></iframe>'; 
		$row .= '<p>'.$youtubeLink.'</p>'; 
	}
	if(!empty($content['NewsImagepath'])){
		$img1 = "<img src='". $upload_dir . $content['NewsImagepath']."'/>"; 
		$row .= '<p>'.$img1.'</p>';
	}
	if(!empty($content['VideoPath'])){
		$vid = '[video width="560" height="320" mp4="'.$upload_dir . $content['VideoPath'].'"][/video]';
		$row .= '<p>'.$vid.'</p>'; 
	}
 
	if(!empty($content['NewsImagepath2'])){
		$img2 = "<img src='". $upload_dir . $content['NewsImagepath2']."'/>"; 
	}
		if(!empty($content['NewsImagepath3'])){
		$img3 = "<img src='". $upload_dir . $content['NewsImagepath3']."'/>"; 
	}
		if(!empty($content['NewsImagepath4'])){
		$img4 = "<img src='". $upload_dir . $content['NewsImagepath4']."'/>"; 
	}
		if(!empty($content['NewsImagepath5'])){
		$img5 = "<img src='". $upload_dir . $content['NewsImagepath5']."'/>"; 
	}
		if(!empty($content['NewsImagepath6'])){
		$img6 = "<img src='". $upload_dir . $content['NewsImagepath6']."'/>"; 
	}
		if(!empty($content['NewsImagepath7'])){
		$img7 = "<img src='". $upload_dir . $content['NewsImagepath7']."'/>"; 
	}	
	if(!empty($content['NewsImagepath8'])){
		$img8 = "<img src='". $upload_dir . $content['NewsImagepath8']."'/>"; 
	}
	if(!empty($content['NewsImagepath9'])){
		$img9 = "<img src='". $upload_dir . $content['NewsImagepath9']."'/>"; 
	}
	if(!empty($content['NewsImagepath10'])){
		$img9 = "<img src='". $upload_dir . $content['NewsImagepath10']."'/>"; 
		$row .= '<p>'.$img10.'</p>'; 

	}


	$row .= '<p>'.$content['NewsMatter'].'</p>'; 
	$row .= '<p>'.$img2.'</p>';  
	$row .= '<p>'.$content['NewsMatter2'].'</p>'; 
	$row .= '<p>'.$img3.'</p>'; 
	$row .= '<p>'.$content['NewsMatter3'].'</p>'; 
	$row .= '<p>'.$img4.'</p>'; 
	$row .= '<p>'.$content['NewsMatter4'].'</p>'; 
	$row .= '<p>'.$img5.'</p>'; 
	$row .= '<p>'.$content['NewsMatter5'].'</p>'; 
	$row .= '<p>'.$img6.'</p>'; 
	$row .= '<p>'.$content['NewsMatter6'].'</p>'; 
	$row .= '<p>'.$img7.'</p>'; 
	$row .= '<p>'.$content['NewsMatter7'].'</p>'; 
	$row .= '<p>'.$img8.'</p>'; 
	$row .= '<p>'.$content['NewsMatter8'].'</p>'; 
	$row .= '<p>'.$img9.'</p>'; 
	$row .= '<p>'.$content['NewsMatter9'].'</p>'; 
	$row .= '<p>'.$content['NewsMatter10'].'</p>'; 


	return $row;
	/* return preg_replace('#<a.*?>(.*?)</a>#i', '\1', $row); */
}

exit;
