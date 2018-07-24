<?php

require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db("movie_migrated");
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Movie Stills</h1>";
$db_old = new Db("movie_mi");

$id_array = array();
$old_artistId = array();
$result_old = $db_old->query("select DISTINCT(MovieId) from dbo_productionstillsmaster limit 1");
//$result_old = $db_old->query("SELECT * FROM `dbo_moviestillsmaster` WHERE `MovieId` = 22450");
//pr($result_old); die;
foreach ($result_old as $key => $row) {
	
	$old_movieid = $row['MovieId'];
	#pr($old_movieid); die;
if (!in_array($old_movieid,$id_array))
	{
	$map_post_id = get_new_movieid_withtitle_array($old_movieid);
	$post_title =  $map_post_id[$old_movieid]['post_title']; 
	//echo $post_title .':'. $old_movieid .'</br>'; 
	$new_id = $map_post_id[$old_movieid]['NewMovieId'];  
	$posters = array("ID" => "",
    "post_author" => "",
    "post_date" => "",
    "post_date_gmt" => "",
    "post_content" => "",
    "post_title" => "MovieId",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "MovieId",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "",
    "post_modified_gmt" => "",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "MovieId",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "MovieId");


$posters_main_default_values = array("ID" => "",
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
    "post_type" => "movie-production-sti",
    "post_mime_type" => "",
    "comment_count" => "");

	$posters_meta = array(
		"wpcf-production-stills-id" => "MovieId",
		//"wpcf-copyright-for-movie-posters" => "Copyright",
		//"posters_view_count" => "ViewCount",
		"_wpcf_belongs_movie_id" => "MovieId",
	);

	$posters_meta_default_values = array();



	
    $posters_main = $posters_main_default_values;
    $posters_meta_default = $posters_meta_default_values;
    $count = 0;
    $fields = '';
	
	//pr($posters); die;
    foreach ($posters as $mapKey => $mapValue) {
					
        if (!empty($mapValue)) {
		
            switch ($mapKey) {
			
			case "post_title":
					
		            $posters_main[$mapKey] = $post_title." "."Production Pictures"; //sultan posters
                    break;
                
            case "post_name":
					$posters_main[$mapKey] = clean(strtolower($post_title))."-production-pictures";//sultan-posters
                    break;
			
			case "guid":
                    $posters_main[$mapKey] = WEBSITE_URL .'movie-production-sti/'. clean(strtolower($post_title))."-production-pictures";
                    break;
		
            default;
			
                    $posters_main[$mapKey] = $row[$mapValue];
                    break;
            }
			
        }
	
    }
    
    #pr($posters_main, 1); die;
	
	
	
    foreach ($posters_meta as $mapKey => $mapValue) {
		
	
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case '_wpcf_belongs_movie_id':
			    
					$row[$mapValue] = $new_id;
                    break;
				//case 'ArtistId':
				//	$row[$mapValue] = $map_artist_id[$row['ArtistId']];
                 //   break;
                default:
                    break;
            }
            $posters_meta_default[$mapKey] = $row[$mapValue];
        } else {
            $posters_meta_default[$mapKey] = $posters_main_default_values[$mapKey];
        }
    }
