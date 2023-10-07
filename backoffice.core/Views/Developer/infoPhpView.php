<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<div class='col-md-12'>
		<div class='box box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'><?php echo $this->title; ?></h3>
			</div><!-- /.box-header -->
			<div class='box-body'>
				<div class="row">
					<div class="col-md-12">
						<?php echo phpinfo(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
