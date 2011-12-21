<?php include 'common_header.php'; ?>
<div class="container_12">
    <div class="grid_10 push_1" id="search_back">
        <div id="logo_vid">
            <img src="<?php echo base_url(), 'application/views/images/title_image.png' ?>"/>
        </div>
            <div class="clear"></div>
            <div id="search_form">
            <form method="get" action="">
            <input type="text" name="search_term" id="search_box"/>
            <input id="submit_image" type="image" name="submit" src="<?php echo base_url(), 'application/views/images/alt_s_button.png' ?>"/>
        <form>
            </div>
        </div>
<div class="clear"></div>    
    <!-- Start Showing The Search Results -->
<div class="grid_9" id="result_container">
    <?php 
    var_dump($data);
    ?>
</div>

</div>

<?php include 'common_footer.php'; ?>
