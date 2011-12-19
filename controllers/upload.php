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
            $count=0;
            while (($data = fgetcsv($handle, 10000, "\t")) !== FALSE) {
                if($count++ == 0)continue;
                $data_real = $data;
                
                        $to_insert= array(
                            'movie_name' =>  reform_title($data[1]) , 
                            'movie_channel_link' => reform_url($data[5]),
                            'movie_direct_links' => reform_url($data[6]) ,
                            'movie_release_date' =>date('Y-m-d H:i:s', strtotime( reform_title($data[9]))) ,
                            'movie_release_countries' =>  reform_title($data[11])
                            );

                
             $res = $this->utils->insert_movie($to_insert);
          $genre1= trim(reform_title($data[2]));
          $genre2= trim(reform_title($data[3]));
          $genre3= trim(reform_title($data[4]));
          
          $all_genre=array();
          if(preg_match('/[a-zA-Z]/',$genre1))$all_genre[]=$genre1;
          if(preg_match('/[a-zA-Z]/',$genre2))$all_genre[]=$genre2;
          if(preg_match('/[a-zA-Z]/',$genre3))$all_genre[]=$genre3;
          
          foreach($all_genre as $single){
              if(!$this->utils->genre_exists($single))$this->utils->insert_genre($single);
              if(!$this->utils->has_genre_movie($movie_id, $genre_id))$this->utils->insert_rel_genre($movie_id,$genre_id);
          }
            
             
                
              
                if($count == 100)break;
             
            }
            fclose($handle);
        }
    }

}

?>
