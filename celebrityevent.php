<?php

require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db("movie_migrated");
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Interviews</h1>";
$db_old = new Db("movie_mi");

$result_old = $db_old->query("SELECT * FROM `dbo_celebrityeventmaster` order by EventId DESC LIMIT 2 ");
foreach ($result_old as $key => $row) {

		
	$old_eventId = $row['EventId'];



$movies = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModified",
    "post_date_gmt" => "LastModified",
    "post_content" => "",
    "post_title" => "EventTitle",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "EventTitle",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModified",
    "post_modified_gmt" => "LastModified",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "EventTitle",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "EventId");


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
    "post_type" => "celebrity-event",
    "post_mime_type" => "",
    "comment_count" => "");

	$movies_meta = array(
		"wpcf-celebrity-event-id" => "EventId",
		"wpcf-event-date" => "EventDate",
		"wpcf-event-venue" => "Venue",
		//"wpcf-interviews-video-id"=> "VideoId"
		
		
	);

	$movies_meta_default_values = array();



	
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
                    $movies_main[$mapKey] = WEBSITE_URL .'event-pictures/'. clean(strtolower($row[$mapValue]));
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
                case 'wpcf-event-date':
					$row[$mapValue] = strtotime($row[$mapValue]);
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
	$artist = array();
    if($result > 0 ) {
        $insert_id = $db_new->lastInsertId();
        echo "<br> Inserted Interview id: ".$insert_id;
		
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
		
		$result_old_datas = $db_old->query("select * from dbo_celebritypicturemaster where EventId =".$old_eventId);
		$i = 0;
		$data = array();
		$total = count($result_old_datas); 
		
			$key2= "celebrity_event_pictures";
			$value2 = $total;
			$keys2 = "_celebrity_event_pictures";
			$values2 = "field_581c34366e416";
			
			$insert_artist_meta_count = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".$insert_id.",'".$key2."','".$value2."'),(".$insert_id.",'".$keys2."','".$values2."')"; 
		#	echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta_count);
		
		foreach ($result_old_datas as $res)
		{
			$result_old_datass = $db_old->query("SELECT * FROM `dbo_celebritypictureartistrelation` WHERE `CelebrityPictureId`  =".$res['CelebrityPictureId']);
			foreach ($result_old_datass as $resss)
			{
				$artist[] = $resss['ArtistId'];
			}
		//	pr($res);
			$key = "celebrity_event_pictures_".$i."_title";
			$value = addslashes($res['Caption']);
			$keys = "_celebrity_event_pictures_".$i."_title";
			$values = "field_581c346e6e417";
			$key1 = "celebrity_event_pictures_".$i."_celebrity_event_picture";
			$value1= new_import_featured_image($res['YearFolder'].'/'.$res['MonthFolder'].'/'.$res['EventNameFolder'].'/'.$res['LargeImageFileName'], $insert_id);
			$keys1 = "_celebrity_event_pictures_".$i."_celebrity_event_picture";
			$values1 = "field_58256122fd3d3";
			
			
				$key3 = "celebrity_event_pictures_".$i."_thumbnail_image";
			$value3= new_import_featured_image($res['YearFolder'].'/'.$res['MonthFolder'].'/'.$res['EventNameFolder'].'/'.$res['ThumbnailImageFileName'], $insert_id);
			$keys3 = "_celebrity_event_pictures_".$i."_thumbnail_image";
			$values3 = "field_58343a0b2841b";
			
			$insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (".$insert_id.",'".$key."','".$value."'),(".$insert_id.",'".$key1."',".$value1."),(".$insert_id.",'".$keys."','".$values."'),(".$insert_id.",'".$keys1."','".$values1."'),(".$insert_id.",'".$key3."','".$value3."'),(".$insert_id.",'".$keys3."','".$values3."')"; 
		#	echo "<br>".$insert_artist_meta; exit;
			$result_meta = $db_new->query($insert_artist_meta);
		$i++;
			
		}
		$insert_thumbnail_meta = "INSERT INTO m8t7_postmeta (meta_value, meta_key, post_id) VALUES (".$value1.", '_thumbnail_id',".$insert_id.")";
		$result_thumbnail_meta = $db_new->query($insert_thumbnail_meta);
		
		$artist_old_id = (array_unique($artist));
		$new_artist_ids = get_new_artist_id( $artist_old_id );
		
		 foreach($new_artist_ids as $id){
			insert_relation_meta_for_movie($insert_id, 'celebrity-event-artise-id', $id, 'celebrity-event');
		 }
		 $row_meta = implode(',', $new_artist_ids);
		 
		 insert_single_postmeta($insert_id, 'wpcf-celebrity-event-artise-id', $row_meta);
		
}



exit;


