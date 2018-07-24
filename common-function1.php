<?php
define('APACHE_MIME_TYPES_URL','http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

function generateUpToDateMimeArray($url){
    $s=array();
    foreach(@explode("\n",@file_get_contents($url))as $x)
        if(isset($x[0])&&$x[0]!=='#'&&preg_match_all('#([^\s]+)#',$x,$out)&&isset($out[1])&&($c=count($out[1]))>1)
            for($i=1;$i<$c;$i++)
                $s[]='&nbsp;&nbsp;&nbsp;\''.$out[1][$i].'\' => \''.$out[1][0].'\'';
    return @sort($s)?'$mime_types = array(<br />'.implode($s,',<br />').'<br />);':false;
}