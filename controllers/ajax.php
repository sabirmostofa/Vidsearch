<?php

class Ajax extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->model('utils', '', true);
        $q = $this->input->get('query');
      
        $type=$this->input->get('data_type');
        
    
        
        $suggest = '[';
        foreach ($this->utils->get_search_terms($q,$type)->result() as $single) {
            $suggest .= '\'' . mysql_real_escape_string($single->name) . '\',';
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
        $todo = $this->input->get('todo');
        $type = $this->input->get('data_type');
        
       
        if($todo == 'down')
         $this->utils->add_report($link_id, $type);
        else
            $this->utils->add_up($link_id, $type);
        exit;
        
    }

}

?>
