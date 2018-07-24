<?php
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
//echo "<h1>Movies</h1>";
$db_new = new Db("movie_local");
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Editor Import</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT * FROM `dbo_editormaster`");
$export_array = array();
foreach ($result_old as $key => $row) {

	$user_name = clean($row['EditorName']);
	$nicename = $row["EditorName"];
	$role_seralized = 'a:1:{s:6:"editor";b:1;}';
	$insert_editor = "INSERT INTO `m8t7_users` (`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_status`) VALUES ('{$user_name}', MD5('Mt@123'), '{$nicename}', 'email@example.com', '0')";
    $result = $db_new->query($insert_editor);
	$insert_id = $db_new->lastInsertId();
	
	$field_value = array();
	$editor_meta = array( 'M8t7_capabilities'=>'a:1:{s:6:"editor";b:1;}', 'M8t7_user_level'=>'7', 'Designation'=>$row['Designation'], 'Profile'=>$row['Profile'], 'FacebookLink'=>$row['FacebookLink'], 'FacebookUserID'=>$row['FacebookUserID'], 'TwitterLink'=>$row['TwitterLink'], 'UserLink'=>$row['UserLink'], 'InstagramLink'=>$row['InstagramLink'] );
	foreach($editor_meta as $key_meta => $row_meta) {
		$field_value[] = "'".$insert_id."','".addslashes($key_meta)."','".addslashes($row_meta)."'";
	}

	$insert_usr_meta = "INSERT INTO m8t7_usermeta (user_id, meta_key , meta_value) values (".implode("),(", $field_value).")";
	$user_meta_result = $db_new->query($insert_usr_meta);	
	$export_array[$row['ID']] = $insert_id;
}
pr($export_array);
Exit;



