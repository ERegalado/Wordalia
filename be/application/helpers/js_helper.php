<?php if(!defined('BASEPATH')) exit('No direct script access allowed');



function search(){
	$search = '
	<!-- Search -->
	<script src="'.base_url('res/scripts/maps/search.js').'"></script>
	<script src="'.base_url('res/scripts/maps/gravity.js').'"></script>';
	
	return $search;
}

function jQuery(){
	$jQuery = '
	<!-- jQuery -->
	<script src="'.base_url('scripts/jquery-1.12.4.min.js').'"></script>';
	
	return $jQuery;
}

function jUI(){
	$jui = '
	<!-- jQuery-UI -->
	<script src="'.base_url('scripts/jquery-ui-1.12.1.custom/jquery-ui.js').'"></script>
	<link rel="stylesheet" type="text/css" href="'.base_url('scripts/jquery-ui-1.12.1.custom/jquery-ui.css').'"/>';
	
	return $jui;
}

function jlist(){
	$jlist ='
	<!-- list.js -->
	<script type="text/javascript" src="'.base_url('res/scripts/list/list.js').'"></script>
	<script type="text/javascript" src="'.base_url('res/scripts/list/list.paging.js').'"></script>
	';
	return $jlist;	
}

?>