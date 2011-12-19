<?php
function reform_title($title) {
	$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
    //$title = strtolower($title);
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	//$title = str_replace('.', '-', $title);
	$title = preg_replace('/[^%a-zA-Z0-9\'"; $%^&*()<>_\-+=`~\]\\\|.,@#!\?\[:]/', '', $title);
	//$title = preg_replace('/\s+/', '-', $title);
	//$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');
	$title = trim($title, '.');
	return trim($title);        
        
}
function reform_url($title) {
	$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	$title = trim($title, '-');
	$title = trim($title, '.');
	return trim($title);     
        
}

?>
