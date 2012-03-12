<?php include 'common_header.php'; ?>
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
                <form>
                    </div>
                    </div>
                    <div class="clear"></div>    
           
                    
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
                        
                            
?>
                        
                    <div style="margin-left:15px"> Results <b><?php echo $start  ?></b> to <b><?php echo $end; ?></b> of <b><?php echo $total_num ?></b> for <b><?php echo $_GET['search_term']  ?></b></div>
                        
                        <?php if(isset($data))foreach($data as $single):?>
                        <div class="grid_8 link_indiv">
                          <b><?php echo $start++ ?>.</b>  <a class="single_link" href="<?php echo $single->link_url ?>"><b><?php echo $single->movie_name ?></b></a>
                          <div class="report">
                                <a href="#" id="<?php echo $single->link_id ?>">Report as dead</a>
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
