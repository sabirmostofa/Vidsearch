<?php include 'common_header.php'; ?>
<?php

$data_type=null;
if(isset($_GET['data_type']))
 $data_type = $_GET['data_type'];   



?>
<div class="container_12">
    <div class="grid_10 push_1" id="search_back">
        <div id="logo_vid">
            <img src="<?php echo base_url(), 'application/views/images/title_image.png' ?>"/>
        </div>
        <div class="clear"></div>
        <div id="search_form">
            <form method="get" action="">
                <input type="text" value="<?php if(isset($_GET['search_term'])) echo $_GET['search_term'] ?>"  name="search_term" id="search_box"/>
                <input id="submit_image" type="image" name="submit" src="<?php echo base_url(), 'application/views/images/alt_s_button.png' ?>"/>
               
                    </div>
                    </div>
                    <div class="clear"></div>    
                    <br/>
                    <div class="grid_3" style="display:none"></div>
                    
            <div class="grid_6">
             <div style="float:right;">   
            <input id="radio_movie" style="" type="radio" <?php echo ($data_type == 'series')? '': 'checked="checked"'; ?> 
                   name="data_type" value="movies"/> 
            <span style="">Movies</span>
            </div>
            </div>
             <div class="grid_6">
                <input style='' type="radio" 
                       <?php echo ($data_type == 'series')? 'checked="checked"':''; ?>
                       name="data_type" value="series"/> 
                <span style="">Tv Shows</span>
              </div>
<!--            <div class="grid_3"></div>-->
            
                    <form>
                    <!-- Start Showing The Search Results -->
<!--                    <div class="grid_9" id="result_container">-->
                        <?php if(isset($_GET['search_term'])):  
                        if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) )
                            $cur_page= $_GET['per_page'];
                        else $cur_page = 0;
                        
                    $start =1
               
                        
                            
?>
                         <div class="grid_9 prefix_2" style="font-size: 15px;line-height: 20px; margin-top: 20px" >  
                    <div style="margin-left:15px"> All Seasons and Episodes for <b><?php echo $_GET['search_term']  ?></b></div>
                       <br/>
                        <?php if(isset($data) && is_array($data) ):
                           
                            foreach($data as $single):  ?>
                        <div class="grid_3 alpha omega ">
                          <b><?php echo $start++ ?>.</b>  
                          <?php $url= base_url(). sprintf('?search_term=%s&season=%s&episode=%s&data_type=series&series_id=%s',$_GET['search_term'], $single->season, $single ->episode,$single->series_id); ?>
                          <a class="single_link" href="<?php echo $url    ?>">
                          <b><?php echo sprintf( "Season %s Episode %s", $single->season, $single->episode ); ?></b>
                          </a> 
                            <br/>                            
                            </div>
                    <?php endforeach; ?>
              
                    <?php endif; //end of series found ?>
                        <div class="clear"></div>
                            
                            
                        
                        <div class="clear"></div>
                        
                        <!-- Pagination -->
                        
                <div class="grid_7 push_2" id="pagination_div"->
                        <?php 
                        echo $this->pagination->create_links();
                        
                        ?>
                    </div>
                        <?php endif; ?>
                        
                    </div> <!--End of Result COntainer   -->

                    </div>

                    <?php include 'common_footer.php'; ?>
