<?php

class Main extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('html');
        $this->load->model('utils', '', true);
        $res = array('data'=> array());
        
        if( $s = $this->input->get("search_term") ){
            $page = isset($page)? $page:1;
            $all_links= $this->utils->get_links($s, $page)->result();
            $res['data']= $all_links;
            
        }
       
        
        $this->load->view('home',$res);
    }

}
?>
