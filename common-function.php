<?php

ini_set('max_execution_time', 30000);
define('APACHE_MIME_TYPES_URL', 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');
define('UPLOAD_DIR_URL', 'http://localhost/movietalkies/wp-content/uploads/');
define('NEW_DB_NAME', 'mt_nov');
//define('NEW_DB_NAME','movie_local');
define('WEBSITE_URL', 'http://localhost/movietalkies/');

function clean($string) {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

    return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

function generateUpToDateMimeArray($url) {
    $s = array();
    foreach (@explode("\n", @file_get_contents($url))as $x)
        if (isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1]) && ($c = count($out[1])) > 1)
            for ($i = 1; $i < $c; $i++)
                $s[] = '&nbsp;&nbsp;&nbsp;\'' . $out[1][$i] . '\' => \'' . $out[1][0] . '\'';
    return @sort($s) ? '$mime_types = array(<br />' . implode($s, ',<br />') . '<br />);' : false;
}

function get_new_movieid_array($old_ids) {

    GLOBAL $db_new;
    $map = array();
    if (is_array($old_ids)) {
        $old_ids = implode(', ', $old_ids);
    }

    if (!empty($old_ids)) {
        $results = $db_new->query("select old_id, ID from m8t7_posts WHERE old_id IN({$old_ids}) and post_type = 'movie'");
        if ($results > 0) {
			
            foreach ($results as $result) {
                $map[$result['old_id']] = $result['ID'];
            }
        }
    }
    return $map;
}

function get_new_artistid_array($old_pids,$type = '') {


    GLOBAL $db_new;
    if($type == '')
        $old_pids = implode(', ', $old_pids);
        //echo "select old_id, ID from m8t7_posts WHERE old_id IN({$old_pids}) and post_type Like 'artiste' "; exit;
    $resultss = $db_new->query("select old_id, ID from m8t7_posts WHERE old_id IN({$old_pids}) and post_type Like 'artiste' ");
    $maps = array();
    if ($resultss > 0) {
        foreach ($resultss as $resultsss) {
            $maps[$resultsss['old_id']] = $resultsss['ID'];
        }
    }

    return $maps;
}

// Get Editor Id
function new_author_id($id) {
	$data = '';
    $id_array = Array
	(
		1 => 35926,
		2 => 35927,
		3 => 35928,
		7 => 35929,
		9 => 35930,
		10 => 35931,
		13 => 35932,
		15 => 35933,
		16 => 35934,
		17 => 35935,
		18 => 35936
	);
	
	if(!empty($id)){
		$data = $id_array[$id];
	}
    return $data;
}

