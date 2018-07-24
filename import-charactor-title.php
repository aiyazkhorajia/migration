<?php
require("db/Db.class.php");
include_once("common-function.php");

// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db(NEW_DB_NAME);
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Movie Charactor Title</h1>";
$db_old = new Db("mt_main");
//$result_old = $db_old->query("SELECT * FROM `dbo_newsmaster` WHERE NewsId = '20224'");

$result_old = $db_old->query("SELECT * FROM `dbo_moviescharactertitle` WHERE CastType = 'Cast' LIMIT 5, 20");

$old_movieid = array();
$old_artistid = array();

foreach($result_old as $result){
	$old_movieid[] = $result['MovieId'];
	$old_artistid [] = $result['ArtisteId'];
} 


$new_movieIds = get_new_movieid_array($old_movieid);
$new_artistIds = get_new_artist_id($old_artistid, 'array');
//pr($new_movieIds); 
//pr($new_artistIds); 

//pr($result_old, 1); die;
$new_table_rows = array();

foreach ($result_old as $key => $row) {
	//pr($row); 
    foreach ($row as $mapKey => $mapValue) {
        if (!empty($mapKey)) {
			//echo $mapValue.'<br>';
            switch ($mapKey) {
                case 'MovieId':
                        $new_table_rows[$key][$mapKey] = $new_movieIds[$mapValue];
                    break;
				case 'ArtisteId':
                        $new_table_rows[$key][$mapKey] = $new_artistIds[$mapValue];
                    break;
				case 'CastType':
                        $new_table_rows[$key][$mapKey] = 'cast';
                    break;
                default:
                    $new_table_rows[$key][$mapKey] = $row[$mapKey];
					break;
            }
        } 
    }
}


        foreach ($new_table_rows as $new_row) {

            if (!empty($new_row)) {
                $field_value[] = "'" . $new_row['MovieId'] . "','" . addslashes($new_row['ArtisteId']) . "','" . addslashes($new_row['CharacterTitle']) . "','" .  addslashes($new_row['CastType']) . "','" . addslashes($new_row['LastModified'])."'";
            }
        }
        $insert_query = "INSERT INTO m8t7_moviecharcter(movie_id, artist_id, character_title, cast_type, last_modified) values (" . implode("),(", $field_value) . ")";
		//echo  $insert_query; die;
        $result_meta = $db_new->query($insert_query);
		echo "</br>inserted records: ".$result_meta;


exit;
