<?php

echo " start time " . date('Y-m-d H:i:s') . "<br>";
require("db/Db.class.php");
include_once("common-function.php");
// Creates the instance
echo "<h1>Movies</h1>";
$db_new = new Db(NEW_DB_NAME);
//$result_new = $db_new->query("SELECT * FROM m8t7_posts limit 1");
//pr($result_new);

echo "<hr><br><h1>Movies</h1>";
$db_old = new Db("mt_main");
$result_old = $db_old->query("SELECT * FROM dbo_moviesmaster order by MovieId limit 10001, 5000"); // WHERE MovieId='21259'
//pr($result_old); die;
$movies = array("ID" => "",
    "post_author" => "",
    "post_date" => "LastModified",
    "post_date_gmt" => "LastModified",
    "post_content" => "Synopsis",
    "post_title" => "MovieName",
    "post_status" => "",
    "post_excerpt" => "",
    "comment_status" => "",
    "ping_status" => "",
    "post_password" => "",
    "post_name" => "MovieName",
    "to_ping" => "",
    "pinged" => "",
    "post_modified" => "LastModified",
    "post_modified_gmt" => "LastModified",
    "post_content_filtered" => "",
    "post_parent" => "",
    "guid" => "MovieName",
    "menu_order" => "",
    "post_type" => "",
    "post_mime_type" => "",
    "comment_count" => "",
    "old_id" => "MovieId");


$movies_main_default_values = array("ID" => "",
    "post_author" => "1",
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
    "post_type" => "movie",
    "post_mime_type" => "",
    "comment_count" => "");

$movies_meta = array(
    "wpcf-year-of-release" => "YearOfRelease",
    "wpcf-month-of-release" => "MonthOfRelease",
    "wpcf-date-of-release" => "DateOfRelease",
    "wpcf-movie-status" => "MovieStatus",
    "wpcf-movie-official-web-site" => "OfficialSite",
    "wpcf-movie-official-facebook" => "OfficialFacebook",
    "wpcf-movie-official-twitter" => "OfficialTwitter",
    "wpcf-movie-official-youtube" => "OfficialYouTube",
    "wpcf-movie-official-blog" => "OfficialBlog",
    "wpcf-movie-official-instagram" => "OfficialInstagram",
    "wpcf-sound" => "Sound",
    "wpcf-screen-ratio" => "ScreenRatio",
    "wpcf-stock" => "Stock",
    "wpcf-gauge" => "Gauge",
    "wpcf-format-available" => "FormatAvailable",
    "wpcf-duration" => "Duration",
    "wpcf-length" => "Length",
    "wpcf-type" => "Type",
    "wpcf-certificate" => "Certificate",
    "wpcf-censor-certificate-number" => "CensorCertificateNumber",
    "wpcf-additional-information" => "AdditionalInfo",
    "wpcf-language" => "language",
    "wpcf-itune-link" => "iTuneLink",
    "wpcf-google-play-link" => "GoogleLink",
    "wpcf-amazon-link" => "AmazonLink",
    "wpcf-movie-id" => "MovieId",
    "wpcf-also-known-as" => "AKA",
    "wpcf-search-keywords" => "MSearchKeywords",
    "wpcf-tag-line" => "TagLine",
);

