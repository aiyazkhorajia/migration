<?php
echo " start time ". date('Y-m-d H:i:s') ."<br>";
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
echo "<h1>Users</h1>";
$db_new = new Db(NEW_DB_NAME);
$result_new = $db_new->query("select count(ID) as cnt, post_name from m8t7_posts WHERE post_type = 'Artiste' group by post_name having cnt>1 order by cnt desc ");
//pr($result_new);


foreach ($result_new as $key => $row) {
    pr($row,0);
    $cnt = 0;
    $select = $db_new->query("select * from m8t7_posts where post_name = '".$row['post_name']."'");
    
    foreach($select as $key1 =>$row1) {
        $cnt++;
        if($cnt == 1) continue;
        $update_query = "update m8t7_posts set post_name = concat(post_name,'-',$cnt), guid = concat(guid,'-',$cnt) where ID = ".$row1['ID'];
        echo "<br>".$update_query;
        $update_result = $db_new->query($update_query);
    }
        //exit;
}
echo "<br> end time ". date('Y-m-d H:i:s') ."\n";
exit;