function get_artist_by_movieid($old_id = '', $key_meta = '', $new_id) {

    GLOBAL $db_old;
    $condition = '';
    $artist = array();

    $mapping = array(
        "wpcf-cast" => 1,
        "wpcf-director" => 3,
        "wpcf-producer" => 4,
        "wpcf-music-director" => 5,
        "wpcf-lyricist" => 6,
        "wpcf-playback-singer" => 7,
        "wpcf-presenter" => 8,
        "wpcf-story-writer" => 9,
        "wpcf-cinematographer" => 10,
        "wpcf-editor" => 11,
        "wpcf-production-designer" => 12,
        "wpcf-executive-producer" => 13,
        "wpcf-choreographer" => 14,
        "wpcf-dialogue-writer" => 15,
        "wpcf-guest-appearance" => 16,
        "wpcf-special-appearance" => 17,
        "wpcf-action-director" => 18,
        "wpcf-publicity-designer" => 19,
        "wpcf-art-director" => 20,
        "wpcf-associate-producer" => 21,
        "wpcf-screenplay-writer" => 22,
        "wpcf-costume-designer" => 23,
        "wpcf-sound-designer" => 24,
        "wpcf-make-up-designer" => 25,
        "wpcf-hair-designer" => 26,
        "wpcf-gaffer" => 27,
        "wpcf-still-photographer" => 28,
        "wpcf-background-sound" => 29,
        "wpcf-visual-effects" => 30,
        "wpcf-publicity-printer" => 35,
        "wpcf-associate-director" => 36,
        "wpcf-first-assistant-director" => 37,
        "wpcf-assistant-director" => 38,
        "wpcf-chief-of-production" => 39,
        "wpcf-production-manager" => 40,
        "wpcf-chief-assistant-cinematographer" => 41,
        "wpcf-assistant-cinematographer" => 44,
        "wpcf-chief-assistant-sound-designer" => 45,
        "wpcf-production-executives" => 46,
        "wpcf-assistant-sound-designer" => 47,
        "wpcf-chief-assistant-choreographer" => 48,
        "wpcf-assistant-choreographer" => 49,
        "wpcf-on-air-promos" => 50,
        "wpcf-pro" => 51,
        "wpcf-continutiy-photographer-stills" => 52,
        "wpcf-music-by" => 53,
        "wpcf-banner" => 54,
        "wpcf-co-producer" => 55,
        "wpcf-line-producer" => 56,
        "wpcf-lab" => 57,
        "wpcf-second-assistant-director" => 58,
        "wpcf-chief-assistant-editor" => 59,
        "wpcf-assistant-editor" => 60,
        "wpcf-music-company" => 61,
        "wpcf-mixing-engineer" => 63,
        "wpcf-song-recording-engineer" => 64,
        "wpcf-song-director" => 65,
        "wpcf-creative-producer" => 66,
        "wpcf-audiography" => 67,
        "wpcf-casting-director" => 68,
        "wpcf-associate-screenplay" => 69,
        "wpcf-marketing-and-promotions" => 70,
        "wpcf-publicity-consultant" => 71,
        "wpcf-chartered-accountant" => 72,
        "wpcf-creative-director" => 73,
        "wpcf-internet-campaign" => 74,
        "wpcf-first-assistant-cinematographer" => 75,
        "wpcf-second-assistant-cinematographer" => 76,
        "wpcf-fous-puller" => 77,
        "wpcf-script-supervisor-continuity" => 78,
        "wpcf-production-co-ordinators" => 79,
        "wpcf-chief-assistant-action-director" => 80,
        "wpcf-assistant-action-director" => 81,
        "wpcf-action-co-ordinator" => 82,
        "wpcf-chief-assistant-art-director" => 83,
        "wpcf-assistant-art-director" => 84,
        "wpcf-chief-assistant-costume-designer" => 85,
        "wpcf-assistant-costume-designer" => 86,
        "wpcf-camera-attendants" => 87,
        "wpcf-sound-attendants" => 88,
        "wpcf-boom-operators" => 89,
        "wpcf-steadicam-operators" => 90,
        "wpcf-jimmy-jib-operators" => 91,
        "wpcf-crane-operators" => 92,
        "wpcf-assistant-make-up-designer" => 93,
        "wpcf-dressman" => 94,
        "wpcf-film-scanning-supervisor" => 95,
        "wpcf-film-scanning-operator" => 96,
        "wpcf-arri-recording" => 97,
        "wpcf-visual-effects-supervisor" => 98,
        "wpcf-storyboard" => 99,
        "wpcf-modelers" => 100,
        "wpcf-animators" => 101,
        "wpcf-sound-post-production" => 102,
        "wpcf-foley-artiste" => 103,
        "wpcf-dubbing-and-foley-studio" => 104,
        "wpcf-recordist-mixer" => 105,
        "wpcf-re-recording-assistant" => 106,
        "wpcf-negative-editor" => 107,
        "wpcf-special-stills" => 108,
        "wpcf-accounts-department" => 109,
        "wpcf-censor-pro" => 110,
        "wpcf-digital-intermediate" => 111,
        "wpcf-di-colorist" => 112,
        "wpcf-re-recording-engineer" => 113,
        "wpcf-chief-assistant-director" => 114,
        "wpcf-sync-sound" => 115,
        "wpcf-legal-advisor" => 116,
        "wpcf-supervising-producer" => 117,
        "wpcf-website-design" => 119,
        "wpcf-assistant-line-producer" => 120,
        "wpcf-friendly-appearance" => 121,
        "wpcf-media-relations" => 123,
        "wpcf-script-consultant" => 124,
        "wpcf-special-effects" => 125,
        "wpcf-sound-recording" => 126,
        "wpcf-associate-editor" => 127,
        "wpcf-production-accountant" => 128,
        "wpcf-assistant-production-designer" => 129,
        "wpcf-assistant-publicity-designer" => 130,
        "wpcf-digital-compositor" => 131,
        "wpcf-production-assistant" => 132,
        "wpcf-action-consultant" => 133,
        "wpcf-set-decoration" => 134,
        "wpcf-assistant-mixing-engineer" => 135,
        "wpcf-third-assistant-director" => 136,
        "wpcf-special-effects-supervisor" => 137,
        "wpcf-post-production-supervisor" => 138,
        "wpcf-production-supervisor" => 139,
        "wpcf-creative-assistant-director" => 140,
        "wpcf-sound-editor" => 141,
        "wpcf-project-designer" => 142,
        "wpcf-co-editor" => 143,
        "wpcf-production-controller" => 144,
        "wpcf-special-effects-co-ordinator" => 145,
        "wpcf-chief-production-executive" => 146,
        "wpcf-visual-effects-animator" => 147,
        "wpcf-dialogue-editor" => 148,
        "wpcf-executive-director" => 149,
        "wpcf-special-makeup-effects" => 150,
        "wpcf-first-assistant-editor" => 151,
        "wpcf-sound-effects" => 152,
        "wpcf-sound-effects-editor" => 153,
        "wpcf-sound-engineer" => 154,
        "wpcf-production-consultant" => 155,
        "wpcf-publicist" => 156,
        "wpcf-digital-effects-artist" => 157,
        "wpcf-associate-production-designer" => 158,
        "wpcf-key-costumer" => 159,
        "wpcf-title-track-music-director" => 160,
        "wpcf-title-track-choreographer" => 161,
        "wpcf-concept" => 162
    );

    if (!empty($key_meta)) {
        $titleId = isset($mapping[$key_meta]) ? $mapping[$key_meta] : '';

        if (!empty($titleId)) {

            $sql_query = "SELECT Artistid as id FROM dbo_mtarelation WHERE `MovieID`='{$old_id}' AND `MovieTitleId` = '{$titleId}'";
            //echo "<br>sql_query " . $sql_query;
            $results = $db_old->query($sql_query);
            if (!empty($results)) {
                foreach ($results as $val) {
                    $artist[] = $val['id'];
                }
                $new = get_new_artist_id($artist);
                // insert in relation table
                foreach ($new as $newItem) {
                    insert_relation_meta($new_id, $key_meta, $newItem, 'movie');
                }

                return implode(',', $new);
            }
        }
    }

    return "";
}

