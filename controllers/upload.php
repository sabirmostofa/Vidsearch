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
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('html');
        $this->load->helper('utils');
        $this->load->model('utils', '', true);
        $file = dirname(dirname(dirname(__FILE__)));
        $file = $file . '/test.csv';


        if (($handle = fopen($file, "r")) !== FALSE) {
            $count = 0;
            while (($data = fgetcsv($handle, 10000, "\t")) !== FALSE) {
                if ($count++ == 0)
                    continue;
                $data_real = $data;

                $movie_name = reform_title($data[1]);
                if (!preg_match('/[a-zA-Z0-9]/', $movie_name))
                    continue;

                $to_insert = array(
                    'movie_name' => $movie_name,
                    'movie_channel_link' => reform_url($data[5]),
                    'movie_release_date' => date('Y-m-d H:i:s', strtotime(reform_title($data[9]))),
                    'movie_release_countries' => reform_title($data[11])
                );

                if (!$this->utils->movie_exists($movie_name))
                    $this->utils->insert_movie($to_insert);
                else
                    $this->utils->update_movie($to_insert);

                $movie_id = $this->utils->get_movie_id($movie_name);

                echo $movie_id, '<br/>';

                // Insert Links
                $all_links = reform_url($data[6]);
                $all_links = explode(';', $all_links);

                if (is_array($all_links))
                    foreach ($all_links as $single) {
                        $single = trim($single);
                        if (strlen($single) > 8) {
                            if (!$this->utils->movie_link_exists($movie_id, $single))
                                $this->utils->insert_link($movie_id,$single);
                        }
                    }

                //Insert or Update genre
                $genre1 = trim(reform_title($data[2]));
                $genre2 = trim(reform_title($data[3]));
                $genre3 = trim(reform_title($data[4]));

                $all_genre = array();
                if (preg_match('/[a-zA-Z]/', $genre1))
                    $all_genre[] = $genre1;
                if (preg_match('/[a-zA-Z]/', $genre2))
                    $all_genre[] = $genre2;
                if (preg_match('/[a-zA-Z]/', $genre3))
                    $all_genre[] = $genre3;

                foreach ($all_genre as $single) {
                    if (!$this->utils->genre_exists($single))
                        $this->utils->insert_genre($single);
                    $genre_id = $this->utils->get_genre_id($single);
                    if (!$this->utils->has_genre_movie($movie_id, $genre_id))
                        $this->utils->insert_rel_genre($movie_id, $genre_id);
                }

                //Insert or Update Actors
                $all_actors = reform_url($data[12]);
                $all_actors = explode(';', $all_actors);
                if (is_array($all_actors))
                    foreach ($all_actors as $single) {
                        $single = trim($single);
                        if (strlen($single) > 2) {
                            if (!$this->utils->actor_exists($single))
                                $this->utils->insert_actor($single);
                            $actor_id = $this->utils->get_actor_id($single);
                            if (!$this->utils->has_actor_movie($movie_id, $genre_id))
                                $this->utils->insert_rel_actor($movie_id, $genre_id);
                        }
                    }



                if ($count > 100)
                    break;
            }
            fclose($handle);
        }
    }

}

?>