$movies_meta_default_values = array(
    "wpcf-cast" => "",
    "wpcf-director" => "",
    "wpcf-producer" => "",
    "wpcf-music-director" => "",
    "wpcf-lyricist" => "",
    "wpcf-playback-singer" => "",
    "wpcf-presenter" => "",
    "wpcf-story-writer" => "",
    "wpcf-cinematographer" => "",
    "wpcf-editor" => "",
    "wpcf-production-designer" => "",
    "wpcf-executive-producer" => "",
    "wpcf-choreographer" => "",
    "wpcf-dialogue-writer" => "",
    "wpcf-guest-appearance" => "",
    "wpcf-special-appearance" => "",
    "wpcf-action-director" => "",
    "wpcf-publicity-designer" => "",
    "wpcf-art-director" => "",
    "wpcf-associate-producer" => "",
    "wpcf-screenplay-writer" => "",
    "wpcf-costume-designer" => "",
    "wpcf-sound-designer" => "",
    "wpcf-make-up-designer" => "",
    "wpcf-hair-designer" => "",
    "wpcf-gaffer" => "",
    "wpcf-still-photographer" => "",
    "wpcf-background-sound" => "",
    "wpcf-visual-effects" => "",
    "wpcf-publicity-printer" => "",
    "wpcf-associate-director" => "",
    "wpcf-first-assistant-director" => "",
    "wpcf-assistant-director" => "",
    "wpcf-chief-of-production" => "",
    "wpcf-production-manager" => "",
    "wpcf-chief-assistant-cinematographer" => "",
    "wpcf-assistant-cinematographer" => "",
    "wpcf-chief-assistant-sound-designer" => "",
    "wpcf-production-executives" => "",
    "wpcf-assistant-sound-designer" => "",
    "wpcf-chief-assistant-choreographer" => "",
    "wpcf-assistant-choreographer" => "",
    "wpcf-on-air-promos" => "",
    "wpcf-pro" => "",
    "wpcf-continutiy-photographer-stills" => "",
    "wpcf-music-by" => "",
    "wpcf-banner" => "",
    "wpcf-co-producer" => "",
    "wpcf-line-producer" => "",
    "wpcf-lab" => "",
    "wpcf-second-assistant-director" => "",
    "wpcf-chief-assistant-editor" => "",
    "wpcf-assistant-editor" => "",
    "wpcf-music-company" => "",
    "wpcf-mixing-engineer" => "",
    "wpcf-song-recording-engineer" => "",
    "wpcf-song-director" => "",
    "wpcf-creative-producer" => "",
    "wpcf-audiography" => "",
    "wpcf-casting-director" => "",
    "wpcf-associate-screenplay" => "",
    "wpcf-marketing-and-promotions" => "",
    "wpcf-publicity-consultant" => "",
    "wpcf-chartered-accountant" => "",
    "wpcf-creative-director" => "",
    "wpcf-internet-campaign" => "",
    "wpcf-first-assistant-cinematographer" => "",
    "wpcf-second-assistant-cinematographer" => "",
    "wpcf-fous-puller" => "",
    "wpcf-script-supervisor-continuity" => "",
    "wpcf-production-co-ordinators" => "",
    "wpcf-chief-assistant-action-director" => "",
    "wpcf-assistant-action-director" => "",
    "wpcf-action-co-ordinator" => "",
    "wpcf-chief-assistant-art-director" => "",
    "wpcf-assistant-art-director" => "",
    "wpcf-chief-assistant-costume-designer" => "",
    "wpcf-assistant-costume-designer" => "",
    "wpcf-camera-attendants" => "",
    "wpcf-sound-attendants" => "",
    "wpcf-boom-operators" => "",
    "wpcf-steadicam-operators" => "",
    "wpcf-jimmy-jib-operators" => "",
    "wpcf-crane-operators" => "",
    "wpcf-assistant-make-up-designer" => "",
    "wpcf-dressman" => "",
    "wpcf-film-scanning-supervisor" => "",
    "wpcf-film-scanning-operator" => "",
    "wpcf-arri-recording" => "",
    "wpcf-visual-effects-supervisor" => "",
    "wpcf-storyboard" => "",
    "wpcf-modelers" => "",
    "wpcf-animators" => "",
    "wpcf-sound-post-production" => "",
    "wpcf-foley-artiste" => "",
    "wpcf-dubbing-and-foley-studio" => "",
    "wpcf-recordist-mixer" => "",
    "wpcf-re-recording-assistant" => "",
    "wpcf-negative-editor" => "",
    "wpcf-special-stills" => "",
    "wpcf-accounts-department" => "",
    "wpcf-censor-pro" => "",
    "wpcf-digital-intermediate" => "",
    "wpcf-di-colorist" => "",
    "wpcf-re-recording-engineer" => "",
    "wpcf-chief-assistant-director" => "",
    "wpcf-sync-sound" => "",
    "wpcf-legal-advisor" => "",
    "wpcf-supervising-producer" => "",
    "wpcf-website-design" => "",
    "wpcf-assistant-line-producer" => "",
    "wpcf-friendly-appearance" => "",
    "wpcf-media-relations" => "",
    "wpcf-script-consultant" => "",
    "wpcf-special-effects" => "",
    "wpcf-sound-recording" => "",
    "wpcf-associate-editor" => "",
    "wpcf-production-accountant" => "",
    "wpcf-assistant-production-designer" => "",
    "wpcf-assistant-publicity-designer" => "",
    "wpcf-digital-compositor" => "",
    "wpcf-production-assistant" => "",
    "wpcf-action-consultant" => "",
    "wpcf-set-decoration" => "",
    "wpcf-assistant-mixing-engineer" => "",
    "wpcf-third-assistant-director" => "",
    "wpcf-special-effects-supervisor" => "",
    "wpcf-post-production-supervisor" => "",
    "wpcf-production-supervisor" => "",
    "wpcf-creative-assistant-director" => "",
    "wpcf-sound-editor" => "",
    "wpcf-project-designer" => "",
    "wpcf-co-editor" => "",
    "wpcf-production-controller" => "",
    "wpcf-special-effects-co-ordinator" => "",
    "wpcf-chief-production-executive" => "",
    "wpcf-visual-effects-animator" => "",
    "wpcf-dialogue-editor" => "",
    "wpcf-executive-director" => "",
    "wpcf-special-makeup-effects" => "",
    "wpcf-first-assistant-editor" => "",
    "wpcf-sound-effects" => "",
    "wpcf-sound-effects-editor" => "",
    "wpcf-sound-engineer" => "",
    "wpcf-production-consultant" => "",
    "wpcf-publicist" => "",
    "wpcf-digital-effects-artist" => "",
    "wpcf-associate-production-designer" => "",
    "wpcf-key-costumer" => "",
    "wpcf-title-track-music-director" => "",
    "wpcf-title-track-choreographer" => "",
    "wpcf-concept" => "",
);