function insert_genre_by_postid($movie_id, $old_id) {

    GLOBAL $db_old, $db_new;
    $sql = "SELECT GenreId FROM `dbo_moviesgenrerelation` WHERE MovieId='{$old_id}'";
    $results = $db_old->query($sql);

    $mapping = array(
        "1" => 25,
        "2" => 26,
        "3" => 27,
        "4" => 28,
        "5" => 29,
        "6" => 30,
        "7" => 31,
        "8" => 32,
        "9" => 33,
        "10" => 34,
        "11" => 35,
        "12" => 36,
        "13" => 37,
        "14" => 38,
        "15" => 39,
        "16" => 40,
        "17" => 41,
        "18" => 42,
        "20" => 43,
        "21" => 44,
        "22" => 45,
        "23" => 46,
        "24" => 47,
        "25" => 48,
        "26" => 49,
        "27" => 50,
        "28" => 51,
        "29" => 52,
        "30" => 53,
        "31" => 54,
        "32" => 55,
        "33" => 56,
        "34" => 57,
        "35" => 58,
        "36" => 59,
        "37" => 60,
        "38" => 61,
        "39" => 62,
        "40" => 63,
    );

    if (!empty($results)) {
        foreach ($results as $genre_id) {
            $new_genere_id = isset($mapping[$genre_id['GenreId']]) ? $mapping[$genre_id['GenreId']] : 0;
            $field_value[] = "'" . $movie_id . "','" . addslashes($new_genere_id) . "','0'";
        }

        $insert_genre = $db_new->query("INSERT INTO m8t7_term_relationships (object_id, term_taxonomy_id , term_order) values (" . implode("),(", $field_value) . ")");
        if ($insert_genre > 0) {
            echo ', Genre(done), ';
        } else {
            echo ', Genre(error), ';
        }
    } else {
        echo ', Genre(notfound), ';
    }
}

