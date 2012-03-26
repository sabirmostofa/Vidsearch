<?php

/*
 * Use simple_query to avoid the memory consumption of the the codeigniter. If using simple query only the resource will be
 * returned. In case you are using query method it will return the CI query object
 * 
 * 
 */

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

    function v_movie($data) {
        $this->db->save_queries = false;
        $m_name = array_shift($data);
        $this->db->update('vs_movies', $data, array('movie_name' => $m_name));
    }

    //Links function

    function movie_link_exists($movie_id, $link) {
        $link = mysql_real_escape_string($link);
        return mysql_num_rows($this->db->simple_query("select movie_id from vs_links where link_url='$link' and movie_id=$movie_id "));
    }

    function insert_link($movie_id, $link) {
        $this->db->reconnect();
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
        return $this->db->query("select movie_name from vs_movies where movie_name like '$q%' limit 50");
    }

    //front functions

    function get_links($s, $start) {
        $s = mysql_real_escape_string($s);
        $low = $start;
        $amount = 10;
        return $this->db->query("select vs_movies.movie_name, vs_links.link_url,vs_links.link_id, vs_links.like_count,
                vs_links.report_count from vs_movies inner join vs_links on vs_movies.movie_id = vs_links.movie_id 
                where vs_movies.movie_name='$s' order by like_count desc limit $low, $amount ");
    }

    function get_total_num($s) {
        $s = mysql_real_escape_string($s);
        return $this->db->query("select count(*) as total
                from vs_movies inner join vs_links on vs_movies.movie_id = vs_links.movie_id 
                where vs_movies.movie_name='$s'");
    }
    
    //get total links for cron cleanup
    
    function get_total_links(){
        return $this->db->simple_query("select count(*) 
                from  vs_links");
        
    }
    
    //return  100 links for db clean
    
    function get_links_partial($start, $limit){
        return $this->db->simple_query(" select * from vs_links limit $start , $limit ");
    }
    
    //
    function delete_single_link($link_id){ 
        $data = mysql_fetch_assoc( $this->db->simple_query("select link_url from vs_links where link_id = $link_id "));
        $link_url = $data['link_url'];
        $this->insert_invalid_link($link_url);
         $this->db->simple_query("delete from vs_links where link_id= $link_id ");
    }
    
    
    //insert into invalid link
    function insert_invalid_link($link_url){
        $this->db->save_queries = false;
        $link_url = mysql_escape_string($link_url);
         if(!$this->in_invalid_links($link_url))
         $this->db->insert('vs_invalid_links', array( 'link_url' => $link_url));
        
    }
    
    //check if the link is invalid
    function in_invalid_links($link_url){
        $this->db->reconnect();
        $link_url = mysql_escape_string($link_url);
       if(mysql_num_rows ($this->db->simple_query("select * from vs_invalid_links where link_url= '$link_url'")) !=0 )
               return 1;
       
              
    }
    
    //function update a report count
    function add_report($link_id){        
         return $this->db->simple_query("update vs_links set report_count=report_count+1 where link_id = $link_id ");
    }
    
     function add_up($link_id){        
         return $this->db->simple_query("update vs_links set like_count=like_count+1 where link_id = $link_id ");
    }
    
        //function get report count
    function get_report_count($link_id){        
       $data = mysql_fetch_assoc( $this->db->simple_query("select report_count from vs_links where link_id = $link_id "));
         return $data['report_count'];
    }
    
    //function for adding to not_found column
    function add_to_not_found($link_id){   
        $this->db->reconnect();
         return $this->db->simple_query("update vs_links set not_found=not_found+1 where link_id = $link_id ");
    }
    //function for clearing the not found column
    function clear_not_found($link_id){        
         return $this->db->simple_query("update vs_links set not_found=0 where link_id = $link_id ");
    }
    //function for getting the not_found column
    function get_not_found($link_id){        
       $data = mysql_fetch_assoc( $this->db->simple_query("select not_found from vs_links where link_id = $link_id "));
         return $data['not_found'];
    }
    
    // API functions
    
    function api_get_links($start, $limit){
                $res =  $this->db->simple_query("select vs_movies.movie_name, vs_links.link_url 
                        from vs_movies inner join vs_links on vs_movies.movie_id = vs_links.movie_id 
                 limit $start, $limit");
                
                while($d=mysql_fetch_assoc($res)){
                    $data[]=$d;
                }
        return $data;
    }
    
    function api_get_movies($start, $limit){
                $res =  $this->db->simple_query("select vs_movies.movie_id, vs_movies.movie_name  from vs_movies 
                 limit $start, $limit");
                
                while($d=mysql_fetch_assoc($res)){
                    $data[]=$d;
                }
        return $data;
    }
    
    function api_single_movie_links($movie_id){
               $res =  $this->db->simple_query("select vs_links.link_url from vs_links where movie_id=$movie_id");
                
                while($d=mysql_fetch_assoc($res)){
                    $data[]=$d;
                }
        return $data;
        
        
    }
    
    function api_most_liked($start, $limit){
                $res =  $this->db->simple_query("select vs_movies.movie_name, vs_links.link_url 
                        from vs_movies inner join vs_links on vs_movies.movie_id = vs_links.movie_id order by like_count desc
                 limit $start, $limit");
                
                while($d=mysql_fetch_assoc($res)){
                    $data[]=$d;
                }
        return $data;
        
    }



}

?>
