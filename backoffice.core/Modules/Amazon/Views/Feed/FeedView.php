<?php 
if ( ! defined('ABSPATH')) exit;

?>

<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $feedModel->form_msg)){ echo $feedModel->form_msg;}?>
		</div>
		
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Modules/Amazon/Feed/SubmittedFeed" name="form-store">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='<?php echo HOME_URI ?>/Modules/Amazon/Feed/SubmittedFeed' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
	
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-xs-2">		
						<div class="form-group">
							<label for="FeedSubmissionId">FeedSubmissionId:</label> 
							<input type="text" name="FeedSubmissionId" class="form-control" value="<?php echo $feedModel->FeedSubmissionId; ?>" />
						</div>
					</div>
					<div class="col-xs-2">		
						<div class="form-group">
							<label for=FeedType:>FeedType:</label> 
							<input type="text" name="FeedType:" class="form-control" value="<?php echo $feedModel->FeedType; ?>" />
						</div>
					</div>
					<div class="col-xs-2">		
						<div class="form-group">
							<label for="SubmittedDate">SubmittedDate:</label> 
							<input type="text" name="SubmittedDate" class="form-control" value="<?php echo $feedModel->SubmittedDate; ?>" />
						</div>
					</div>
					<div class="col-xs-2">		
						<div class="form-group">
							<label for="FeedProcessingStatus">FeedProcessingStatus:</label> 
							<input type="text" name="FeedProcessingStatus" class="form-control" value="<?php echo $feedModel->FeedProcessingStatus; ?>" />
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-primary btn-sm pull-right"  name="search">Buscar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>
<?php if(isset($feeds)){?>
<div class="row">
	<div class="col-xs-12">

    	<div class="box box-primary">
        	<div class="box-header">
        		<h3 class="box-title"><?php echo $this->title; ?></h3>
        		<div class="box-tools pull-right">
					<button class="btn btn-default btn-xs" id='submit_products_feed' >1 - Atualizar Feed de Produtos</button>
					<button class="btn btn-default btn-xs" id='submit_inventory_feed' >2 - Atualizar Feed de Estoque</button>
					<button class="btn btn-default btn-xs" id='submit_price_feed' >3 - Atualizar Feed de Preços</button>
					<button class="btn btn-default btn-xs" id='submitted_feed' >4 - Atualizar Status do Feed</button>
				</div>
        	</div>
        	<div class="box-body">
        		<div class="row">
        		<div class="col-xs-12">
        		<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
                <thead>
                <tr>
                	 <th>SubmittedDate</th>
                     <th>FeedSubmissionId</th>
                     <th>FeedType</th>
                     <th>Status</th>
                     <th>FeedProcessingStatus</th>
                     <th>CompleteProcessingDate</th>
                     <th>Ações</th>
                </tr>
                </thead>
         		<tbody>
                     <?php 
                     foreach ($feeds as $key => $feed){ 
                         $path =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$feed['store_id']}/xml/FeedSubmissionResult-{$feed['FeedSubmissionId']}.xml";
                         $pathShow =  "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$feed['store_id']}/xml/FeedSubmissionResult-{$feed['FeedSubmissionId']}.xml";
	                     
                         $StartedProcessingDate = !empty($feed['StartedProcessingDate']) ? $feed['StartedProcessingDate'] : 'Processing...' ;
                         $CompletedProcessingDate = !empty($feed['CompletedProcessingDate']) ? $feed['CompletedProcessingDate'] : 'Processing...' ;
	                     echo "<tr >
                            <td>".dateTimeBrBreakLine($feed['SubmittedDate'])."</td>
							<td>{$feed['FeedSubmissionId']}</td>
							<td>{$feed['FeedType']}</td>
							<td>{$feed['FeedProcessingStatus']}</td>
							<td>{$StartedProcessingDate}</td>
							<td>{$CompletedProcessingDate}</td>
                            <td>
                                <div class='dropdown'>
                                    <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                    <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                        <li role='presentation'>";
                                       if(file_exists($path)){
                                    	    echo "<a class='result_modal' data-toggle='modal' data-target='#amazon_feed_result_modal' file='{$pathShow}' id='{$feed['id']}'  FeedSubmissionId='{$feed['FeedSubmissionId']}'>Resultado 1</a>";
                                    	}else{
                                    	    echo "<a class='amazon_feed_action' action='get_feed_result' id='{$feed['id']}'  FeedSubmissionId='{$feed['FeedSubmissionId']}'>Resultado 2</a>";
                                    	}
                                    	    
                                    echo " </li>
                                    </ul>
                                </div>
                            </td>
	                     </tr>";
                     
                     	}
                     ?>
         
         		</tbody>
        		</table>
        		</div>
        		</div>
        	</div>
        	<div class="overlay amazon-feed-loading" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
    	</div>
	</div>
</div>


<?php }?>

<div class="modal fade" id='amazon_feed_result_modal' role='dialog'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Resultado Feed Enviado</h4>
        
      </div>
      <div class="modal-body">
      <div id='message'></div>

	  <div class='row'>
    	  <div id='id'></div>
    	  <div id='FeedSubimissionId'></div>
    	  <div class="col-xs-12">
    	  	<pre id='file'></pre>
    	  </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default  btn-sm pull-right" id='close-modal' data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>
