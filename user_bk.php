<?php

require("db/Db.class.php");

// Creates the instance
echo "<h1>Users</h1>";
$db_new = new Db("movietalkies");
$result_new = $db_new->query("SELECT * FROM m8t7_users");
pr($result_new);


echo "<hr><br><h1>Artist Broadband Comments</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT * FROM dbo_membermaster order by MemberId limit 1");
pr($result_old);

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

foreach ($result_old as $key => $row) {
    $user_main = array();
    $user_meta = array();
    $count = 0;
    $fields = '';
    foreach ($users as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            $user_main[$mapKey] = $row[$mapValue];
        }
        
        /*if ($count++ != 0) $fields .= ', ';
        $col = PDO::quote($mapKey);
        $val = PDO::quote($row[$mapValue]);
        $fields .= "`$col` = $val";
         * 
         */
    }
    //echo $fields;
    foreach ($users_meta as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            $user_meta[$mapKey] = $row[$mapValue];
        } else {
            $user_meta[$mapKey] = $user_meta_default_values[$mapKey];
        }
    }
    $field_alise = ":".implode(",:",array_keys($result_new[0]));
    pr($user_main);
    echo $field_alise; 
    echo "<br>INSERT INTO M8t7_users(".implode(',',array_keys($result_new[0])).") VALUES($field_alise)";
//    exit;
    $insert = $db_new->query("INSERT INTO M8t7_users(".implode(',',array_keys($result_new[0]))." VALUES(".$field_alise.")", $user_main);
    echo "<br>array keys comma ".implode(",:",array_keys($result_new[0]));

    pr($user_main, 0);
    pr($user_meta, 1);
}
exit;
