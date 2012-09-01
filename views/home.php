<?php include 'common_header.php'; ?>
<?php

$data_type=null;
if(isset($_GET['data_type']))
 $data_type = $_GET['data_type'];   

$season = null;
if(isset($_GET['season']))
 $season = $_GET['season']; 
$episode = null;
if(isset($_GET['episode']))
 $episode = $_GET['episode']; 



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
<!--                    <div class="grid_3" style="display:none"></div>-->
                    
            <div class="grid_6">
             <div style="float:right;">   
            <input id="radio_movie" style="" type="radio" <?php echo ($data_type == 'series')? '': 'checked="checked"'; ?> 
                   name="data_type" value="movies"/> 
            <span style="">Movies</span>
            </div>
            </div>
             <div class="grid_6">
                <input style='' type="radio" 
                       <?php echo ($data_type == 'series')? 'checked="checked"': '' ; ?>
                       name="data_type" value="series"/> 
                <span style="">Tv Shows</span>
              </div>
<!--            <div class="grid_3"></div>-->
            
                    <form>
                    <!-- Start Showing The Search Results -->
                    <div class="grid_9" id="result_container">
                        <?php if(isset($_GET['search_term'])):  
                        if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) )
                            $cur_page= $_GET['per_page'];
                        else $cur_page = 0;
                        
                        $start= $cur_page+1;
                        $start=  $total_num == 0?0:$start;
                        $end= $start+9;
                        $end = $end>$total_num? $total_num:$end;
                        
                        $upvote = 0;
                        $downvote =0;
               
                        
                            
?>
                        
                    <div style="margin-left:15px"> Results <b><?php echo $start  ?></b> to <b><?php echo $end; ?></b> of <b><?php echo $total_num ?></b> for <b><?php echo $_GET['search_term']  ?>
                         <?php if($season)echo sprintf(': Season %s Episode %s', $season, $episode); ?> 
                        </b></div>
                        
                        <?php if(isset($data))foreach($data as $single):?>
                        <div class="grid_8 link_indiv">
                          <b><?php echo $start++ ?>.</b>  <a class="single_link" href="<?php echo $single->link_url ?>">
                              <b>
                                  <?php echo $single->name ?>
                                  <?php if($season)echo sprintf(': Season %s Episode %s', $season, $episode); ?>
                              
                              </b></a>
                          <div class="report">
                                <a class="upvote"  url="#" id="up<?php echo $single->link_id ?>"></a>
                               <span class="votecount"> (<?php echo $single->like_count ?>)</span>
                                <a  class="downvote" url="#"  id="down<?php echo $single->link_id ?>"></a>
                                <span class="votecount"> (<?php echo $single->report_count ?>)</span>
                                </div>
                            <br/>
                            
                            <div style="margin-left:25px"><b>Link:</b> <a  class="single_link" href="<?php echo $single->link_url ?>"><?php echo $single->link_url?></a></div> 
                            
                            </div>
                        <div class="clear"></div>
                            
                            
                        <?php endforeach; ?>
                        <div class="clear"></div>
                        
                        <!-- Pagination -->
                        
                <div class="grid_7 push_2" id="pagination_div"->
                        <?php 
                        echo $this->pagination->create_links();
                        
                        ?>
                    </div>
                        <?php endif; ?>
                        
                    </div><!-- End of Result COntainer   -->

                    </div>

                    <?php include 'common_footer.php'; ?>