function insert_movie_category($movie_id, $cat_id) {

    GLOBAL $db_old, $db_new;
    $field_value = array();
    $mapping = array(
        "1" => 19,
        "2" => 20,
        "3" => 21,
        "4" => 22,
        "5" => 23,
        "6" => 24
    );

    $new_cat_id = isset($mapping[$cat_id]) ? $mapping[$cat_id] : '';
    if (isset($new_cat_id) && !empty($new_cat_id)) {

        $insert_category = $db_new->query("INSERT INTO m8t7_term_relationships (object_id, term_taxonomy_id , term_order) values ('{$movie_id}', '{$new_cat_id}', 0)");
        if ($insert_category > 0) {
            echo ', Movie Category(done), ';
        } else {
            echo ', Movie Category(error), ';
        }
    } else {
        echo ', Movie Category(notfound), ';
    }
}

function import_featured_image($file_path, $post_parent) {
    global $db_new;
    $upload_path = UPLOAD_DIR_URL . $file_path;
    $insert_post = "INSERT INTO m8t7_posts (post_type, guid, post_status, post_mime_type,post_parent) VALUES ('attachment', '" . $upload_path . "', 'inherit', 'image/jpeg'," . $post_parent . ")";
    $result = $db_new->query($insert_post);
    $attachmentid = $db_new->lastInsertId();
    $insert_post_meta = "INSERT INTO m8t7_postmeta (meta_value, meta_key, post_id) VALUES ('" . $file_path . "', '_wp_attached_file'," . $attachmentid . ")";
    $result_meta_query = $db_new->query($insert_post_meta);
    $insert_thumbnail_meta = "INSERT INTO m8t7_postmeta (meta_value, meta_key, post_id) VALUES (" . $attachmentid . ", '_thumbnail_id'," . $post_parent . ")";
    $result_thumbnail_meta = $db_new->query($insert_thumbnail_meta);
}

// insert record in relation table

function insert_relation_meta($post_id, $meta_key, $meta_value, $posttype) {

    global $db_new;

    if (in_array($meta_key, all_relational_fields()) && !empty($post_id) && !empty($meta_key) && !empty($meta_value) && !empty($posttype)) {

        $query = "INSERT INTO m8t7_relationships (post_id, meta_key , meta_value, posttype) values('{$post_id}', '{$meta_key}', '{$meta_value}', '{$posttype}' )";
        $result_meta = $db_new->query($query);
    }
    return;
}

