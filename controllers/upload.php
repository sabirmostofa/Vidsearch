<?php

class Upload extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('html');
        $this->load->view('upload_file_alt');
    }

    public function form() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('html');
        var_dump($this->input->post('csv_file'));
        var_dump($_POST);
        var_dump($_FILES);
    }

    public function form_alt() {
        set_time_limit(3600);
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('html');
        $this->load->helper('utils');
        $this->load->model('utils', '', true);
        $file = dirname(dirname(dirname(__FILE__)));
        $file = $file . '/process.csv';
        $time = time();

        // Determining  the separator
        if (($handle_test = fopen($file, "r")) !== FALSE) {
            $line = fgets($handle_test);
            $pos_comma = stripos($line, ',');
            if ($pos_comma === FALSE)
                $del = "\t";
            $pos_tab = stripos($line, "\t");
            if ($pos_tab === FALSE)
                $del = ',';

            if (is_int($pos_comma) && is_int($pos_tab))
                $del = ($pos_comma < $pos_tab) ? ',' : "\t";

            fclose($handle_test);
        }


        //Starting Main Loop
        if (($handle = fopen($file, "r")) !== FALSE) {
            $count = 0; $link_count=0;
            while (($data = fgetcsv($handle, 10000, $del)) !== FALSE) {
                if ($count++ == 0)
                    continue;

                extract(return_data_reformed($data));


                if (strlen($movie_name) < 2)
                    continue;


                $to_insert = array(
                    'movie_name' => $movie_name,
                    'movie_channel_link' => $movie_channel_link,
                    'movie_release_date' => $movie_release_date,
                    'movie_release_countries' => $movie_release_countries
                );


                if (!$this->utils->movie_exists($movie_name))
                    $this->utils->insert_movie($to_insert);
//                else
//                    $this->utils->update_movie($to_insert);

                $movie_id = (int) $this->utils->get_movie_id($movie_name);



                if ($movie_id == 0)
                    continue;



                // Insert Links            
                $all_links = explode(';', $all_links);
                $all_links[] = $movie_channel_link;

                if (is_array($all_links))
                    foreach ($all_links as $single) {
                        $single = trim($single);
                        if (strlen($single) > 10 && strlen($single) < 200) {
                            if (!$this->utils->movie_link_exists($movie_id, $single)){
                                $this->utils->insert_link($movie_id, $single);
                                $link_count++;
                            }
                        }
                    }

                //Insert or Update genre     

                $all_genre = array();
                if (strlen($genre1) > 2)
                    $all_genre[] = substr($genre1, 0, 40);
                if (strlen($genre2) > 2)
                    $all_genre[] = substr($genre1, 0, 40);
                if (strlen($genre3) > 2)
                    $all_genre[] = substr($genre1, 0, 40);

                foreach ($all_genre as $single) {
                    if (!$this->utils->genre_exists($single))
                        $this->utils->insert_genre($single);
                    $genre_id = $this->utils->get_genre_id($single);
                    if (!$this->utils->has_genre_movie($movie_id, $genre_id))
                        $this->utils->insert_rel_genre($movie_id, $genre_id);
                }

                //Insert or Update Actors

                $all_actors = explode(';', $all_actors);
                if (is_array($all_actors))
                    foreach ($all_actors as $single) {
                        $single = substr($single, 0, 58);
                        if (strlen($single) > 2) {
                            if (!$this->utils->actor_exists($single))
                                $this->utils->insert_actor($single);
                            $actor_id = $this->utils->get_actor_id($single);
                            if (!$this->utils->has_actor_movie($movie_id, $genre_id))
                                $this->utils->insert_rel_actor($movie_id, $genre_id);
                        }
                    }



//               if ($count > 5)
//                   break;
            }
            fclose($handle);
            $time_last = time();
            $t_time=($time_last-$time)/60;
            $this->load->view('parse_result',array('t_time'=>$t_time, 'num_links' => $link_count));
        }
    }

}

?>
