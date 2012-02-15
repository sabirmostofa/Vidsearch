<?php
class Cron extends CI_Controller {
    
    public function index(){
        $base_url = 'http://www.1channel.ch';
        $url = 'main.html';
        
        $dom = new DOMDocument();
       $content = file_get_contents($url);       
        $dom->loadHTML($content);
        
     
        
        foreach ($dom->getElementsByTagName('div') as $div){      
            if($div-> getAttribute('class') == 'pagination') {
                $pag = array();
                foreach($div->getElementsByTagName('a') as $a){
                    preg_match('/\d+/',  $a->getAttribute('href'), $val );
                    $pag[]=$val[0];         
                                       
                }
                
                echo $max_page= array_pop($pag);
                
                
                
                }
        }// endforeach
        
  
        
        //Getting all pages
        for($i=0;$i<=$max_page; $i++):
            
            if($i != 0){
            $base= 'http://www.1channel.ch/index.php?page=';
            $page = $base.$i;
             $content = file_get_contents($page); 
            $dom->loadHTML($content);
            }
            
            foreach ($dom->getElementsByTagName('div') as $div){  
               
            if( $div->getAttribute('class') == 'index_item index_item_ie' ){
                
               $mov_url = $base_url. $div->getElementsByTagName('a')->item(0)->getAttribute('href');
               $mov_dom = new DOMDocument();
               $mov_dom -> loadHTML( file_get_contents($mov_url) );
               
               //getting title
               foreach($mov_dom ->getElementsByTagName('meta') as $meta )
                   if($meta -> getAttribute('property') == 'og:title' )
                       $m_title = $meta->getAttribute('content');
                   
                   echo $m_title, '<br/>';
                   
                   $m_genres = array();
                   $m_actors = array();
                   $m_links = array();
                   
                   //$info_needed= array('movie_info_genres', 'movie_info_actors', );
                   //getting other info
                   foreach($mov_dom ->getElementsByTagName('span') as $span):
                       
                       switch( $span ->getAttribute('class')):
                        case 'movie_info_genres':
                            foreach( $span->getElementsByTagName('a') as $a )
                                $m_genres[] = $a->textContent;
                            break;
                            
                        case 'movie_info_actors':
                            foreach( $span->getElementsByTagName('a') as $a )
                                $m_actors[] = $a->textContent;
                            break;
                            
                        case 'movie_version_link':
                            foreach( $span->getElementsByTagName('a') as $a )
                                 preg_match( '//',$a->getAttribute('href'), $link);
                            break;
                      
                       endswitch;
                       
                       
                   endforeach;
             
            }
            
            }
            
            exit;
        endfor;
        
    }
}
