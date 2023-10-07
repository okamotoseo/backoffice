<?php 
header("Cache-Control: max-age=0");
if ( ! defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php 
//     pre($this);die;
    ?>
    <title><?php echo $this->title; ?> | SYSPlace - Sistema de Integrações de Marketplaces</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!--     <link rel="shortcut icon" type="image/x-icon" href="backoffice.sysplace.com.br/favicon.ico" > -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
<!--     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css"/> -->
    <script src="https://kit.fontawesome.com/3b7af3f9e7.js" crossorigin="anonymous"></script>
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/datatables/dataTables.bootstrap.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/dist/css/AdminLTE.min.css">
    <!-- Modal dialogs -->
<!--     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" /> -->
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/dist/css/skins/_all-skins.min.css">
    <!-- iCheck -->
     <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/iCheck/all.css">

    <!-- iCheck for checkboxes and radio inputs -->
    <!-- Select2 -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/select2/select2.min.css">

    <!-- Morris chart -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/morris/morris.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/datepicker/datepicker3.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/daterangepicker/daterangepicker-bs3.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" type="text/css">
	
	<link rel="stylesheet" href="/library/js/jquery-3.5.1/jquery-ui.css" type="text/css" >
    
	<link rel="stylesheet" href="/library/bootstrap-fileinput-master/css/fileinput.min.css" type="text/css" />
	<link rel="stylesheet" href="/library/bootstrap-fileinput-master/themes/explorer/theme.min.css" type="text/css" />
	<link href="https://transloadit.edgly.net/releases/uppy/v1.14.1/uppy.min.css" rel="stylesheet">
	
	
	
    <link rel="stylesheet" href="/Views/Styles/Custom.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
        <!-- jQuery 2.1.4 -->
 <script src="/library/js/jquery-3.5.1/jquery-3.5.1.min.js"></script>
 <style type="text/css">
 .table > tbody> tr > td{
 
    vertical-align:middle;
 
 </style> 
<?php 
$developer = false;
if($this->userdata['cpf'] == '30456130802' OR $this->userdata['cpf'] == '30269241809'){
	$developer = true;
}

?>
	
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
<header class="main-header">
        
        <!-- Logo -->
        <a href="/Home" class="logo">
	        <!-- mini logo for sidebar mini 50x50 pixels -->
	        <span class="logo-mini"><b>S</b>P</span>
	        <!-- logo for regular state and mobile devices -->
	        <span class="logo-lg"><b>SYS</b>Place</span>
        </a>
        
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
	        <!-- Sidebar toggle button-->
	        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
	        	<span class="sr-only">Toggle navigation</span>
	        </a>
			<div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
			<!-- Notifications: style can be found in dropdown.less -->
              <li class="dropdown notifications-menu">
                <a href="/Sac/Questions/" >
                  <i class="fa  fa-comments-o"></i>
                  <span class="label label-warning"><?php  echo totalQuestionStatus($this->db, $this->storedata['id'], 'UNANSWERED'); ?></span>
                </a>
              </li>
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <?php

                    $file = HOME_URI . "/Views/_uploads/images/profile/160x160/{$this->userdata['id']}.png";
                    
                    if(@getimagesize($file)){
                        
                        $src = $file;
                        
                    }else{
                        
                       $file = HOME_URI . "/Views/_uploads/images/profile/160x160/{$this->userdata['id']}.jpg";
                        
                        if(@getimagesize($file)){
                            
                            $src = $file;
                            
                        }else{
                            
                            $src = HOME_URI . "/Views/_uploads/images/profile/160x160/default.png";
                            
                        }
                    }
                
                ?>
                  <img src="<?php  echo $src; ?>" class="user-image" alt="User Image">
                  <span class="hidden-xs"><?php  echo $this->userdata['name']; ?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                  
                                  <?php

                    $file = HOME_URI . "/Views/_uploads/images/profile/160x160/{$this->userdata['id']}.png";
                    if(@getimagesize($file)){
                        $src = $file;
                    }else{
                        $file = HOME_URI . "/Views/_uploads/images/profile/160x160/{$this->userdata['id']}.jpg";
                        if(@getimagesize($file)){
                            $src = $file;
                        }else{
                            $src = HOME_URI . "/Views/_uploads/images/profile/160x160/default.png";
                        }
                    }
                
                ?>
                    <img src="<?php  echo $src; ?>" class="img-circle" alt="User Image">
                    
                    <p>
                      <?php  echo $this->userdata['name']; ?>
                      <small>Menbro desde <?php  echo dateBr($this->userdata['created'], '/'); ?></small>
                    </p>
                  </li>
                <!-- Menu Body -->
<!--                   <li class="user-body"> -->
<!--                     <div class="col-xs-4 text-center"> -->
<!--                       <a href="#">Followers</a> -->
<!--                     </div> -->
<!--                     <div class="col-xs-4 text-center"> -->
<!--                       <a href="#">Sales</a> -->
<!--                     </div> -->
<!--                     <div class="col-xs-4 text-center"> -->
<!--                       <a href="#">Friends</a> -->
<!--                     </div> -->
<!--                   </li> -->
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="<?php echo HOME_URI . '/User/Register/'; ?>" class="btn btn-default">Usuários</a>
                    &nbsp;
                      <a href="<?php echo HOME_URI . '/User/Profile/'; ?>" class="btn btn-default">Perfil</a>
                    </div>
                    <div class="pull-right">
                    	<button class="btn btn-default" id='logout_user' >Sair</button>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
	</header>
	
	<?php  include "menu.php"; ?>
	
	<div class="content-wrapper ">
		
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1><?php echo $this->panel;?><small><?php echo isset($this->control_panel)  && !empty($this->control_panel) ? $this->control_panel : 'Control panel' ; ?></small></h1>
			<ol class="breadcrumb">
				<li><a href="/Home" ><i class="fa fa-dashboard"></i>backoffice</a></li>
				<li class="active"><?php echo substr(get_class($this), 0, -10); ?></li>
				<li class="active"><?php echo $this->panel;?></li>
			</ol>
		</section>
        
		<!-- Main content -->
		<section class="content">
            <input type="hidden"  id ="user_id" value="<?php echo $this->userdata['id'];?>" />
    		<input type='hidden' id='store_id' value='<?php echo $this->userdata['store_id'];?>' />
    		<input type='hidden' id='account_id' value='<?php echo $this->userdata['account_id'];?>' />
    		<input type='hidden' id='user_name' value='<?php echo $this->userdata['name'];?>' />
    		<input type='hidden' id='home_uri' value='<?php echo HOME_URI; ?>' />