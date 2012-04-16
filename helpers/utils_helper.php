<?php

function reform_title($title) {
    $title = strip_tags($title);
    // Preserve escaped octets.
    $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
    // Remove percent signs that are not part of an octet.
    $title = str_replace('%', '', $title);
    // Restore octets.
    $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
    //$title = strtolower($title);
    $title = preg_replace('/&.+?;/', '', $title); // kill entities
    //$title = str_replace('.', '-', $title);
    $title = preg_replace('/[^%a-zA-Z0-9\'"; $%^&*()<>_\-+=`~\]\\\|.,@#!\?\[:]/', '', $title);
    //$title = preg_replace('/\s+/', '-', $title);
    //$title = preg_replace('|-+|', '-', $title);
    $title = trim($title, '-');
    $title = trim($title, '.');
    return trim($title);
}

function reform_url($title) {
    $title = strip_tags($title);
    // Preserve escaped octets.
    $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
    // Remove percent signs that are not part of an octet.
    $title = str_replace('%', '', $title);
    // Restore octets.
    $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

    $title = trim($title, '-');
    $title = trim($title, '.');
    return trim($title);
}

function return_data($data) {
    return array(
        'movie_name' => trim($data[1]),
        'movie_channel_link' => trim($data[5]),
        'movie_release_date' => date('Y-m-d H:i:s', strtotime(trim($data[9]))),
        'movie_release_countries' => trim($data[11]),
        'all_links' => trim($data[6]),
        'all_actors' => trim($data[12]),
        'genre1' => trim($data[2]),
        'genre2' => trim($data[3]),
        'genre3' => trim($data[4])
    );
}

function return_data_reformed($data) {
    return array(
        'movie_name' => reform_title($data[1]),
        'movie_channel_link' => reform_url($data[5]),
        'movie_release_date' => date('Y-m-d H:i:s', strtotime(reform_title($data[9]))),
        'movie_release_countries' => reform_title($data[11]),
        'all_links' => reform_url($data[6]),
        'all_actors' => reform_url($data[12]),
        'genre1' => reform_title($data[2]),
        'genre2' => reform_title($data[3]),
        'genre3' => reform_title($data[4])
    );
}

// checking with curl if video still exists

function video_still_exists($link) {

    // checking if video exists from http code

    $ch = curl_init();

// set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

// grab URL and pass it to the browser
    $response = curl_exec($ch);
    if (!$response) {
        // echo 'no data found in curl';
        return;
    }

// close cURL resource, and free up system resources
    curl_close($ch);


    $pos = stripos($response, "\n");

    preg_match('/\s\d+/', substr($response, 0, $pos), $res_code);
    $res_code_mod = trim($res_code[0]);

    if ($res_code_mod != 200) {
        //echo 'http code prob';
        return;
    }
    
    //if the video has been removed
    
    $messages = array(
       'This file doesn\'t exist, or has been removed.'        
    );
    
    foreach($messages as $single)
        if(stripos($response, $single) !== false)
                return;

    return true;
}

function valid_single_link($link) {
    if (stripos($link, 'http://') === false) {
        //echo 'http not foound';
        if( stripos($link, 'https://') === false )
        return;
    }

    // all domains to avoid
    $invalid_domains = array(
        'affbuzzads.com'
    );

    foreach ($invalid_domains as $dom) {
        if (stripos($link, $dom) !== false) {
            //echo 'invalid domain';
            return;
        }
    }

    if (!video_still_exists($link))
        return;


    return true;
}

function decode_characters($info) 
{ 
   // $info = mb_convert_encoding($info, "HTML-ENTITIES", "UTF-8"); 
    $info = preg_replace('~^(&([a-zA-Z0-9]);)~',htmlentities('${1}'),$info); 
    return($info); 
}



function regex_get_all_movs($main='main.html') {
    
       $ctx = stream_context_create(array( 
    'http' => array( 
        'timeout' => 5 
        ) 
    ) 
);

    $st = file_get_contents($link, 0, $ctx);


    $regex = '~<div class="index_item index_item_ie">.*?</div>~s';

    preg_match_all($regex, $st, $matches);

    $title_regex = '~title="(.*?)"~';
    $link_regex = '~href="(.*?)"~';



    $all_movs = array();

    foreach ($matches[0] as $match) {
        preg_match($title_regex, $match, $title);
        //var_dump($title);

        $title = substr(trim(preg_replace('~\(\d+\)~', '', $title[1])), 6);

        preg_match($link_regex, $match, $href);

        $all_movs[$title] = trim($href[1]);
    }

    return $all_movs;
}

function regex_get_all_links($link='movie.html') {
    $ctx = stream_context_create(array( 
    'http' => array( 
        'timeout' => 5 
        ) 
    ) 
);
    $all_links = array();
    $st = file_get_contents($link, 0, $ctx);


    $regex = '~<span class="movie_version_link">.*?</span>~s';

    preg_match_all($regex, $st, $matches);
    foreach ($matches[0] as $match) {

        if (preg_match('/(?<=&url).*?(?=&domain)/', $match, $link)) {
            $link_a = ltrim($link[0], '=');
            $all_links[] = base64_decode($link_a);
        }
    }



    return $all_links;
}

?>