function all_relational_fields() {
    return array(
        "wpcf-cast",
        "wpcf-director",
        "wpcf-producer",
        "wpcf-music-director",
        "wpcf-lyricist",
        "wpcf-playback-singer",
        "wpcf-presenter",
        "wpcf-story-writer",
        "wpcf-cinematographer",
        "wpcf-editor",
        "wpcf-production-designer",
        "wpcf-executive-producer",
        "wpcf-choreographer",
        "wpcf-dialogue-writer",
        "wpcf-guest-appearance",
        "wpcf-special-appearance",
        "wpcf-action-director",
        "wpcf-publicity-designer",
        "wpcf-art-director",
        "wpcf-associate-producer",
        "wpcf-screenplay-writer",
        "wpcf-costume-designer",
        "wpcf-sound-designer",
        "wpcf-make-up-designer",
        "wpcf-hair-designer",
        "wpcf-gaffer",
        "wpcf-still-photographer",
        "wpcf-background-sound",
        "wpcf-visual-effects",
        "wpcf-publicity-printer",
        "wpcf-associate-director",
        "wpcf-first-assistant-director",
        "wpcf-assistant-director",
        "wpcf-chief-of-production",
        "wpcf-production-manager",
        "wpcf-chief-assistant-cinematographer",
        "wpcf-assistant-cinematographer",
        "wpcf-chief-assistant-sound-designer",
        "wpcf-production-executives",
        "wpcf-assistant-sound-designer",
        "wpcf-chief-assistant-choreographer",
        "wpcf-assistant-choreographer",
        "wpcf-on-air-promos",
        "wpcf-pro",
        "wpcf-continutiy-photographer-stills",
        "wpcf-music-by",
        "wpcf-banner",
        "wpcf-co-producer",
        "wpcf-line-producer",
        "wpcf-lab",
        "wpcf-second-assistant-director",
        "wpcf-chief-assistant-editor",
        "wpcf-assistant-editor",
        "wpcf-music-company",
        "wpcf-mixing-engineer",
        "wpcf-song-recording-engineer",
        "wpcf-song-director",
        "wpcf-creative-producer",
        "wpcf-audiography",
        "wpcf-casting-director",
        "wpcf-associate-screenplay",
        "wpcf-marketing-and-promotions",
        "wpcf-publicity-consultant",
        "wpcf-chartered-accountant",
        "wpcf-creative-director",
        "wpcf-internet-campaign",
        "wpcf-first-assistant-cinematographer",
        "wpcf-second-assistant-cinematographer",
        "wpcf-fous-puller",
        "wpcf-script-supervisor-continuity",
        "wpcf-production-co-ordinators",
        "wpcf-chief-assistant-action-director",
        "wpcf-assistant-action-director",
        "wpcf-action-co-ordinator",
        "wpcf-chief-assistant-art-director",
        "wpcf-assistant-art-director",
        "wpcf-chief-assistant-costume-designer",
        "wpcf-assistant-costume-designer",
        "wpcf-camera-attendants",
        "wpcf-sound-attendants",
        "wpcf-boom-operators",
        "wpcf-steadicam-operators",
        "wpcf-jimmy-jib-operators",
        "wpcf-crane-operators",
        "wpcf-assistant-make-up-designer",
        "wpcf-dressman",
        "wpcf-film-scanning-supervisor",
        "wpcf-film-scanning-operator",
        "wpcf-arri-recording",
        "wpcf-visual-effects-supervisor",
        "wpcf-storyboard",
        "wpcf-modelers",
        "wpcf-animators",
        "wpcf-sound-post-production",
        "wpcf-foley-artiste",
        "wpcf-dubbing-and-foley-studio",
        "wpcf-recordist-mixer",
        "wpcf-re-recording-assistant",
        "wpcf-negative-editor",
        "wpcf-special-stills",
        "wpcf-accounts-department",
        "wpcf-censor-pro",
        "wpcf-digital-intermediate",
        "wpcf-di-colorist",
        "wpcf-re-recording-engineer",
        "wpcf-chief-assistant-director",
        "wpcf-sync-sound",
        "wpcf-legal-advisor",
        "wpcf-supervising-producer",
        "wpcf-website-design",
        "wpcf-assistant-line-producer",
        "wpcf-friendly-appearance",
        "wpcf-media-relations",
        "wpcf-script-consultant",
        "wpcf-special-effects",
        "wpcf-sound-recording",
        "wpcf-associate-editor",
        "wpcf-production-accountant",
        "wpcf-assistant-production-designer",
        "wpcf-assistant-publicity-designer",
        "wpcf-digital-compositor",
        "wpcf-production-assistant",
        "wpcf-action-consultant",
        "wpcf-set-decoration",
        "wpcf-assistant-mixing-engineer",
        "wpcf-third-assistant-director",
        "wpcf-special-effects-supervisor",
        "wpcf-post-production-supervisor",
        "wpcf-production-supervisor",
        "wpcf-creative-assistant-director",
        "wpcf-sound-editor",
        "wpcf-project-designer",
        "wpcf-co-editor",
        "wpcf-production-controller",
        "wpcf-special-effects-co-ordinator",
        "wpcf-chief-production-executive",
        "wpcf-visual-effects-animator",
        "wpcf-dialogue-editor",
        "wpcf-executive-director",
        "wpcf-special-makeup-effects",
        "wpcf-first-assistant-editor",
        "wpcf-sound-effects",
        "wpcf-sound-effects-editor",
        "wpcf-sound-engineer",
        "wpcf-production-consultant",
        "wpcf-publicist",
        "wpcf-digital-effects-artist",
        "wpcf-associate-production-designer",
        "wpcf-key-costumer",
        "wpcf-title-track-music-director",
        "wpcf-title-track-choreographer",
        "wpcf-concept",
    );
}

