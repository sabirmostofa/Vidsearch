<?php

class Ajax extends CI_Controller {

    public function index() {   
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->library('pagination');    
    }

} 

?>
