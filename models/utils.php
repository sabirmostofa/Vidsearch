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

    function get_movie_release_date($id) {
        $res = $this->db->simple_query("select movie_release_date from vs_movies where movie_id=$id");
        return mysql_result($res, 0);
    }

    function insert_release_imdb_info($id, $imdb, $release_date) {
        $this->db->save_queries = false;
        $this->movie_channel_link=$imdb;
        $this->movie_release_date=$release_date;
        return $this->db->update('vs_movies', $this ,array('movie_id' => $id));
        
    }

    function get_movie_id($movie_name) {
        $movie_name = mysql_real_escape_string($movie_name);
        $res = $this->db->simple_query("select movie_id from vs_movies where movie_name='$movie_name' ");
        if (mysql_num_rows($res) == 0)
            return false;
        return mysql_result($res, 0);
    }
    function get_series_id($series_name) {
        $series_name = mysql_real_escape_string($series_name);
        $res = $this->db->simple_query("select series_id from vs_series where series_name='$series_name' ");
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
    
    
    //series_functions
    function get_season_episodes($s){
         $s = mysql_real_escape_string($s);
         return $this->db->query("
           select           
           vs_series_links.series_id,
           vs_series.series_name,
           vs_series_links.season,
           vs_series_links.episode
           from vs_series           
           inner join 
           vs_series_links
           on 
           vs_series.series_id = vs_series_links.series_id
           where 
           vs_series.series_name='$s'
           group by
           vs_series_links.series_id, 
           vs_series_links.season, 
           vs_series_links.episode
        ");
         
        
    }
    

    //get total links for cron cleanup

    function get_total_links() {
        return $this->db->simple_query("select count(*) 
                from  vs_links");
    }

    //get total movies for api

    function get_total_movies() {
        $res = $this->db->simple_query("select count(*) 
                from  vs_movies");
        $tot = mysql_fetch_array($res);
        return $tot[0];
    }

    //get total series for api

    function get_total_series() {
        $res = $this->db->simple_query("select count(*) 
                from  vs_series");
        $tot = mysql_fetch_array($res);
        return $tot[0];
    }
    //get total series LINKS for api

    function get_total_series_links() {
        $res = $this->db->simple_query("select count(*) 
                from  vs_series_links");
        $tot = mysql_fetch_array($res);
        return $tot[0];
    }

    //return  100 links for db clean

    function get_links_partial($start, $limit) {
        return $this->db->simple_query(" select * from vs_links limit $start , $limit ");
    }

    //
    function delete_single_link($link_id) {
        $data = mysql_fetch_assoc($this->db->simple_query("select link_url from vs_links where link_id = $link_id "));
        $link_url = $data['link_url'];
        $this->insert_invalid_link($link_url);
        $this->db->simple_query("delete from vs_links where link_id= $link_id ");
    }

    //insert into invalid link
    function insert_invalid_link($link_url) {
        $this->db->save_queries = false;
        $link_url = mysql_escape_string($link_url);
        if (!$this->in_invalid_links($link_url))
            $this->db->insert('vs_invalid_links', array('link_url' => $link_url));
    }

    //check if the link is invalid
    function in_invalid_links($link_url) {
        $this->db->reconnect();
        $link_url = mysql_escape_string($link_url);
        if (mysql_num_rows($this->db->simple_query("select * from vs_invalid_links where link_url= '$link_url'")) != 0)
            return 1;
    }

    //function update a report count
    function add_report($link_id) {
        return $this->db->simple_query("update vs_links set report_count=report_count+1 where link_id = $link_id ");
    }

    function add_up($link_id) {
        return $this->db->simple_query("update vs_links set like_count=like_count+1 where link_id = $link_id ");
    }

    //function get report count
    function get_report_count($link_id) {
        $data = mysql_fetch_assoc($this->db->simple_query("select report_count from vs_links where link_id = $link_id "));
        return $data['report_count'];
    }

    //function for adding to not_found column
    function add_to_not_found($link_id) {
        $this->db->reconnect();
        return $this->db->simple_query("update vs_links set not_found=not_found+1 where link_id = $link_id ");
    }

    //function for clearing the not found column
    function clear_not_found($link_id) {
        return $this->db->simple_query("update vs_links set not_found=0 where link_id = $link_id ");
    }

    //function for getting the not_found column
    function get_not_found($link_id) {
        $data = mysql_fetch_assoc($this->db->simple_query("select not_found from vs_links where link_id = $link_id "));
        return $data['not_found'];
    }

    // API functions

    function api_get_links($start, $limit) {
        $res = $this->db->simple_query("select vs_movies.movie_name, vs_links.link_url 
                        from vs_movies inner join vs_links on vs_movies.movie_id = vs_links.movie_id 
                 limit $start, $limit");

        while ($d = mysql_fetch_assoc($res)) {
            $data[] = $d;
        }
        return $data;
    }

    function api_get_movies($start, $limit) {
        $res = $this->db->simple_query("select vs_movies.movie_id, vs_movies.movie_name  from vs_movies 
                 limit $start, $limit");

        while ($d = mysql_fetch_assoc($res)) {
            $data[] = $d;
        }
        return $data;
    }

    function api_single_movie_links($movie_id) {
        $res = $this->db->simple_query("select vs_links.link_url from vs_links where movie_id=$movie_id");

        while ($d = mysql_fetch_assoc($res)) {
            $data[] = $d;
        }
        return $data;
    }

    function api_most_liked($start, $limit) {
        $res = $this->db->simple_query("select vs_movies.movie_name, vs_links.link_url 
                        from vs_movies inner join vs_links on vs_movies.movie_id = vs_links.movie_id order by like_count desc
                 limit $start, $limit");

        while ($d = mysql_fetch_assoc($res)) {
            $data[] = $d;
        }
        return $data;
    }

    //api search get links


    function api_get_search($s) {
        $s = mysql_real_escape_string($s);

        return $this->db->query("
                select movie_id, 
                movie_name,
                movie_channel_link imdb_id,
                movie_release_date release_date 
                from vs_movies               
                where vs_movies.movie_name like '%$s%'  
                ");
    }
    
    function api_get_search_series($s) {
        $s = mysql_real_escape_string($s);

        return $this->db->query("
                select series_id, 
                series_name,
                imdb_link imdb_id,
                series_release_date release_date 
                from vs_series               
                where vs_series.series_name like '%$s%'  
                ");
    }



    function api_single_series_links($series_id) {
        $data = array();
        $res = $this->db->simple_query("select 
                season,
                episode,
                link_url link                
                from vs_series_links 
                where 
                series_id=$series_id");

        while ($d = mysql_fetch_assoc($res)) {
            $data[] = $d;
        }
        
        
        $uni_ar = array();
        
        function get_key($uni_ar,$season,$episode){
            foreach($uni_ar as $key=> $ar):
                if( ($ar['season'] == $season) && ($ar['episode'] == $episode) ):
                    return $key;
                    
                endif;
            endforeach;
            return FALSE;
            
            
        }
        
        
        //summonning serialization
        foreach($data as $chunk_array):
            $key=get_key($uni_ar,$chunk_array['season'] ,$chunk_array['episode']); 
            if($key !== FALSE):
                $uni_ar[$key]['links'][] = $chunk_array['link'];
                
            else:
                $uni_ar[]=array(
                    'season' => $chunk_array['season'],
                    'episode' => $chunk_array['episode'],
                    'links' => array($chunk_array['link']),
                    );
            endif;
                    
        endforeach;
        //var_dump($data);
        //var_dump($uni_ar);
        //exit;
        return $uni_ar;
    }
    
}

?>