// return new artist id in array
function get_new_artist_id($old_ids, $type = 'array') {

//	print_r($old_ids); die;
    GLOBAL $db_new;

	//echo $old_ids.'</br>';
    if ($type == 'array') {
	$old_ids =  array_filter($old_ids);
        $old_ids = implode(',', $old_ids);
		
    } else{
		$old_ids = explode(',', $old_ids);
		$old_ids =  array_filter($old_ids);
		$old_ids = implode(',', $old_ids);
	}
	//echo $old_ids; die;
	if(!empty( $old_ids ))
	{
		$results = $db_new->query("select old_id, ID from m8t7_posts WHERE old_id IN({$old_ids}) AND post_type = 'artiste'");
		//pr($old_ids); die;
		$map = array();
		if ($results > 0) {
			foreach ($results as $result) {
				$map[$result['old_id']] = $result['ID'];
			}
		}
		return $map;
	}
	return '';
}

function mtrelation() {
    return $mtrelation_mapping = array(
        1 => "wpcf-cast",
        3 => "wpcf-director",
        4 => "wpcf-producer",
        5 => "wpcf-music-director",
        6 => "wpcf-lyricist",
        7 => "wpcf-playback-singer",
        8 => "wpcf-presenter",
        9 => "wpcf-story-writer",
        10 => "wpcf-cinematographer",
        11 => "wpcf-editor",
        12 => "wpcf-production-designer",
        13 => "wpcf-executive-producer",
        14 => "wpcf-choreographer",
        15 => "wpcf-dialogue-writer",
        16 => "wpcf-guest-appearance",
        17 => "wpcf-special-appearance",
        18 => "wpcf-action-director",
        19 => "wpcf-publicity-designer",
        20 => "wpcf-art-director",
        21 => "wpcf-associate-producer",
        22 => "wpcf-screenplay-writer",
        23 => "wpcf-costume-designer",
        24 => "wpcf-sound-designer",
        25 => "wpcf-make-up-designer",
        26 => "wpcf-hair-designer",
        27 => "wpcf-gaffer",
        28 => "wpcf-still-photographer",
        29 => "wpcf-background-sound",
        30 => "wpcf-visual-effects",
        35 => "wpcf-publicity-printer",
        36 => "wpcf-associate-director",
        37 => "wpcf-first-assistant-director",
        38 => "wpcf-assistant-director",
        39 => "wpcf-chief-of-production",
        40 => "wpcf-production-manager",
        41 => "wpcf-chief-assistant-cinematographer",
        44 => "wpcf-assistant-cinematographer",
        45 => "wpcf-chief-assistant-sound-designer",
        46 => "wpcf-production-executives",
        47 => "wpcf-assistant-sound-designer",
        48 => "wpcf-chief-assistant-choreographer",
        49 => "wpcf-assistant-choreographer",
        50 => "wpcf-on-air-promos",
        51 => "wpcf-pro",
        52 => "wpcf-continutiy-photographer-stills",
        53 => "wpcf-music-by",
        54 => "wpcf-banner",
        55 => "wpcf-co-producer",
        56 => "wpcf-line-producer",
        57 => "wpcf-lab",
        58 => "wpcf-second-assistant-director",
        59 => "wpcf-chief-assistant-editor",
        60 => "wpcf-assistant-editor",
        61 => "wpcf-music-company",
        63 => "wpcf-mixing-engineer",
        64 => "wpcf-song-recording-engineer",
        65 => "wpcf-song-director",
        66 => "wpcf-creative-producer",
        67 => "wpcf-audiography",
        68 => "wpcf-casting-director",
        69 => "wpcf-associate-screenplay",
        70 => "wpcf-marketing-and-promotions",
        71 => "wpcf-publicity-consultant",
        72 => "wpcf-chartered-accountant",
        73 => "wpcf-creative-director",
        74 => "wpcf-internet-campaign",
        75 => "wpcf-first-assistant-cinematographer",
        76 => "wpcf-second-assistant-cinematographer",
        77 => "wpcf-fous-puller",
        78 => "wpcf-script-supervisor-continuity",
        79 => "wpcf-production-co-ordinators",
        80 => "wpcf-chief-assistant-action-director",
        81 => "wpcf-assistant-action-director",
        82 => "wpcf-action-co-ordinator",
        83 => "wpcf-chief-assistant-art-director",
        84 => "wpcf-assistant-art-director",
        85 => "wpcf-chief-assistant-costume-designer",
        86 => "wpcf-assistant-costume-designer",
        87 => "wpcf-camera-attendants",
        88 => "wpcf-sound-attendants",
        89 => "wpcf-boom-operators",
        90 => "wpcf-steadicam-operators",
        91 => "wpcf-jimmy-jib-operators",
        92 => "wpcf-crane-operators",
        93 => "wpcf-assistant-make-up-designer",
        94 => "wpcf-dressman",
        95 => "wpcf-film-scanning-supervisor",
        96 => "wpcf-film-scanning-operator",
        97 => "wpcf-arri-recording",
        98 => "wpcf-visual-effects-supervisor",
        99 => "wpcf-storyboard",
        100 => "wpcf-modelers",
        101 => "wpcf-animators",
        102 => "wpcf-sound-post-production",
        103 => "wpcf-foley-artiste",
        104 => "wpcf-dubbing-and-foley-studio",
        105 => "wpcf-recordist-mixer",
        106 => "wpcf-re-recording-assistant",
        107 => "wpcf-negative-editor",
        108 => "wpcf-special-stills",
        109 => "wpcf-accounts-department",
        110 => "wpcf-censor-pro",
        111 => "wpcf-digital-intermediate",
        112 => "wpcf-di-colorist",
        113 => "wpcf-re-recording-engineer",
        114 => "wpcf-chief-assistant-director",
        115 => "wpcf-sync-sound",
        116 => "wpcf-legal-advisor",
        117 => "wpcf-supervising-producer",
        119 => "wpcf-website-design",
        120 => "wpcf-assistant-line-producer",
        121 => "wpcf-friendly-appearance",
        123 => "wpcf-media-relations",
        124 => "wpcf-script-consultant",
        125 => "wpcf-special-effects",
        126 => "wpcf-sound-recording",
        127 => "wpcf-associate-editor",
        128 => "wpcf-production-accountant",
        129 => "wpcf-assistant-production-designer",
        130 => "wpcf-assistant-publicity-designer",
        131 => "wpcf-digital-compositor",
        132 => "wpcf-production-assistant",
        133 => "wpcf-action-consultant",
        134 => "wpcf-set-decoration",
        135 => "wpcf-assistant-mixing-engineer",
        136 => "wpcf-third-assistant-director",
        137 => "wpcf-special-effects-supervisor",
        138 => "wpcf-post-production-supervisor",
        139 => "wpcf-production-supervisor",
        140 => "wpcf-creative-assistant-director",
        141 => "wpcf-sound-editor",
        142 => "wpcf-project-designer",
        143 => "wpcf-co-editor",
        144 => "wpcf-production-controller",
        145 => "wpcf-special-effects-co-ordinator",
        146 => "wpcf-chief-production-executive",
        147 => "wpcf-visual-effects-animator",
        148 => "wpcf-dialogue-editor",
        149 => "wpcf-executive-director",
        150 => "wpcf-special-makeup-effects",
        151 => "wpcf-first-assistant-editor",
        152 => "wpcf-sound-effects",
        153 => "wpcf-sound-effects-editor",
        154 => "wpcf-sound-engineer",
        155 => "wpcf-production-consultant",
        156 => "wpcf-publicist",
        157 => "wpcf-digital-effects-artist",
        158 => "wpcf-associate-production-designer",
        159 => "wpcf-key-costumer",
        160 => "wpcf-title-track-music-director",
        161 => "wpcf-title-track-choreographer",
        162 => "wpcf-concept"
    );
}

