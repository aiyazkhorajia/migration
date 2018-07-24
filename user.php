<?php
require("db/Db.class.php");
// Creates the instance
echo "<h1>Users</h1>";
$db_new = new Db(NEW_DB_NAME);
$result_new = $db_new->query("SELECT * FROM m8t7_users limit 1");
//pr($result_new);

echo "<hr><br><h1>Artist Broadband Comments</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT * FROM dbo_membermaster order by MemberId");
//pr($result_old,0);

$users = array("ID" => "MemberId",
    "user_login" => "UserName",
    "user_email" => "Email",
    "user_pass" => "Password",
    "user_nicename" => "",
    "user_url" => "",
    "user_registered" => "CreatedDate",
    "user_activation_key" => "",
    "user_status" => "Status",
    "display_name" => "");

$users_meta = array("first_name" => "FName",
    "last_name" => "LName",
    "user_gender" => "Gender",
    "user_sendupdate" => "SendUpdate", //newsletter
    "user_birthdate" => "DateofBirth",
    "M8t7_user_level" => "",
    "M8t7_capabilities" => '',
    "show_admin_bar_front" => "",
    "use_ssl" => "",
    "admin_color" => "",
    "comment_shortcuts" => "",
    "rich_editing" => "",
    "description" => "",
    "nickname" => "",
    "wp_user_level" => "",
    "oa_social_login_user_token" => "",
    "oa_social_login_identity_provider" => "",
    "oa_social_login_user_thumbnail" => "",
    "oa_social_login_user_picture" => "",
    "default_password_nag" => ""
);
$user_meta_default_values = array(
    "last_name" => "",
    "user_gender" => "",
    "user_sendupdate" => "", 
    "user_birthdate" => "",
    "M8t7_user_level" => "0",
    "M8t7_capabilities" => 'a:1:{s:10:"subscriber";b:1;}',
    "show_admin_bar_front" => "false",
    "use_ssl" => "0",
    "admin_color" => "fresh",
    "comment_shortcuts" => "false",
    "rich_editing" => "true",
    "description" => "",
    "nickname" => "",
    "wp_user_level" => "0",
    "oa_social_login_user_token" => "",
    "oa_social_login_identity_provider" => "",
    "oa_social_login_user_thumbnail" => "",
    "oa_social_login_user_picture" => "",
    "default_password_nag" => ""
);

$user_main_default_values = array("ID" => "",
    "user_login" => "",
    "user_email" => "",
    "user_pass" => "",
    "user_registered" => "0000-00-00 00:00:00",
    "user_status" => "1",
    "user_nicename" => "",
    "user_url" => "",
    "user_activation_key" => "",
    "display_name" => "");

foreach ($result_old as $key => $row) {
    $user_main = $user_main_default_values;
    $user_meta = $user_meta_default_values;
    $count = 0;
    $fields = '';
    foreach ($users as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            $user_main[$mapKey] = $row[$mapValue];
        }
    }
    //pr($row, 0);
    //pr($user_main, 0);
    //echo $fields;
    foreach ($users_meta as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            $user_meta[$mapKey] = $row[$mapValue];
        } else {
            $user_meta[$mapKey] = $user_meta_default_values[$mapKey];
        }
    }

    //pr($user_meta, 0);
    $db_new->bindMore($user_main);
    $insert_users = "INSERT INTO m8t7_users (".implode(",", array_keys($user_main)).") values (:".implode(",:", array_keys($user_main)).")";
    //echo $insert_users; 
    $result = $db_new->query($insert_users);
    //exit;
    //$result = 1;
    if($result > 0 ) {
        $insert_id = $db_new->lastInsertId();
        echo "<br> user id ".$insert_id;
        $field_value = array();
        foreach($user_meta as $key_meta => $row_meta) {
            $field_value[] = "'".$insert_id."','".addslashes($key_meta)."','".addslashes($row_meta)."'";
        }
        
        $insert_user_meta = "INSERT INTO m8t7_usermeta (user_id, meta_key , meta_value) values (".implode("),(", $field_value).")";
        //echo "<br>".$insert_user_meta; exit;
        $result_meta = $db_new->query($insert_user_meta);
    }
}
exit;
