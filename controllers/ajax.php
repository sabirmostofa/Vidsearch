<?php

class Ajax extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->model('utils', '', true);
        $q = $this->input->get('query');
        $suggest = '[';
        foreach ($this->utils->get_search_terms($q)->result() as $single) {
            $suggest .= '\'' . mysql_real_escape_string($single->movie_name) . '\',';
        }

        $suggest = trim($suggest, ',') . ']';
        echo "{
        query:'{$q}',
        suggestions:{$suggest},
        data:'',
        }";
        exit;
    }
    
    
    public function report_link(){
         $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        
        $this->load->model('utils', '', true);
        //var_dump($_GET['link_id']);
        $link_id = $this->input->get('link_id');
        $this->utils->add_report($link_id);
        exit;
        
    }

}

?>
