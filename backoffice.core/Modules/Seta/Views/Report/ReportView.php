<style>
  .ui-autocomplete-loading {
    background: white url("./images/ajax-loader-mini.gif") right center no-repeat;
  }
</style>

<div class="row">
	<div class="col-xs-12">
		<?php 
		  require ABSPATH . "/Modules/Seta/Views/Report/Form/{$this->form}.php"; 
		?>
	</div>
</div>

<div class="row">
    <div class="col-xs-12">
    	<?php 
        	if(isset($list)){ 
        	    require ABSPATH . "/Modules/Seta/Views/Report/Layout/{$this->layout}.php";
        	} 
    	?>
    </div>
</div>