function get_new_movieid_withtitle_array($old_ids){
	
	GLOBAL $db_new;
	//$old_ids = implode( ', ', $old_ids ) ;
	
	$results = $db_new->query("select old_id,post_title, ID from m8t7_posts WHERE old_id = ".$old_ids." and post_type Like 'movie'");
	
	$data = array();
	if($results > 0 ) {
		foreach( $results as $result ){
		
			$data[$result['old_id']] = array( 'NewMovieId' =>  $result['ID'] , "post_title" => $result['post_title']);
			
		}
	}
	
	return $data;
}
function get_new_Artistid_withtitle_array($old_ids){
	
	GLOBAL $db_new;
	//$old_ids = implode( ', ', $old_ids ) ;
	
	$results = $db_new->query("select old_id,post_title, ID from m8t7_posts WHERE old_id = ".$old_ids." and post_type Like 'artiste'");
	
	$data = array();
	if($results > 0 ) {
		foreach( $results as $result ){
		
			$data[$result['old_id']] = array( 'NewArtisteId' =>  $result['ID'] , "post_title" => $result['post_title']);
			
		}
	}

	return $data;
}
function new_import_featured_image($file_path, $post_parent){
	global $db_new;
	$upload_path = "http://localhost/movietalkies/wp-content/uploads/".$file_path;
	$insert_post = "INSERT INTO m8t7_posts (post_type, guid, post_status, post_mime_type,post_parent) VALUES ('attachment', '".$upload_path."', 'inherit', 'image/jpeg',".$post_parent.")";
	$result = $db_new->query($insert_post);
	$attachmentid = $db_new->lastInsertId();
	$insert_post_meta = "INSERT INTO m8t7_postmeta (meta_value, meta_key, post_id) VALUES ('".$file_path."', '_wp_attached_file',".$attachmentid.")";
	$result_meta_query = $db_new->query($insert_post_meta);
	//$insert_thumbnail_meta = "INSERT INTO m8t7_postmeta (meta_value, meta_key, post_id) VALUES (".$attachmentid.", '_thumbnail_id',".$post_parent.")";
	//$result_thumbnail_meta = $db_new->query($insert_thumbnail_meta);
	return $attachmentid;
}


