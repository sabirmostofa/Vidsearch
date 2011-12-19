<?php

class Main extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('html');
        $this->load->view('home');
    }

}
?>
