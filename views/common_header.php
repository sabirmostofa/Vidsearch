<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

 echo doctype('xhtml1-trans');
?>

<html>
       <head>
        <title>VidSearch</title>
        
        <?php echo link_tag('application/views/css/960.css'); ?>
        <?php echo link_tag('application/views/css/style.css'); ?> 
        <script type="text/javascript" src="<?php echo base_url(), 'application/views/js/jquery-1.6.2.min.js' ?>"></script>
        <script type="text/javascript" src="<?php echo base_url(), 'application/views/js/jquery.autocomplete-min.js' ?>"></script>
        <script type="text/javascript" src="<?php echo base_url(), 'application/views/js/custom.js' ?>"></script>
        <script type="text/javascript">
            ajaxUrl = "<?php echo index_page(),'?c=ajax' ?>";
            </script>
    </head>
    <body>
