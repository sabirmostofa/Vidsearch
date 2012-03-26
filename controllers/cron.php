<?php

class Cron extends CI_Controller {

    public function index() {
        set_time_limit(300);
        $this->load->helper('utils');
        $this->load->model('utils', '', true);
        $base_url = 'http://www.1channel.ch';
        $url = 'main.html';

        $dom = new DOMDocument();
        $content = file_get_contents($url);
        @$dom->loadHTML($content);



        foreach ($dom->getElementsByTagName('div') as $div) {
            if ($div->getAttribute('class') == 'pagination') {
                $pag = array();
                foreach ($div->getElementsByTagName('a') as $a) {
                    preg_match('/\d+/', $a->getAttribute('href'), $val);
                    $pag[] = $val[0];
                }

                echo $max_page = array_pop($pag);
            }
        }// endforeach
        //Getting all pages
        for ($i = 0; $i <= $max_page; $i++):

            if ($i != 0) {
                $base = 'http://www.1channel.ch/index.php?page=';
                $page = $base . $i;
                $content = file_get_contents($page);
                $dom->loadHTML($content);
            }

            foreach ($dom->getElementsByTagName('div') as $div) {

                if ($div->getAttribute('class') == 'index_item index_item_ie') {

                    echo $mov_url = $base_url . $div->getElementsByTagName('a')->item(0)->getAttribute('href');
                    $mov_dom = new DOMDocument();

                    @$mov_dom->loadHTML(file_get_contents($mov_url));

                    //getting title
                    foreach ($mov_dom->getElementsByTagName('meta') as $meta)
                        if ($meta->getAttribute('property') == 'og:title')
                            $m_title = $meta->getAttribute('content');

                    echo $m_title, '<br/>';

                    $m_genres = array();
                    $m_actors = array();
                    $m_links = array();

                    //$info_needed= array('movie_info_genres', 'movie_info_actors', );
                    //getting other info
                    foreach ($mov_dom->getElementsByTagName('span') as $span):

                        switch ($span->getAttribute('class')):
                            case 'movie_info_genres':
                                foreach ($span->getElementsByTagName('a') as $a)
                                    $m_genres[] = $a->textContent;
                                break;

                            case 'movie_info_actors':
                                foreach ($span->getElementsByTagName('a') as $a)
                                    $m_actors[] = $a->textContent;
                                break;

                            case 'movie_version_link':
                                foreach ($span->getElementsByTagName('a') as $a) {
                                    if (preg_match('/(?<=&url).*?(?=&domain)/', $a->getAttribute('href'), $link)) {
                                        $link_a = ltrim($link[0], '=');
                                        $m_links[] = base64_decode($link_a);
                                    } else {

                                        $m_links[] = $a->getAttribute('href');
                                    }
                                }
                                break;

                        endswitch;


                    endforeach;

                    // var_dump($m_genres);
                    // var_dump($m_actors);
                    // var_dump($m_links);
                    //Inserting into the databse tables
                    $to_insert = array(
                        'movie_name' => $m_title,
                        'movie_channel_link' => '',
                        'movie_release_date' => '',
                        'movie_release_countries' => ''
                    );

                    if (!$this->utils->movie_exists($m_title))
                        $this->utils->insert_movie($to_insert);

                    $movie_id = (int) $this->utils->get_movie_id($m_title);

                    if ($movie_id == 0)
                        continue;

                    foreach ($m_links as $single) {
                        $single = trim($single);
                        
                        if($this->utils->in_invalid_links($single))
                            continue;

                        if (!$this->utils->movie_link_exists($movie_id, $single)) {

                            if (!valid_single_link($single))
                                continue;

                            $this->utils->insert_link($movie_id, $single);
                        }
                    }
                }
            }

           // exit;
        endfor;
    }// endof cron parser

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


                //delete if not founded more than consecutive 5 times
                if ($this->utils->get_not_found($link_id) > 10)
                    $this->utils->delete_single_link($link_id);

                // delete if reported more than 5 times
                if ($this->utils->get_report_count($link_id) > 10)
                    $this->utils->delete_single_link($link_id);
            }



            $index = $index + 100;
        }
    }

}
