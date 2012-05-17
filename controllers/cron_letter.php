<?php

class Cron_Letter extends CI_Controller {

    public function index() {
		error_reporting(E_ALL);
		ignore_user_abort(true);
        //set_time_limit(4*3600);
	set_time_limit(0);
        $this->load->helper('utils');
        $this->load->model('utils', '', true);
        
        $base_site = 'http://www.1channel.ch';
        $base_url = 'http://www.1channel.ch/?letter=';
        //$url = 'main.html';
//
      $dom = new DOMDocument();

        $letter_array =  range('a', 'z');
       array_push($letter_array , '123' );
        
        shuffle($letter_array);
        
        //$letter_array = array('c');
        
        foreach( $letter_array as $let):
        
         $base_page = $base_url . $let;
        
            
       $max_page = get_max_page($base_page);
       
        for ($i = 0; $i <= $max_page; $i++):

            if ($i != 0) 
                $page= $base_page . "&page=$i";
             else 
               $page = $base_page;
             
         
                
                @$dom->loadHTML(get_tidy_html($page));
                
           
            

            foreach ($dom->getElementsByTagName('div') as $div) {
               

                if ($div->getAttribute('class') == 'index_item index_item_ie') {

                    $mov_url = $base_site . $div->getElementsByTagName('a')->item(0)->getAttribute('href');
                    $mov_dom = new DOMDocument();

                    @$mov_dom->loadHTML(get_tidy_html($mov_url));

                    //getting title
                    foreach ($mov_dom->getElementsByTagName('meta') as $meta)
                        if ($meta->getAttribute('property') == 'og:title')
                            $m_title = $meta->getAttribute('content');
                        
                        if(strlen($m_title) < 2 )
                            continue;

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

                    $link_inserted=0;
                    foreach ($m_links as $single) {
                      $single = trim($single);
                       //echo '<br/>';
                        
                        if($this->utils->in_invalid_links($single)){
							//echo $single;
							//exit('invalid');
                            continue;
						}

                        if (!$this->utils->movie_link_exists($movie_id, $single)) {

                            if (!valid_single_link($single)){
								//echo $single;
							//exit('link not valid');
                                continue;
							}

                            $this->utils->insert_link($movie_id, $single);
                            $link_inserted++;
                        }
                    }
                }
            }

            //exit;
        endfor;
        
        endforeach;
        
        echo "New Links: $link_inserted";
        
    }// endof cron parser index function

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
