<form action='' method='POST'>
	<div class="box <?php echo $this->box['form']['model']; ?>">
		<div class="box-header with-border">
			<h3 class="box-title"><?php  echo $this->title; ?></h3>
			<div class="box-tools pull-right">
            	<span class="btn btn-box-tool" data-widget="collapse" tabIndex="-1"><i class="fa <?php echo $this->box['form']['icon']; ?>" tabIndex="-1"></i></span>
          </div>
		</div>
		<div class="box-body">

			<div class="form-group">
			<?php 
			$selectedReceivables = "";
			$selectedReceivablesOrder = "";
			    switch($reportOrderModel->report_model){
			        case "Receivables": $selectedReceivables = "Selected";break;
			        case "ReceivablesOrder": $selectedReceivablesOrder = "Selected";break;
			        
                    
                }
			
                ?>
           		<label>Selecione o modelo de relatório</label>
            	<select class="form-control input-sm" name='report_model' style="width: 100%;" tabindex="1">
	            	<option value='Receivables' <?php echo $selectedReceivables; ?> >1 - Produtos a Receber</option>
	            	<option value='ReceivablesOrder'  <?php echo $selectedReceivablesOrder; ?> >2 - Produtos por Pedido </option>
                </select>
                
            </div>
            
            <div class="row">
           		<div class="form-group">
	                <div class="col-xs-5">
	                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='description' <?php echo $reportOrderModel->report == 'description' ? "checked" : "" ; ?> > Descrição</label>
	                </div>
	                <div class="col-xs-7">
	                    <input type="text" class="form-control input-sm" name='description' tabindex="2" value='<?php echo $reportOrderModel->description;?>' >
	                </div>
                </div>
            </div>
            
            <div class="row">
           		<div class="form-group">
	                <div class="col-xs-5">
            			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='reference' <?php echo $reportOrderModel->report == 'reference' ? "checked" : "" ; ?> > Referência</label>
            		</div>
	                <div class="col-xs-7">
                		<input type="text" class="form-control input-sm" name='reference' tabindex="3" value='<?php echo $reportOrderModel->reference;?>'>
	                </div>
                </div>
            </div>
            
            <div class="row">
           		<div class="form-group">
	                <div class="col-xs-5">
            			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='color' <?php echo $reportOrderModel->report == 'color' ? "checked" : "" ; ?> > Cor</label>
					</div>
	                <div class="col-xs-7">
                		<input type="text" class="form-control input-sm" name='color' tabindex="4" value='<?php echo $reportOrderModel->color;?>'>
	                </div>
                </div>
            </div>
            
            <div class="row">
           		<div class="form-group">
	                <div class="col-xs-5">
            			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='brand' <?php echo $reportOrderModel->report == 'brand' ? "checked" : "" ; ?> > Marca</label>
            		</div>
	                <div class="col-xs-7">
	                	<input type="hidden"  id='brand_id'  name='brand_id' value='<?php echo $reportOrderModel->brand_id; ?>'>
                		<input type="text" class="form-control input-sm autocomplete-products" id='brand'  name='brand' tabindex="5" value='<?php echo $reportOrderModel->brand; ?>'>
	                </div>
                </div>
            </div>
            
            <div class="row">
           		<div class="form-group">
	                <div class="col-xs-5">
            			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='provider' <?php echo $reportOrderModel->report == 'provider' ? "checked" : "" ; ?> > Fornecedor</label>
            		</div>
	                <div class="col-xs-7">
	                	<input type="hidden"  id='provider_id'  name='provider_id' value='<?php echo $reportOrderModel->provider_id; ?>'>
                   		<input type="text" class="form-control input-sm autocomplete-products" id='provider' name='provider' tabindex="6" value='<?php echo $reportOrderModel->provider_id; ?>'>
	                </div>
                </div>
            </div>
            
            <div class="row">
           		<div class="form-group">
	                <div class="col-xs-5">
            			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='department' <?php echo $reportOrderModel->report == 'department' ? "checked" : "" ; ?> > Depart.</label>
            		</div>
	                <div class="col-xs-7">
	               		<input type="hidden"  id='department_id'  name='department_id' value='<?php echo $reportOrderModel->department_id; ?>'>
                		<input type="text" class="form-control input-sm autocomplete-products" id='department'  name='department' tabindex="7" value='<?php echo $reportOrderModel->department; ?>'>
	                </div>
                </div>
            </div>
            
            <div class="row">
           		<div class="form-group">
	                <div class="col-xs-5">
	                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='grid' <?php echo $reportOrderModel->report == 'grid' ? "checked" : "" ; ?> > Grade</label>
            		</div>
	                <div class="col-xs-7">
	                	<input type="hidden"  id='grid_id'  name='grid_id' value='<?php echo $reportOrderModel->grid_id; ?>'>
	                    <input type="text" class="form-control input-sm autocomplete-products" id='grid' name='grid' tabindex="8" value='<?php echo $reportOrderModel->grid; ?>'>
	                </div>
                </div>
            </div>
            
            <div class="row">
           		<div class="form-group">
	                <div class="col-xs-5">
	                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='collection' <?php echo $reportOrderModel->report == 'collection' ? "checked" : "" ; ?> > Coleção</label>
            		</div>
	                <div class="col-xs-7">
	                	<input type="text" class="form-control input-sm autocomplete-products" id='collection' name='collection' tabindex="9" value='<?php echo $reportOrderModel->collection;?>'>
	                </div>
                </div>
            </div>
            
            <div class="row">
           		<div class="form-group">
	                <div class="col-xs-5">
	                	<label><input type="radio" class="flat-red " name='report' tabIndex="-1" value='company' <?php echo $reportOrderModel->report == 'company' ? "checked" : "" ; ?> > Empresa</label>
            		</div>
	                <div class="col-xs-7">
	                    <input type="text" class="form-control input-sm autocomplete-products" id='company' name='company' tabindex="10" value='<?php echo $reportOrderModel->company;?>'>
	                </div>
                </div>
            </div>
            
            
            <div class="row">
            	<div class="form-group">
            		<div class="col-xs-5" >
	                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='date_add' <?php echo $reportOrderModel->report == 'date_add' ? "checked" : "" ; ?> > Data do Pedido</label>
	                </div>
	                <div class="col-xs-4" style='width:29.16%;padding-right:0px;'>
						<input type="date" class="form-control input-sm" name='date_add' style='width:100%;padding-left:0px;padding-right:0px;' tabindex="13"  value='<?php echo $reportOrderModel->date_add;?>'>
					</div>
					<div class="col-xs-4" style='width:29.16%;padding-left:0px;'>
						<input type="date" class="form-control input-sm" name='date_add_end' style='width:100%;padding-left:0px;padding-right:0px;' tabindex="14" value='<?php echo $reportOrderModel->date_add_end;?>'>
					</div>
				</div>
			</div>
			
			<div class="row">
            	<div class="form-group">
            		<div class="col-xs-5">
	                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='date_last' <?php echo $reportOrderModel->report == 'date_last' ? "checked" : "" ; ?> > Data de Previsão</label>
	                </div>
	                <div class="col-xs-4"  style='width:29.16%;padding-right:0px;'>
						<input type="date" class="form-control input-sm" name='date_last' style='width:100%;padding-left:0px;padding-right:0px;' tabindex="15" value='<?php echo $reportOrderModel->date_last;?>'>
					</div>
					<div class="col-xs-4"  style='width:29.16%;padding-left:0px;'>
						<input type="date" class="form-control input-sm"  name='date_last_end' style='width:100%;padding-left:0px;padding-right:0px;' tabindex="16" value='<?php echo $reportOrderModel->date_last_end;?>'>
					</div>
				</div>
			</div>
			              

		</div>
		<div class="box-footer">
            <button type="submit" name='preview' class="btn btn-info pull-right submit" tabindex="19">Visualizar</button>
       	</div><!-- /.box-footer -->
       	
	</div>
</form>