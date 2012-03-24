<?php

class Api extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->model('utils', '', true);
       $limit = $this->input->get('limit');
       $start = $this->input->get('start');
       
       $limit = $limit? $limit:100;
       $start = $start? $start:0;
       
    
       
       
       echo  json_encode( $this->utils->api_get_links($start,$limit));
    }

}

?>
