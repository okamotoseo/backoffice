<?php if ( ! defined('ABSPATH')) exit; ?>
</section><!-- /.content -->
	</div><!-- /.content-wrapper -->
     	<?php // include_once 'main_control_sidebar.php';
        $year = date("Y");
        ?>
		<footer class="main-footer noPrint">
			<div class="pull-right hidden-xs"><b>Version</b> 4.7.5</div>
			<strong>Copyright &copy; <?php echo "2014-{$year}"; ?> &copy; <a href='https://sysplace.com.br' target='_blank'>sysplace.com.br</a> </strong> All rights reserved.
		</footer>
</div><!-- ./wrapper -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/jQueryUI/jquery-1-12-1-ui.js";?>"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.5 -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/bootstrap/js/bootstrap.min.js"; ?>"></script>
	<script type="text/javascript" src="<?php  echo HOME_URI . "/library/bootstrap-fileinput-master/js/fileinput.min.js"; ?>" ></script>
	<script type="text/javascript" src="<?php  echo HOME_URI . "/library/bootstrap-fileinput-master/themes/explorer/theme.min.js"; ?>" ></script>
	<script type="text/javascript" src="<?php  echo HOME_URI . "/library/bootstrap-fileinput-master/js/plugins/sortable.js"; ?>" ></script>
	<!-- Morris.js charts -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	
	<script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/morris/morris.min.js"; ?>"></script>
    <!-- Sparkline -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/sparkline/jquery.sparkline.min.js"; ?>"></script>
    <!-- jvectormap -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"; ?>"></script>
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"; ?>"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/knob/jquery.knob.js"; ?>"></script>
    <!-- daterangepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/daterangepicker/daterangepicker.js"; ?>"></script>
    <!-- datepicker -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/datepicker/bootstrap-datepicker.js"; ?>"></script>
    <!-- CK Editor -->
	<!--     <script src="https://cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script> -->
	<script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/ckeditor/ckeditor.js"; ?>"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"; ?>"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.0.2/js/dataTables.rowGroup.min.js"></script>
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/datatables/dataTables.bootstrap.min.js"; ?>"></script>
    <!-- Select2 -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/select2/select2.full.min.js"; ?>"></script>
    <!-- InputMask -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/input-mask/jquery.inputmask.js"; ?>"></script>
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/input-mask/jquery.inputmask.date.extensions.js"; ?>"></script>
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/input-mask/jquery.inputmask.extensions.js"; ?>"></script>
    <!-- bootstrap color picker -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/colorpicker/bootstrap-colorpicker.min.js"; ?>"></script>
    <!-- bootstrap time picker -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/timepicker/bootstrap-timepicker.min.js"; ?>"></script>
    <!-- Slimscroll -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/slimScroll/jquery.slimscroll.js"; ?>"></script>
    <!-- iCheck 1.0.1 -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/iCheck/icheck.min.js"; ?>"></script>
    <!-- FastClick -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/fastclick/fastclick.min.js"; ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/dist/js/app.min.js"; ?>"></script>
	<?php
// 	echo "<script src='" . HOME_URI . "/Views/js/default.js?".date("H:i:s")."' language='javascript'></script>";
	echo "<script src='" . HOME_URI . "/Views/js/default.js' language='javascript'></script>";
	if(isset($this->includes['js'])){
	    $includesJs = $this->includes['js'];
	    if(is_array($includesJs)){
	        foreach($includesJs as $key => $path){
    	        echo "<script src='" . HOME_URI . $path."?".date("H:i:s")."' language='javascript'></script>";
        	}
	    }else{
	        echo "<script src='" . HOME_URI . $includesJs."?".date("H:i:s")."' language='javascript'></script>";
	    }
    }
	?>
	<script src="//cdn.datatables.net/plug-ins/1.10.22/sorting/datetime-moment.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>
	
  </body>
</html>