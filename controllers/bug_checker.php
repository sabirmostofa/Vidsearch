<?php

class Bug_Checker extends CI_Controller {

    public function index() {

        error_reporting(E_ALL);
        ignore_user_abort(true);
        //set_time_limit(4*3600);
        //set_time_limit(20);
        $this->load->helper('utils');
        $this->load->model('utils', '', true);
        $base_url = 'http://www.1channel.ch';
        //$url = 'main.html';

        $dom = new DOMDocument();
        $content = decode_characters(file_get_contents($base_url));
        @$dom->loadHTML($content);



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
        //Getting all pages
        $fp = fopen('bug_check.txt', 'w');
        for ($i = 0; $i <= $max_page; $i++):

            $base = 'http://www.1channel.ch/index.php?page=';
            //$base = 'http://www.1channel.ch/index.php?sort=featured&page=';
            if ($i != 0)
                $page = $base . $i;
            else
                $page = $base;
            $all_movs = regex_get_all_movs($page);


            foreach ($all_movs as $m_title => $m_link) {

                if(stripos($m_title, 'casino') !== false){
                    fwrite($fp, $page."\n");
                }



//                $m_genres = array();
//                $m_actors = array();
//                $m_links = array();
//                $m_links = regex_get_all_links($base_url . $m_link);
//
//                //$info_needed= array('movie_info_genres', 'movie_info_actors', );
//                //getting other info
//                // var_dump($m_genres);
//                // var_dump($m_actors);
//                // var_dump($m_links);
//                //Inserting into the databse tables
//                $to_insert = array(
//                    'movie_name' => $m_title,
//                    'movie_channel_link' => '',
//                    'movie_release_date' => '',
//                    'movie_release_countries' => ''
//                );
//
//                if (!$this->utils->movie_exists($m_title))
//                    $this->utils->insert_movie($to_insert);
//
//                $movie_id = (int) $this->utils->get_movie_id($m_title);
//
//                if ($movie_id == 0)
//                    continue;
//
//                $link_inserted = 0;
//                foreach ($m_links as $single) {
//                    echo $single = trim($single);
//                    echo '<br/>';
//
//                    if ($this->utils->in_invalid_links($single)) {
//                        //echo $single;
//                        //exit('invalid');
//                        continue;
//                    }
//
//                    if (!$this->utils->movie_link_exists($movie_id, $single)) {
//
//                        if (!valid_single_link($single)) {
//                            //echo $single;
//                            //exit('link not valid');
//                            continue;
//                        }
//
//                        $this->utils->insert_link($movie_id, $single);
//                        $link_inserted++;
//                    }
//                }
            }

        //exit;
        endfor;
        
        fclose($fp);

       // echo "New Links: $link_inserted";
    }

// endof cron parser
    // function to remove the  obsolete urls as cron 
    public function cleanup() {
        set_time_limit(300);
        $this->load->helper('utils');
        $this->load->model('utils', '', true);

        $tot = mysql_fetch_array($this->utils->get_total_links());
        $total = $tot[0];




        $index = 0;
        while ($index <= $total) {
            $res = $this->utils->get_links_partial($index, 100);
            while ($res_ar = mysql_fetch_assoc($res)) {

                $link = $res_ar['link_url'];
                $link_id = $res_ar['link_id'];


                if (!video_still_exists($link))
                    $this->utils->add_to_not_found($link_id);
                else
                    $this->utils->clear_not_found($link_id);


                //delete if not founded more than consecutive 10 times
                if ($this->utils->get_not_found($link_id) > 10)
                    $this->utils->delete_single_link($link_id);

                // delete if reported more than 10 times
                if ($this->utils->get_report_count($link_id) > 10)
                    $this->utils->delete_single_link($link_id);
            }



            $index = $index + 100;
        }
    }

}