foreach ($result_old as $key => $row) {
    echo "<br> old movie id: " . $row["MovieId"];
    $movies_main = $movies_main_default_values;
    $movies_meta_default = $movies_meta_default_values;
    foreach ($movies as $mapKey => $mapValue) {
        if (!empty($mapValue)) {
            switch ($mapKey) {
                case "post_name":
                    $movies_main[$mapKey] = clean(strtolower($row[$mapValue]));
                    break;
                case "post_content":
                    $movies_main[$mapKey] = $row[$mapValue];
                    break;
                case "guid":
                    $movies_main[$mapKey] = "http://localhost/movietalkies/movies/" . clean(strtolower($row[$mapValue]));
                    break;

                default;
                    $movies_main[$mapKey] = $row[$mapValue];
                    break;
            }
        }
    }

    //pr($movies_main, 1);
    foreach ($movies_meta as $mapKey => $mapValue) {
        if (!empty($mapKey)) {

            switch ($mapKey) {
                case 'wpcf-date-of-release':
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

    /* ================= */
    //pr($movies_main);
    $db_new->bindMore($movies_main);
    $insert_artist = "INSERT INTO m8t7_posts (" . implode(",", array_keys($movies_main)) . ") values (:" . implode(",:", array_keys($movies_main)) . ")";
    //echo $insert_artist;
    $result = $db_new->query($insert_artist);

    if ($result > 0) {

        $insert_id = $db_new->lastInsertId();
        echo "<br> Inserted Movie id: " . $insert_id;

        $mtarelation = $db_old->query("SELECT MovieTitleId, Artistid FROM `dbo_mtarelation` WHERE MovieId = " . $row['MovieId']); // WHERE MovieId='21259'
        $mtrelation_mapping = mtrelation();

        foreach ($mtarelation as $keymt => $valuemt) {
            $castValue = $valuemt["MovieTitleId"];
            $castTitle = $mtrelation_mapping[$castValue];
            $newArtistid = get_new_artistid_array($valuemt["Artistid"], 'array');
            $newArtistid = implode(",", $newArtistid);
            //pr($newArtistid,1);
            if (!empty($movies_meta_default[$castTitle])) {
                $movies_meta_default[$castTitle] .= "," . $newArtistid;
            } else {
                $movies_meta_default[$castTitle] = $newArtistid;
            }
            //echo "<br>".$insert_id.", ". $movies_meta_default[$castTitle].", ". $newArtistid.", ". "'movie' ". " casttitle ".$castTitle;
            insert_relation_meta($insert_id, $castTitle, $newArtistid, 'movie');
        }
        //pr($mtrelation_mapping,1);
        //pr($mtarelation, 0);
        //pr($movies_meta_default, 0);
        insert_genre_by_postid($insert_id, $row["MovieId"]);
        insert_movie_category($insert_id, $row['IndustryId']);

        $field_value = array();
        $field_relation_value = array();

        foreach ($movies_meta_default as $key_meta => $row_meta) {

            /* $meta_value = get_artist_by_movieid($movies_main['old_id'], $key_meta, $insert_id);
              if (!empty($meta_value)) {
              $field_relation_value[] = "'" . $insert_id . "','" . addslashes($key_meta) . "','" . addslashes($row_meta) . "'";
              }
              $row_meta = !empty($meta_value) ? $meta_value : $row_meta;
             * 
             */
            if (!empty($row_meta)) {
                $field_value[] = "'" . $insert_id . "','" . addslashes($key_meta) . "','" . addslashes($row_meta) . "'";
            }
            //$field_value[] = "'" . $insert_id . "','" . addslashes($key_meta) . "','" . addslashes($row_meta) . "'";
        }
        $insert_artist_meta = "INSERT INTO m8t7_postmeta (post_id, meta_key , meta_value) values (" . implode("),(", $field_value) . ")";

        $result_meta = $db_new->query($insert_artist_meta);
    }
    //exit;
}
echo " <br>end time " . date('Y-m-d H:i:s') . "<br>";
exit;
