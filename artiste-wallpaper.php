<?php
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db("movie_migrated");
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Movie Posters</h1>";
$db_old = new Db("movie_mi");
$id_array = array();
$old_artistId = array();
$result_old = $db_old->query("select DISTINCT(ArtistId) from dbo_artistwallpaper  limit 1");
//pr($result_old); die;
foreach ($result_old as $key => $row) {
	
	$old_movieid = $row['ArtistId'];
	//pr($id_array);
if (!in_array($old_movieid,$id_array))
	{
	$map_post_id = get_new_Artistid_withtitle_array($old_movieid);
	$post_title =  $map_post_id[$old_movieid]['post_title']; 
	//echo $post_title .':'. $old_movieid .'</br>'; 
	$new_id = $map_post_id[$old_movieid]['NewArtisteId'];  
	$posters = array("ID" => "",
    "post_author" => "",
    "post_date" => "",
    "post_date_gmt" => "",
    "post_content" => "",
    "post_title" => "ArtistId",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "ArtistId",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "",
    "post_modified_gmt" => "",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "ArtistId",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "ArtistId");


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
    "post_type" => "artiste-wallpapers",
    "post_mime_type" => "",
    "comment_count" => "");

	$posters_meta = array(
		//"movie-posters-artiste-id" => "ArtistId",
		//"wpcf-copyright-for-movie-posters" => "Copyright",
		//"posters_view_count" => "ViewCount",
		"_wpcf_belongs_artiste_id" => "ArtistId",
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
					
		            $posters_main[$mapKey] = $post_title." "."Wallpapers"; //sultan posters
                    break;
                
            case "post_name":
					$posters_main[$mapKey] = clean(strtolower($post_title))."-wallpapers";//sultan-posters
                    break;
            default;
			
				case "guid":
                    $posters_main[$mapKey] = WEBSITE_URL .'artiste-wallpapers/'. clean(strtolower($post_title))."-wallpapers";
                    break;
			
                    $posters_main[$mapKey] = $row[$mapValue];
                    break;
            }
			
        }
	
    }
    
    #pr($posters_main, 1); die;
	
	
	
    foreach ($posters_meta as $mapKey => $mapValue) {
		
	
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case '_wpcf_belongs_artiste_id':
			    
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
		
		$result_old_data = $db_old->query("select * from dbo_artistwallpaper where ArtistId =".$old_movieid);
		$i = 0;
		$data = array();
		$total = count($result_old_data); 
		
			$key2= "artiste_wallpapers";
			$value2 = $total;
			$keys2 = "_artiste_wallpapers";
			$values2 = "field_5822df079a5a8";
			
			$insert_artist_meta_count = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".$insert_id.",'".$key2."','".$value2."'),(".$insert_id.",'".$keys2."','".$values2."')"; 
		#	echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta_count);
		
		foreach ($result_old_data as $res)
		{
		//	pr($res);
			$key = "artiste_wallpapers_".$i."_title";
			$value = addslashes($res['Caption']);
			$keys = "_artiste_wallpapers_".$i."_title";
			$values = "field_582424bb1d6be";
			
			$key1 = "artiste_wallpapers_".$i."_wallpaper";
			$value1= new_import_featured_image($res['FolderName'].'/'.$res['LFileName'], $insert_id);
			$keys1 = "_artiste_wallpapers_".$i."_wallpaper";
			$values1 = "field_5822df229a5a9";
			
			$key2 = "artiste_wallpapers_".$i."_thumbnail_image";
			$value2= new_import_featured_image($res['FolderName'].'/'.$res['TFileName'], $insert_id);
			$keys2 = "artiste_wallpapers_".$i."_thumbnail_image";
			$values2 = "field_583531e7c2afb";
			
			 $insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".$insert_id.",'".$key."','".$value."'),(".$insert_id.",'".$key1."',".$value1."),(".$insert_id.",'".$keys."','".$values."'),(".$insert_id.",'".$keys1."','".$values1."'),(".$insert_id.",'".$key2."','".$value2."'),(".$insert_id.",'".$keys2."','".$values2."')"; 
		#	echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta);
		$i++;
			
		}
		$insert_thumbnail_meta = "INSERT INTO m8t7_postmeta (meta_value, meta_key, post_id) VALUES (".$value1.", '_thumbnail_id',".$insert_id.")";
		$result_thumbnail_meta = $db_new->query($insert_thumbnail_meta);
		
		
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
			
			
	//	
	}

	
	}
	$id_array[] = $old_movieid; 
	//pr($id_array); die;
}
	die; exit;	


		
	
		