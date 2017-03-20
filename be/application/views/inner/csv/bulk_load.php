<div class="jumbotron" style="padding-top:30px;">
<h1>Bulk Load</h1>
<p>Insert the content of a CSV in the DB.</p>

<?php if(isset($error)): ?>
<div class="alert alert-danger" role="alert">
    <?php echo $error; ?>
</div>
<?php endif; ?>


<?php if($this->session->flashdata('msg')): ?>
<div class="alert alert-success" role="alert">
    <?php echo $this->session->flashdata('msg'); ?>
</div>
<?php endif; ?>

<div class="well">
<h3 class="primaryColor">Bulk Load</h3>
<?php 
	echo printForm(
		array('post'),
		array('index.php/csv/bulk_load',true),
		array('Archivo'),
		array(
			array('name'=> 'wd_load','type'=> 'file','attrs'=> 'accept=".csv"','required'=> 'required' ),
		),
		array('name'=> 'u1','value'=> 'Load','attrs'=> 'id="u1" class="btn btn-primary"')
	);
?>
<!--p>Descargar formato de ejemplo <?php echo anchor(base_url('resources/formats/staff_employees_format.csv'),'Aqu&iacute;'); ?></p-->
</div><!--/well-users -->

</div> <!--/jumbotron -->