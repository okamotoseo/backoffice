 <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.5 -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/bootstrap/js/bootstrap.min.js"; ?>" ></script>
    <!-- Morris.js charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/morris/morris.min.js"; ?>" ></script>
    <!-- Sparkline -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/sparkline/jquery.sparkline.min.js"; ?>" ></script>
    <!-- jvectormap -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"; ?>" ></script>
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"; ?>" ></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/knob/jquery.knob.js"; ?>" ></script>
    <!-- daterangepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/daterangepicker/daterangepicker.js"; ?>" ></script>
    <!-- datepicker -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/datepicker/bootstrap-datepicker.js"; ?>" ></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"; ?>" ></script>
    <!-- DataTables -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/datatables/jquery.dataTables.min.js"; ?>" ></script>
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/datatables/dataTables.bootstrap.min.js"; ?>" ></script>
    <!-- Slimscroll -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/slimScroll/jquery.slimscroll.min.js"; ?>" ></script>
    <!-- FastClick -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/plugins/fastclick/fastclick.min.js"; ?>" ></script>
    <!-- AdminLTE App -->
    <script src="<?php  echo HOME_URI . "/library/themes/AdminLTE-2.3.0/dist/js/app.min.js"; ?>" ></script>
    <?php 
    	if(isset($this->includes['js'])){
	    foreach($this->includes as $key => $path){
	        echo "<script src='" . HOME_URI . $path."?".date("H:i:s")."' language='javascript'></script>";
    	}
    }
	?>
  </body>
</html>