function insert_relation_meta_for_movie($post_id, $meta_key, $meta_value, $posttype) {

    global $db_new;

    if ( !empty($post_id) && !empty($meta_key) && !empty($meta_value) && !empty($posttype)) {

        $query = "INSERT INTO m8t7_relationships (post_id, meta_key , meta_value, posttype) values('{$post_id}', '{$meta_key}', '{$meta_value}', '{$posttype}' )";
        $result_meta = $db_new->query($query);
    }
    return;
}

function get_brodband_artist_relation($old_brodband_id){
	
	global $db_old;
	$insert_post = "SELECT * FROM `dbo_brodbandmovieartistrelation` WHERE `BrodbandMovieId` = '{$old_brodband_id}'";
	$results = $db_old->query($insert_post);
	
	$data = array();
	if($results > 0 ) {
		foreach( $results as $result ){
		
			$data[] = $result['ArtistId'];
		}
		
	}
	return $data;
}

function insert_single_postmeta($post_id, $meta_key, $meta_value){
	global $db_new;
	 $insert_post_meta = "INSERT INTO m8t7_postmeta ( post_id, meta_key,meta_value) VALUES (" . $post_id . ", '".$meta_key."','" . $meta_value . "')";
	$results = $db_new->query($insert_post_meta);
	
	if($results > 0 ) {
		
		return true;
	}
	return false;
}


function get_comma_separated_newmovieId($old_ids){

	GLOBAL $db_new;
    $map = array();
    if (is_array($old_ids)) {
        $old_ids = implode(', ', $old_ids);
    }

    if (!empty($old_ids)) {
        $results = $db_new->query("select old_id, ID from m8t7_posts WHERE old_id IN({$old_ids}) and post_type = 'movie'");
        if ($results > 0) {
            foreach ($results as $result) {
                $map[$result['old_id']] = $result['ID'];
            }
        }
    }
    return implode(', ', $map);
}

// get new artist id with seralize format
function get_new_artist_id_without_old_id($old_ids, $type = 'array') {

    GLOBAL $db_new;
    if ($type == 'array') {
        $old_ids = implode(', ', $old_ids);
    }
	if(!empty( $old_ids ))
	{
		
		$results = $db_new->query("select old_id, ID from m8t7_posts WHERE old_id IN({$old_ids}) AND post_type = 'artiste'");
		//pr($old_ids); die;
		$map = array();
		if ($results > 0) {
			foreach ($results as $result) {
				$map[] = "{$result['ID']}";
			}
		}
		
		return $map;
	}
	return '';
}
