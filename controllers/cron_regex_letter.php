<?php

class Cron_Regex_Letter extends CI_Controller {

    public function index() {

        error_reporting(E_ALL);
        ignore_user_abort(true);
        //set_time_limit(4*3600);
        set_time_limit(0);
        $link_inserted = 0;
        $this->load->helper('utils');
        $this->load->model('utils', '', true);
        include(APPPATH.'libraries/simple_html_dom.php');
         global $proxy_array;
        $proxy_array= get_proxy_list();


        $base_site = 'http://www.1channel.ch';
        $base_url = 'http://www.1channel.ch/?letter=';
        //$url = 'main.html';
//
        $dom = new DOMDocument();

        $letter_array = range('a', 'z');
        array_push($letter_array, '123');

        shuffle($letter_array);
        
        
        //testing
        
        //$letter_array = array('b');


        foreach ($letter_array as $let):

            $base_page = $base_url . $let;

//$base_page = 'http://www.1channel.ch/?letter=u';
            $max_page = get_max_page_regex($base_page);


            //Getting all pages
            
            //$max_page = $max_page-28;
            $f_checked=0;
            for ($i = 0; $i <= $max_page; $i++):


                if ($i != 0)
                    $page = $base_page . "&page=$i";
                else
                    $page = $base_page;
                    
                    
                    
                    if($i==$max_page){
                        if($f_checked++ < 1){
                        $page='http://www.1channel.ch/index.php?sort=featured';
                        $i--;
                        
                        }
                    }
                    echo $page , '<br/>';

                $all_movs = regex_get_all_movs($page);

                if (empty($all_movs))
                    continue;


                // var_dump($all_movs);



                foreach ($all_movs as $m_title => $m_link) {
                    echo $m_title ,'<br/>';

                    if (strlen($m_title) == 0)
                        continue;

                    $m_genres = array();
                    $m_actors = array();
                    $m_links = array();
                    $m_info = regex_get_all_info($base_site . $m_link);
                    //var_dump($m_info);
                    $m_links = $m_info['links'];
                    $m_release= date('Y-m-d H:i:s', $m_info['release_date']);

                    if (empty($m_links))
                        continue;

                    //var_dump($m_links);
                    //$info_needed= array('movie_info_genres', 'movie_info_actors', );
                    //getting other info
                    // var_dump($m_genres);
                    // var_dump($m_actors);
                    // var_dump($m_links);
                    //Inserting into the databse tables
                    $to_insert = array(
                        'movie_name' => $m_title,
                        'movie_channel_link' => $m_info['imdb_link'],
                        'movie_release_date' => $m_release,
                        'movie_release_countries' => ''
                    );

                    if (!$this->utils->movie_exists($m_title))
                        $this->utils->insert_movie($to_insert);

                    $movie_id = (int) $this->utils->get_movie_id($m_title);
                    var_dump($movie_id);
                    $release_date = $this->utils-> get_movie_release_date($movie_id);
                    
                    // Inserting release date and imdb_Link if not exists
                    if( !strtotime($release_date))
                        $this->utils->insert_release_imdb_info($movie_id , $m_info['imdb_link'],$m_release);
                    

                    if ($movie_id == 0)
                        continue;

                    
                    foreach ($m_links as $single) {
                        $single = trim($single);
                        

                        if ($this->utils->in_invalid_links($single)) {
                            //echo $single;
                            //exit('invalid');
                            continue;
                        }

                        if (!$this->utils->movie_link_exists($movie_id, $single)) {

                            if (!valid_single_link($single)) {
                                //echo $single;
                                //exit('link not valid');
                                continue;
                            }

                            $this->utils->insert_link($movie_id, $single);
                            $link_inserted++;
                        }
                    }
                }

            //exit;
            endfor;
            exit;
        endforeach;


        echo "New Links: $link_inserted";
    }

// endof cron parser function
    // function to remove the  obsolete urls as cron 
    public function cleanup() {
        //set_time_limit(300);
        ignore_user_abort(true);       
        set_time_limit(0);
        $this->load->helper('utils');
        $this->load->model('utils', '', true);

        $tot = mysql_fetch_array($this->utils->get_total_links());
        $total = $tot[0];




        $index = 0;
        while ($index <= $total) {
            $res = $this->utils->get_links_partial($index, 100);
            while ($res_ar = mysql_fetch_assoc($res)) {

                echo '<br/>', $link = $res_ar['link_url'];
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
