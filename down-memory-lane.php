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
$result_old = $db_old->query("SELECT * FROM `dbo_downmemorylane` WHERE DownMemoryLaneId = '85'");
foreach($result_old as $result){
	$old_artistId[] = $result['ArtistId'];
}
$map_artist_id = get_new_artistid_array($old_artistId);
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
    "old_id" => "DownMemoryLaneId");


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
    "post_type" => "down-memory-lane",
    "post_mime_type" => "",
    "comment_count" => "");

$movies_meta = array(
                    "wpcf-down-memory-lane-artist-id" => "ArtistId",
                    "wpcf-down-memory-lane-id" => "DownMemoryLaneId",
                    "wpcf-by"=>"ReviewBy",
                    "wpcf-date" => "EntryDate",
                    "down_memory_lane_view_count" => "ViewCount",
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
                    $movies_main[$mapKey] = down_memory_lane_merged_content($row);
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
                case 'wpcf-date':
                    $row[$mapValue] = strtotime($row[$mapValue]);
                    break;
                case 'wpcf-down-memory-lane-artist-id':
                    $row[$mapValue] = $map_artist_id[$row['ArtistId']];
                    break;
                case 'wpcf-by':
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

    if($result > 0 ) {
        $insert_id = $db_new->lastInsertId();
        echo "<br> Inserted Down Memory Lane id: ".$insert_id;
        
        //$result_old_data = $db_old->query("select * from dbo_artiststill where ArtistId =".$old_artistId);
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


function down_memory_lane_merged_content($content){
	
	$row = '';
	$row .= !empty( $content['text1'] ) ? '<p>'.$content['text1'].'</p>' : '';  
	$row .= !empty( $content['text2'] ) ? '<p>'.$content['text2'].'</p>' : '';
	$row .= !empty( $content['text3'] ) ? '<p>'.$content['text3'].'</p>' : '';
	$row .= !empty( $content['text4'] ) ? '<p>'.$content['text4'].'</p>' : '';
	$row .= !empty( $content['text5'] ) ? '<p>'.$content['text5'].'</p>' : '';
	
	return $row;
}




exit;


