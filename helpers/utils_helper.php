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
        'all_links'=>reform_url($data[6]),
        'all_actors' => reform_url($data[12]),
        'genre1'=> reform_title($data[2]),
        'genre2'=> reform_title($data[3]),
        'genre3'=> reform_title($data[4])
    );
    
}

?>
