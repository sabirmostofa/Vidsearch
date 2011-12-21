<?php include 'common_header.php'; ?>

<?php
echo form_open('c=upload&m=form_alt');
$name= array(
    'name' => 'csv_file',
    'id' => 'file_name',
    'value' => ''
);





?>

<h3>Process file</h3>




<?php echo form_submit('submit', 'Process the uploaded CSV file'); ?>


<?php include 'common_footer.php'; ?>

