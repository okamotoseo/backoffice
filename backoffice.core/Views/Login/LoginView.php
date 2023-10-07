<?php if ( ! defined('ABSPATH')) exit;?>
<!DOCTYPE html>
<html>
<head>

    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SYSPlace | Sistema de Integração de Estoque com Marketplaces</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    
    
    <style type="text/css">

/* 	 { */
/* 	  box-sizing: border-box; */
/* 	} */
	
/*   	.bg-img {   */
/*    	  background-image: url("https://backoffice.sysplace.com.br/Views/_uploads/images/Inventory-background.png") !important;  */
/*   	  min-height: 380px;  */
/*   	  background-position: center;   */
/*   	  background-repeat: no-repeat;  */
/*   	  background-size: cover;   */
/*   	  position: relative;   */
 	   
 	  
 	 
/*   	}   */
    
  .video {
    position: fixed;
    right: 0;
    bottom: 0;
    min-width: 100%;
    min-height: 100%;
}  

.login-box {
	width: 320px !important;
    margin: 15% auto !important;
    
    
    color: #fff;
    opacity: 0.95;
    position:relative;
    box-shadow: 0px 0px 40px black;
    background: rgba(0,0,0,0.1);
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
     	
}
.login-box .login-logo{
	color: #fff;

}

.login-box .login-box-msg{

}

.login-box .login-box-body{
	color: #fff;
    background: rgba(0,0,0,0.3);
    border-top-left-radius: 10px;
    border-top-right-radius: 10px ;
}
.login-box .box-footer{
	color: #fff;
	background: rgba(0,0,0,0.3);
 	border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    padding-top:25px;
    padding-bottom:25px;
}
    
    </style>
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/bootstrap/css/bootstrap.min.css" />
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/dist/css/AdminLTE.min.css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/dist/css/skins/_all-skins.min.css" />
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
     <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>   
	
</head>
 
<body class="hold-transition login-page bg-img">

	<video id="video_rand" autoplay="autoplay" muted="muted" loop="loop" class="video">
		<source src="/Views/_uploads/images/back-login-3.mp4" type="video/mp4" class="video_source">
	</video>

	<form action="" name='login' method="post">
	<div class="login-box"  >
	
		<div class="login-box-body">
		
			<div class="login-logo">
				<a href="#" style="color:white;font-size:45px;"><b>SYS</b>Place</a> <span style='font-size:15px'><i>4.7.5</i></span>
			</div>
	      
			<p class="login-box-msg" >Faça login para iniciar sua sessão</p>
			<div class="form-group has-feedback">
				<input type="email" class="form-control" name='userdata[email]' placeholder="Email">
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
			</div>
	        
			<div class="form-group has-feedback">
				<input type="password" class="form-control" name='userdata[password]'  placeholder="Password">
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			</div>
	        
	      		<?php if ( !empty($this->login_error) ) { echo "<span style=' color:#ff0000;'>". $this->login_error."</span>"; }?>
		</div>
	
	      
		<div class="box-footer">
			<button type="submit" class="btn btn-primary btn-block  pull-right"  name='btn_login'>Entrar</button>
		</div>
    </div>
    </form>
    
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Sparkline -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/sparkline/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/library/themes/AdminLTE-2.3.0/dist/js/app.min.js"></script>

    
  </body>
</html>