#pr($posters_meta_default); die;
    $db_new->bindMore($posters_main);
	
    $insert_artist = "INSERT INTO m8t7_posts (".implode(",", array_keys($posters_main)).") values (:".implode(",:", array_keys($posters_main)).")";
	
    $result = $db_new->query($insert_artist);
	//echo $map_post_id[$row[$mapValue]]; die;
    if($result > 0 ) {
		//print_r($row);
        $insert_id = $db_new->lastInsertId();
        echo "Poster inserted: ".$insert_id;
		
		$result_old_data = $db_old->query("select * from dbo_productionstillsmaster where MovieId =".$old_movieid);
		$i = 0;
		$data = array();
		$artist = array();
		$total = count($result_old_data); 
		
			$key2= "movie_production_stills";
			$value2 = $total;
			$keys2 = "_movie_production_stills";
			$values2 = "field_58230e634077b";
			
			$insert_artist_meta_count = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".$insert_id.",'".$key2."','".$value2."'),(".$insert_id.",'".$keys2."','".$values2."')"; 
		#	echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta_count);
		
		foreach ($result_old_data as $res)
		{
			$artistdata = array();
			
			$result_old_datass = $db_old->query("SELECT * FROM `dbo_productionstillsartistrelation` WHERE `ProductionStillsId`  = ".$res['ProductionStillsId']);
			foreach ($result_old_datass as $resss)
			{
				$artist[] = $resss['ArtistId'];
				$artistdata[] = $resss['ArtistId'];
			}
			if(!empty ($artistdata)) {
			$new_artist_ids = get_new_artist_id_without_old_id( $artistdata, $type = 'array');
		//	pr($new_artist_ids);
			$data_new_artist = serialize ($new_artist_ids);	
			}
			else
			{
			$data_new_artist = "";
			}
		//	pr($res);
			$key = "movie_production_stills_".$i."_title";
			$value = addslashes($res['Caption']);
			$keys = "_movie_production_stills_".$i."_title";
			$values = "field_58230f315966a";
			
			$key1 = "movie_production_stills_".$i."_movie_production_stills_picture";
			$value1= new_import_featured_image($res['ProductionYearFolder'].'/'.$res['ProductionNameFolder'].'/'.$res['LargeImageFileName'], $insert_id);
			$keys1 = "_movie_production_stills_".$i."_movie_production_stills_picture";
			$values1 = "field_58230f3e5966b";
			
			$key2 = "movie_production_stills_".$i."_productionstill_artiste_id";
			$value2= $data_new_artist;
			$keys2 = "_movie_production_stills_".$i."_productionstill_artiste_id";
			$values2 = "field_58258ec71235a";
			
			
			$key3 = "movie_production_stills_".$i."_thumbnail_image";
			$value3= new_import_featured_image($res['ProductionYearFolder'].'/'.$res['ProductionNameFolder'].'/'.$res['ThumbnailImageFileName'], $insert_id);
			$keys3 = "_movie_production_stills_".$i."_thumbnail_image";
			$values3 = "field_58353d2836228";
			
			 $insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".$insert_id.",'".$key."','".$value."'),(".$insert_id.",'".$key1."',".$value1."),(".$insert_id.",'".$keys."','".$values."'),(".$insert_id.",'".$keys1."','".$values1."'),(".$insert_id.",'".$key2."','".$value2."'),(".$insert_id.",'".$keys2."','".$values2."'),(".$insert_id.",'".$key3."','".$value3."'),(".$insert_id.",'".$keys3."','".$values3."')";  
		#	echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta);
		$i++;
			
		}
		$insert_thumbnail_meta = "INSERT INTO m8t7_postmeta (meta_value, meta_key, post_id) VALUES (".$value1.", '_thumbnail_id',".$insert_id.")";
		$result_thumbnail_meta = $db_new->query($insert_thumbnail_meta);
		
		
		$new_artist_ids = get_new_artist_id_without_old_id( $artist, $type = 'array');
			$data_new_artist = serialize ($new_artist_ids);	
		
	//	if(!empty($row['LargeImageFileName']) && !empty($row['MovieYearFolder']) &&  $insert_id)
	//	{
	//		import_featured_image($row['MovieYearFolder'].'/'.$row['MovieNameFolder'].'/'.$row['LargeImageFileName'], $insert_id);
	//	}
        $field_value = array();
		$field_relation_value = array();

			$field_value = array();
			foreach($posters_meta_default as $key_meta => $row_meta) {
				$field_value[] = "'".$insert_id."','".addslashes($key_meta)."','".addslashes($row_meta)."'";
			}
			//pr($field_value,1);
			$insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".implode("),(", $field_value).")";
		#	echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta);
			echo ", Meta Inserted. <br>";
			if(!empty($artist)) {
				$artist_old_id = (array_unique($artist));
				$new_artist_idsss = get_new_artist_id( $artist_old_id, $type = 'array' );
				foreach($new_artist_idsss as $id){
				insert_relation_meta_for_movie($insert_id, 'aritste-for-production', $id, 'movie-production-sti');
				}
				$row_meta = implode(',', $new_artist_idsss);
				 insert_single_postmeta($insert_id, 'wpcf-aritste-for-production', $row_meta);
			}
			
	//	
	}

	
	}
	$id_array[] = $old_movieid; 
//	die;
	//pr($id_array); 
}
	die; exit;	


