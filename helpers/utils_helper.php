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
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);

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

    foreach ($messages as $single)
        if (stripos($response, $single) !== false)
            return;

    return true;
}

function valid_single_link($link) {
    if (stripos($link, 'http://') === false) {
        //echo 'http not foound';
        if (stripos($link, 'https://') === false)
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

function decode_characters($info) {
    // $info = mb_convert_encoding($info, "HTML-ENTITIES", "UTF-8"); 
    $info = preg_replace('~^(&([a-zA-Z0-9]);)~', htmlentities('${1}'), $info);
    return($info);
}

function regex_get_all_movs($main='main.html') {
    $st = url_get_contents($main);

    $regex = '~<div class="index_item index_item_ie">.*?</div>~s';

    preg_match_all($regex, $st, $matches);

    $title_regex = '~title="(.*?)"~';
    $link_regex = '~href="(.*?)"~';



    $all_movs = array();

    foreach ($matches[0] as $match) {
        preg_match($title_regex, $match, $title);
        //var_dump($title);

        $title = reform_title(substr(trim(preg_replace('~\(\d+\)~', '', $title[1])), 6));

        preg_match($link_regex, $match, $href);

        $all_movs[$title] = trim($href[1]);
    }

    return $all_movs;
}

function regex_get_all_links($link='movie.html') {
    $all_links = array();
    $st = url_get_contents($link);


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

function get_max_page($base_url) {

    $dom = new DOMDocument();

    @$dom->loadHTML(get_tidy_html($base_url));



    foreach ($dom->getElementsByTagName('div') as $div) {
        if ($div->getAttribute('class') == 'pagination') {
            $pag = array();
            foreach ($div->getElementsByTagName('a') as $a) {
                preg_match('/\d+/', $a->getAttribute('href'), $val);
                $pag[] = $val[0];
            }

            $max_page = array_pop($pag);
        }
    }// endforeach

    if (!isset($max_page))
        $max_page = 150;
    return $max_page;
}

function get_max_page_regex($url) {

    $content = url_get_contents($url);

    if (preg_match('~pagination".*?</div>~s', $content, $matches))
        preg_match_all('~page=(\d+)~s', $content, $matches);



    /*
      if( !is_array($matches1[1]) || empty($matches1[1]) ){
      var_dump($content);
      mail('sabirmostofa@gmail.com', 'Failing returns', $content);
      $fp = fopen('./bug_check_406.txt','w');
      fwrite($fp,$content);
      fclose($fp);
      exit;
      }
     * */

// alternative approach to get the max pages

    if (!is_array($matches)) {
        echo '<br/>**********', 'No max page found', '*********<br/>';
        if (preg_match('~(\d+) items found~s', $content, $matches))
            return ceil($matches[1] / 24);
    }
    echo '<br/>**********', 'Max page= ', max($matches[1]), '*********<br/>';

    return max($matches[1]);
}

// Sleep while checking robot 

function is_robot_check($content) {
    if (stripos($content, 'robot_check_container') !== false)
        return true;
}

//request 3 times max if the web request fails

$counter_proxy = 0;

function url_get_contents($url) {
    global $counter_proxy;

    /*
      if(++$counter_proxy%5000 < 200){
      $content = get_content_through_proxy($url);
      if($content === false)
      $content = get_content_through_proxy($url);
      return $content;
      }
     * */

    $content = file_get_contents($url);
    if (is_robot_check($content)) {

        echo '<br/>**********', 'switching to robot check', '*********<br/>';
        $content = get_content_through_proxy($url);

        if ($content === false || is_robot_check($content))
            $content = get_content_through_proxy($url);

        return $content;
    }



    if ($content === false)
        $content = file_get_contents($url);
    if ($content === false) {

        $content = get_content_through_proxy($url);
        if ($content === false)
            $content = get_content_through_proxy($url);
        if ($content === false)
            $content = get_content_through_proxy($url);
        if ($content === false) {
            $message = <<<EOT
   ******************************************
    <br/>
    ********************************************
       
<h1>Proxy Failed</h1>
********************************************
       

            
EOT;
            echo $message;

            $content = file_get_contents($url);
        }
    }

    return $content;
}

function get_tidy_html($page) {
    $content = tidy_parse_file($page, array('anchor-as-name' => 0));
    if (!$content) {
        $content = tidy_parse_file($page, array('anchor-as-name' => 0));
    }
    if (!$content) {
        $content = tidy_parse_file($page, array('anchor-as-name' => 0));
    }
    if (!$content) {
        $content = tidy_parse_file($page, array('anchor-as-name' => 0));
    }

    return tidy_get_output($content);
}

//Getting Proxy List

function getIP($obj, $html) {


    $text = str_replace("div", "span", $obj->xmltext);
    $text = explode("span", $text);

    $ip = array();

    foreach ($text as $value) {
        $value = trim($value);
        $value = trim($value, "<");
        $value = trim($value, ">");
        $value = trim($value, ".");

        if (empty($value))
            continue;

        if (strpos($value, "display:none")) {
            continue;
        }

        if (strpos($value, ">")) {
            $value = "<" . $value . ">";
        }

        $value = strip_tags($value);

        $value = trim($value, ".");

        if (empty($value))
            continue;

        $ip [] = $value;
    }

    if (is_array($ip)) {
        return implode(".", $ip);
    }
}

function get_proxy_list() {


    $html = file_get_html('http://www.hidemyass.com/proxy-list/');

    $proxy_array = array();
    $counter = 0;
    foreach ($html->find('tr') as $element) {
        if (++$counter == 1)
            continue;
        $ip = $element->find('td', 1);
        $port = trim($element->find('td', 2)->xmltext);
        $ip = getIP($ip, $html);
        // var_dump($element->xmltext);
        if (preg_match('~\d~', $ip) && preg_match('~\d~', $port))
            $proxy_array[$ip] = $port;
    }

    return $proxy_array;
}

// get content through proxy


function get_content_through_proxy($url) {

    /*
      $pr = get_proxy_list();


      $keys = array_keys($pr);
      shuffle($keys);
      $pr = array_merge(array_flip($keys), $pr);



      foreach ($pr as $key => $value):
      $ip = $key;
      $port = $value;
      break;
      endforeach;
     */

    global $proxy_array;

    $ip = array_rand($proxy_array);


    $port = $proxy_array[$ip];




    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/12.0');
    curl_setopt($ch, CURLOPT_PROXY, $ip);
    curl_setopt($ch, CURLOPT_PROXYPORT, $port);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
//curl_setopt($ch, CURLOPT_HEADER, 1);

    return curl_exec($ch);
}

?>
