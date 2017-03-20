<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Supernova IC">

    <title>Wordalia - Administration Portal</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="<?php echo base_url('res/styles/css/bootstrap.min.css'); ?>" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('res/styles/css/creative.css'); ?>" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<!-- Favicon -->	
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
</head>

<body id="page-top">
<div id="wrapper" class="center-block">

<div class="jumbotron">
<div class="row">
  <div class="col-md-12"><h1>Wordalia - Administration Portal</h1></div>
</div>
<hr/><br/>
<p>Welcome to Wordalia's Administration Portal. Please enter your username and password:</p>
<?php 
	echo printForm(
		array('post'),
		array('index.php/admin/'),
		array('Username','Password'),
		array(
			array('name'=> 'username','type'=> 'text','attrs'=> 'class="form-control"','required'=> 'required' ),
			array('name'=> 'password','type'=> 'password','attrs'=> 'class="form-control"','required'=> 'required' )
		),
		array('name'=> 'submit','value'=> 'Sign in','attrs'=> 'class="btn btn-primary"')
		// $vals=NULL
	);


?>

<?php echo validation_errors('<h5 class="message message-error" style="max-width:45%">','</h5>'); ?>
<br/>

<?php if($this->session->flashdata('msg')): ?>
<?php echo $this->session->flashdata('msg'); ?>
<?php endif; ?>

</div>

</div> <!-- /wrapper -->
</body>
</html>