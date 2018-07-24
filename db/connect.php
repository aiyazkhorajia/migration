<?php

class myDbClass {

    function dbConnect($db) {
        $dbhost = 'localhost';
        $dbuser = 'root';
        $dbpass = '';
        $conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error connecting to mysql');
        mysqli_select_db($conn, $db);

        $this->dbConnection = $conn;
    }

    function dbSelect($sql) {

        // run the query
        if ($result = mysqli_query($this->dbConnection,$sql)) {
            echo 'got a result';
        } else {
            echo 'error';
        }
        return $result;
    }

// end of dbSelect function
}

function pr($data){
    echo "<pre>";
    print_r($data);
}
// end of class