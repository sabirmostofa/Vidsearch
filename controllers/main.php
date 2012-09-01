<?php

class Main extends CI_Controller {

    public function index() {

        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('html');
        $this->load->model('utils', '', true);
        $this->load->library('pagination');

        $res = array('data' => array());
        $s = $this->input->get("search_term");
        $type = $this->input->get('data_type');

        if ($type == 'series' && $s && !isset($_GET['season'])):
            $seas_eps = $this->utils->get_season_episodes($s)->result();
            $res['data'] = $seas_eps;
            $this->load->view('home_series', $res);
            
            
        elseif (isset($_GET['season'])):
            $season = $_GET['season'];
            $eps = $_GET['episode'];
            $series_id = $_GET['series_id'];
            

            if ($s) {
                if (isset($_GET['per_page']) && is_numeric($_GET['per_page']))
                    $per_page = $_GET['per_page'];
                else
                    $per_page = 0;
                $all_links = $this->utils->get_series_episode_links($s, $series_id,$season, $eps ,$per_page )->result();
                $res['data'] = $all_links;
                $res['total_num'] = $this->utils->get_total_episode_num($series_id,$season, $eps)->row()->total;
                $config['base_url'] = base_url() . sprintf( '?search_term=%s&data_type=series&season=%s&episode=%s&series_id=%s', $s,$season,$eps, $series_id);
                $config['total_rows'] = $res['total_num'];
                $config['per_page'] = 10;
                $config['num_links'] = 5;
                $config['anchor_class'] = 'class="pag_link" ';
                $config['cur_tag_open'] = '<div class="pag_nums" id="pag_cur">';
                $config['cur_tag_close'] = '</div>';
                $config['num_tag_open'] = '<div class="pag_nums">';
                $config['num_tag_close'] = '</div>';
                $config['prev_link'] = '<div id="pag_prev" ><img  src="' . base_url() . 'application/views/images/prev.png' . '"/></div>';
                $config['next_link'] = '<div id="pag_next"><img  src="' . base_url() . 'application/views/images/next.png' . '"/></div>';
                $config['enable_query_strings'] = TRUE;
                $this->pagination->initialize($config);
                //echo $this->pagination->create_links();
            }

            $this->load->view('home', $res);

        else:

            //$this->pagination->initialize($config);


            if ($s) {
                if (isset($_GET['per_page']) && is_numeric($_GET['per_page']))
                    $per_page = $_GET['per_page'];
                else
                    $per_page = 0;
                $all_links = $this->utils->get_links($s, $per_page)->result();
                $res['data'] = $all_links;
                $res['total_num'] = $this->utils->get_total_num($s)->row()->total;
                $config['base_url'] = base_url() . '?search_term=' . $s;
                $config['total_rows'] = $res['total_num'];
                $config['per_page'] = 10;
                $config['num_links'] = 5;
                $config['anchor_class'] = 'class="pag_link" ';
                $config['cur_tag_open'] = '<div class="pag_nums" id="pag_cur">';
                $config['cur_tag_close'] = '</div>';
                $config['num_tag_open'] = '<div class="pag_nums">';
                $config['num_tag_close'] = '</div>';
                $config['prev_link'] = '<div id="pag_prev" ><img  src="' . base_url() . 'application/views/images/prev.png' . '"/></div>';
                $config['next_link'] = '<div id="pag_next"><img  src="' . base_url() . 'application/views/images/next.png' . '"/></div>';
                $config['enable_query_strings'] = TRUE;
                $this->pagination->initialize($config);
                //echo $this->pagination->create_links();
            }

            $this->load->view('home', $res);
        endif;
    }

}

?>
