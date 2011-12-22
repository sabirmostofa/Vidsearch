<?php

class Utils extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function movie_exists($movie_name) {
        $movie_name = mysql_real_escape_string($movie_name);
        return mysql_num_rows($this->db->simple_query("select movie_id from vs_movies where movie_name='$movie_name' "));
    }

    function get_movie_id($movie_name) {
        $movie_name = mysql_real_escape_string($movie_name);
        $res = $this->db->simple_query("select movie_id from vs_movies where movie_name='$movie_name' ");
        if (mysql_num_rows($res) == 0)
            return false;
        return mysql_result($res, 0);
    }

    function insert_movie($data) {
        $this->db->save_queries = false;
        extract($data);
        $names = array('movie_name', 'movie_channel_link', 'movie_release_date', 'movie_release_countries');
        foreach ($names as $val) {
            $this->$val = $$val;
        }
        return $this->db->insert('vs_movies', $this);
    }

    function update_movie($data) {
        $m_name = array_shift($data);
        $this->db->update('vs_movies', $data, array('movie_name' => $m_name));
    }

    //Links function

    function movie_link_exists($movie_id, $link) {
        $link = mysql_real_escape_string($link);
        return mysql_num_rows($this->db->simple_query("select movie_id from vs_links where link_url='$link' and movie_id=$movie_id "));
    }

    function insert_link($movie_id, $link) {
        $this->db->save_queries = false;
        $this->db->insert('vs_links', array('movie_id' => $movie_id, 'link_url' => $link));
    }

    //Genre Functions
    function genre_exists($genre) {
        $genre = mysql_real_escape_string($genre);
        return mysql_num_rows($this->db->simple_query("select id from vs_genre where genre='$genre' "));
    }

    function insert_genre($genre) {
        $this->db->save_queries = false;
        $this->db->insert('vs_genre', array('genre' => $genre));
    }

    function get_genre_id($genre) {
        $genre = mysql_real_escape_string($genre);
        $res = $this->db->simple_query("select id from vs_genre where genre='$genre' ");
        if (mysql_num_rows($res) == 0)
            return false;
        return mysql_result($res, 0);
    }

    function has_genre_movie($movie_id, $genre_id) {
        return mysql_num_rows($this->db->simple_query("select movie_id from vs_movies_genre where movie_id=$movie_id and genre_id= $genre_id"));
    }

    function insert_rel_genre($movie_id, $genre_id) {
        $this->db->save_queries = false;
        $this->db->insert('vs_movies_genre', array('movie_id' => $movie_id, 'genre_id' => $genre_id));
    }

    //Actor Functions

    function actor_exists($actor) {
        $actor = mysql_real_escape_string($actor);
        return mysql_num_rows($this->db->simple_query("select id from vs_actors where actor_name='$actor' "));
    }

    function insert_actor($actor) {
        $this->db->save_queries = false;
        $this->db->insert('vs_actors', array('actor_name' => $actor));
    }

    function get_actor_id($actor) {
        $actor = mysql_real_escape_string($actor);
        $res = $this->db->simple_query("select id from vs_actors where actor_name='$actor' ");
        if (mysql_num_rows($res) == 0)
            return false;
        return mysql_result($res, 0);
    }

    function has_actor_movie($movie_id, $actor_id) {
        return mysql_num_rows($this->db->simple_query("select movie_id from vs_movies_actors where movie_id=$movie_id and actor_id= $actor_id"));
    }

    function insert_rel_actor($movie_id, $actor_id) {
        $this->db->save_queries = false;
        $this->db->insert('vs_movies_actors', array('movie_id' => $movie_id, 'actor_id' => $actor_id));
    }

    //Ajax Functions

    function get_search_terms($q) {
        $q = mysql_real_escape_string($q);
        return $this->db->simple_query("select movie_name from vs_movies where movie_name like '$q%' limit 50");
    }

    //front functions

    function get_links($s, $start) {
        $s = mysql_real_escape_string($s);
        $low = $start;
        $amount = 10;
        return $this->db->simple_query("select vs_movies.movie_name, vs_links.link_url 
                from vs_movies inner join vs_links on vs_movies.movie_id = vs_links.movie_id 
                where vs_movies.movie_name='$s' limit $low, $amount");
    }

    function get_total_num($s) {
        $s = mysql_real_escape_string($s);
        return $this->db->simple_query("select count(*) as total
                from vs_movies inner join vs_links on vs_movies.movie_id = vs_links.movie_id 
                where vs_movies.movie_name='$s'");
    }

}

?>
