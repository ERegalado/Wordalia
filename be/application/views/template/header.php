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
		
	<script type="text/javascript" src="<?php echo base_url('res/scripts/jquery-1.12.4.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('res/scripts/bootstrap.min.js'); ?>"></script>
	
	<!-- Scripts -->
	<?php if (isset($scripts)) echo $scripts; ?>
</head>

<body id="page-top">
<div id="wrapper" class="center-block">

<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button class="navbar-toggle collapsed" aria-expanded="false" aria-controls="navbar" type="button" data-toggle="collapse" data-target="#navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Wordalia</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar">
          <ul class="nav navbar-nav">
            <li class="active"><?php echo anchor(base_url('index.php/admin/home'),'Home'); ?></li>
            <!--li><?php echo anchor(base_url('index.php/admin/load_words'),'Load'); ?></li-->
            <li class="dropdown">
              <a class="dropdown-toggle" role="button" aria-expanded="false" aria-haspopup="true" href="#" data-toggle="dropdown">CSV <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><?php echo anchor(base_url('index.php/admin/load_words'),'Words'); ?></li>
				<li><?php echo anchor(base_url('index.php/csv/bulk_load'),'Bulk Load'); ?></li>				
              </ul>
            </li>
			<li><?php echo anchor(base_url('index.php/admin/logout'),'Log Out'); ?></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
	
	
