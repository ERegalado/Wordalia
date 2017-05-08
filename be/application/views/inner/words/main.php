<div class="jumbotron" style="padding-top:30px;">
<br/>
<h1>WORDS</h1>
<?php 
	echo printTable('Search',
	array('Word','Definition','Example','Translation','Date Published'),
	$words,
	array('WORD','DEFINITION','EXAMPLES','WORD_TRANSLATION','DATE_PUBLISHED'),
	NULL // array('index.php/administrar/ubicaciones/',NULL,'location_id','Acciones')
	);
?>

<?php /*if ($this->session->userdata(Level)==0){
	echo anchor(base_url('index.php/administrar/ubicaciones/agregar'),'Agregar Nueva Ubicaci&oacute;n','class="btn btn-primary"');
	echo anchor(base_url('index.php/administrar/ubicaciones/cargar'),'Cargar Promocionales/Canjeables','class="btn btn-primary"');
}*/  ?>

<br/>
<br/>
<?php if($this->session->flashdata('msg')): ?>
<div class="alert alert-success" role="alert">
    <?php echo $this->session->flashdata('msg'); ?>
</div>
<?php endif; ?>
</div>
