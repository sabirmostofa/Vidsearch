<?php include 'common_header.php'; ?>

<?php
echo form_open_multipart('upload/form');
$name= array(
    'name' => 'csv_file',
    'id' => 'file_name',
    'value' => '',
    'type' => 'file'
);





?>

<h3>Upload File</h3>
Choose file <?php echo form_input($name); ?>
<br/>



<?php echo form_submit('submit', 'Submit'); ?>


<?php include 'common_footer.php'; ?>