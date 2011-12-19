<?php

class Utils extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function movie_exists($movie_name) {
        $movie_name = mysql_real_escape_string($movie_name);
        $movie = $this->db->query("select movie_id from vs_movies where movie_name='$movie_name' ")->row();
        if (is_array($movie) && count($movie) == 0)
            return false;
        return true;
    }

    function get_movie_id($movie_name) {
        $movie_name = mysql_real_escape_string($movie_name);
        $movie = $this->db->query("select movie_id from vs_movies where movie_name='$movie_name' ")->row();
        return $movie->movie_id;
    }

    function insert_movie($data) {
        extract($data);
        $names = array('movie_name', 'movie_channel_link', 'movie_direct_links', 'movie_release_date', 'movie_release_countries');
        foreach ($names as $val) {
            $this->$val = $$val;
        }
        return $this->db->insert('vs_movies', $this);
    }

    function update_movie($data) {
        $m_name = array_shift($data);
        $this->db->update('vs_movies', $data, array('movie_name' => $m_name));
    }

    //Genre Functions
    function genre_exists($genre) {
        $genre = mysql_real_escape_string($genre);
        $gen = $this->db->query("select id from vs_genre where genre='$genre' ")->row();
        if (is_array($gen) && count($gen) == 0)
            return false;
        return true;
    }

    function insert_genre($genre) {
        $this->db->insert('vs_genre', array('genre' => $genre));
    }

    function get_genre_id($genre) {
        $genre = mysql_real_escape_string($genre);
        $gen = $this->db->query("select id from vs_genre where genre='$genre' ")->row();
        return $gen->id;        
    }

    function has_genre_movie($movie_id, $genre_id) {
        $gen = $this->db->query("select movie_id from vs_movies_genre where movie_id=$movie_id and genre_id= $genre_id")->row();
        if (is_array($gen) && count($gen) == 0)
            return false;
        return true;
        
    }

    function insert_rel_genre($movie_id, $genre_id) {
         $this->db->insert('vs_movies_genre', array('movie_id' => $movie_id , 'genre_id' =>$genre_id));        
    }

}

?>
