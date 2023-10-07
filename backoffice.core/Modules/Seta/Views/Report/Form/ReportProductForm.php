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
               		<label>Selecione o modelo de relatório</label>
                	<select class="form-control input-sm" name='report_model' style="width: 100%;" tabindex="1">
		            	<option value='StockPrice'>1 - Listagem de Preços e Estoque</option>
		                <option value='StockGrid'>2 - Estoque dos Produtos por Grade</option>
		                <option value='ResumeStockPrice'>3 - Resumo de Preços e Estoque</option>
		                <option value='StockGridStore'>4 - Estoque dos Produtos por Grade e Empresa</option>
		                <option value='ResumePromotionStockPrice'>5 - Resumo de Preços e Estoque em Promoção</option>
		                <option value='ResumeStorePromotionStockPriceGrid'>6 - Resumo de Preços e Estoque em Promoção com Grade e Filial</option>
                    </select>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
		                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='description' <?php echo $reportProductModel->report == 'description' ? "checked" : "" ; ?> > Descrição</label>
		                </div>
		                <div class="col-xs-7">
		                    <input type="text" class="form-control input-sm" name='description' tabindex="2" value='<?php echo $reportProductModel->description;?>' >
		                </div>
	                </div>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
                			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='reference' <?php echo $reportProductModel->report == 'reference' ? "checked" : "" ; ?> > Referência</label>
                		</div>
		                <div class="col-xs-7">
                    		<input type="text" class="form-control input-sm" name='reference' tabindex="3" value='<?php echo $reportProductModel->reference;?>'>
		                </div>
	                </div>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
                			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='color' <?php echo $reportProductModel->report == 'color' ? "checked" : "" ; ?> > Cor</label>
						</div>
		                <div class="col-xs-7">
                    		<input type="text" class="form-control input-sm" name='color' tabindex="4" value='<?php echo $reportProductModel->color;?>'>
		                </div>
	                </div>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
                			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='brand' <?php echo $reportProductModel->report == 'brand' ? "checked" : "" ; ?> > Marca</label>
                		</div>
		                <div class="col-xs-7">
		                	<input type="hidden"  id='brand_id'  name='brand_id' value='<?php echo $reportProductModel->brand_id; ?>'>
                    		<input type="text" class="form-control input-sm autocomplete-products" id='brand'  name='brand' tabindex="5" value='<?php echo $reportProductModel->brand; ?>'>
		                </div>
	                </div>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
                			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='provider' <?php echo $reportProductModel->report == 'provider' ? "checked" : "" ; ?> > Fornecedor</label>
                		</div>
		                <div class="col-xs-7">
		                	<input type="hidden"  id='provider_id'  name='provider_id' value='<?php echo $reportProductModel->provider_id; ?>'>
	                   		<input type="text" class="form-control input-sm autocomplete-products" id='provider' name='provider' tabindex="6" value='<?php echo $reportProductModel->provider_id; ?>'>
		                </div>
	                </div>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
                			<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='department' <?php echo $reportProductModel->report == 'department' ? "checked" : "" ; ?> > Depart.</label>
                		</div>
		                <div class="col-xs-7">
		               		<input type="hidden"  id='department_id'  name='department_id' value='<?php echo $reportProductModel->department_id; ?>'>
                    		<input type="text" class="form-control input-sm autocomplete-products" id='department'  name='department' tabindex="7" value='<?php echo $reportProductModel->department; ?>'>
		                </div>
	                </div>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
		                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='grid' <?php echo $reportProductModel->report == 'grid' ? "checked" : "" ; ?> > Grade</label>
                		</div>
		                <div class="col-xs-7">
		                	<input type="hidden"  id='grid_id'  name='grid_id' value='<?php echo $reportProductModel->grid_id; ?>'>
		                    <input type="text" class="form-control input-sm autocomplete-products" id='grid' name='grid' tabindex="8" value='<?php echo $reportProductModel->grid; ?>'>
		                </div>
	                </div>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
		                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='collection' <?php echo $reportProductModel->report == 'collection' ? "checked" : "" ; ?> > Coleção</label>
                		</div>
		                <div class="col-xs-7">
		                	<input type="text" class="form-control input-sm autocomplete-products" id='collection' name='collection' tabindex="9" value='<?php echo $reportProductModel->collection;?>'>
		                </div>
	                </div>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
		                	<label><input type="radio" class="flat-red " name='report' tabIndex="-1" value='company' <?php echo $reportProductModel->report == 'company' ? "checked" : "" ; ?> > Empresa</label>
                		</div>
		                <div class="col-xs-7">
		                    <input type="text" class="form-control input-sm autocomplete-products" id='company' name='company' tabindex="10" value='<?php echo $reportProductModel->company;?>'>
		                </div>
	                </div>
                </div>
                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
		                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='turning_status' <?php echo $reportProductModel->report == 'turning_status' ? "checked" : "" ; ?> > Status Giro</label>
		                </div>
		                <div class="col-xs-7">
		                    <input type="text" class="form-control input-sm" name='turning_status' tabindex="12" value='<?php echo $reportProductModel->turning_status;?>'>
		                </div>
	                </div>
                </div>
                
                
                <div class="row">
                	<div class="form-group">
                		<div class="col-xs-5" >
		                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='date_add' <?php echo $reportProductModel->report == 'date_add' ? "checked" : "" ; ?> > Cadastro</label>
		                </div>
		                <div class="col-xs-4" style='width:29.16%;padding-right:0px;'>
							<input type="date" class="form-control input-sm" name='date_add' style='width:100%;padding-left:0px;padding-right:0px;' tabindex="13"  value='<?php echo $reportProductModel->date_add;?>'>
						</div>
						<div class="col-xs-4" style='width:29.16%;padding-left:0px;'>
							<input type="date" class="form-control input-sm" name='date_add_end' style='width:100%;padding-left:0px;padding-right:0px;' tabindex="14" value='<?php echo $reportProductModel->date_add_end;?>'>
						</div>
					</div>
				</div>
				
				<div class="row">
                	<div class="form-group">
                		<div class="col-xs-5">
		                	<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='date_last' <?php echo $reportProductModel->report == 'date_last' ? "checked" : "" ; ?> > Última Compra</label>
		                </div>
		                <div class="col-xs-4"  style='width:29.16%;padding-right:0px;'>
							<input type="date" class="form-control input-sm" name='date_last' style='width:100%;padding-left:0px;padding-right:0px;' tabindex="15" value='<?php echo $reportProductModel->date_last;?>'>
						</div>
						<div class="col-xs-4"  style='width:29.16%;padding-left:0px;'>
							<input type="date" class="form-control input-sm"  name='date_last_end' style='width:100%;padding-left:0px;padding-right:0px;' tabindex="16" value='<?php echo $reportProductModel->date_last_end;?>'>
						</div>
					</div>
				</div>
				
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
               				<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='stock_position' <?php echo $reportProductModel->report == 'stock_position' ? "checked" : "" ; ?> > Posição</label>
		                </div>
		                <div class="col-xs-7">               				
		                	<select class="form-control input-sm" name='stock_position' style="width: 100%;" tabindex="17">
				            	<option value='0'>Todos</option>
				                <option value='1' selected>Com Estoque</option>
				                <option value='2'>Sem Estoque</option>
				                <option value='3'>Estoque Negativo</option>
		                    </select>
		                </div>
	                </div>
                </div>                
                <div class="row">
               		<div class="form-group">
		                <div class="col-xs-5">
		               		<label><input type="radio" class="flat-red" name='report' tabIndex="-1" value='status' <?php echo $reportProductModel->report == 'status' ? "checked" : "" ; ?> > Status</label>
		                </div>
		                <div class="col-xs-7">   		               		
		                	<select class="form-control input-sm" name='status' style="width: 100%;" tabindex="18">
				            	<option value='False'>Ativo</option>
				                <option value='True'>Inativo</option>
		                    </select>
		                </div>
	                </div>
                </div> 
			</div>
			<div class="box-footer">
                <button type="submit" name='preview' class="btn btn-info pull-right submit" tabindex="19">Visualizar</button>
           	</div><!-- /.box-footer -->
           	
		</div>
	</form>