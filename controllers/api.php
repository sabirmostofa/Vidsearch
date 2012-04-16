<?php

class Api extends CI_Controller {

    public function index() {
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->model('utils', '', true);
        $limit = $this->input->get('limit');
        $start = $this->input->get('start');

        $limit = $limit ? $limit : 100;
        $start = $start ? $start : 0;




        echo json_encode($this->utils->api_get_links($start, $limit));
    }

    // return the list of movies
    public function movies() {
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->model('utils', '', true);
        $limit = $this->input->get('limit');
        $start = $this->input->get('start');

        $limit = $limit ? $limit : 100;
        $start = $start ? $start : 0;




        echo json_encode($this->utils->api_get_movies($start, $limit));
    }

// return single movie links
    public function single() {
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->model('utils', '', true);
        $movie_id = $this->input->get('mov_id');

        echo json_encode($this->utils->api_single_movie_links($movie_id));
    }

    // return the most liked links


    public function liked() {
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->model('utils', '', true);
        $limit = $this->input->get('limit');
        $start = $this->input->get('start');

        $limit = $limit ? $limit : 100;
        $start = $start ? $start : 0;

        echo json_encode($this->utils->api_most_liked($start, $limit));
    }

    public function info() {
        $this->load->model('utils', '', true);
        $tot = mysql_fetch_array($this->utils->get_total_links());

        $tot_mov = $this->utils->get_total_movies();
        $tot_links = $tot[0];

        echo json_encode(array('total_movies' => $tot_mov, 'total_links' => $tot_links));
    }

    public function search() {
        $this->load->model('utils', '', true);
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->helper('form');
        
        $term = $this->input->get('term');
        
        $movs = $this->utils->api_get_search($term)->result();
        echo json_encode($movs);
    }

}

// end of class
?>
