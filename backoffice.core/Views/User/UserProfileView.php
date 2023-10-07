<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
		
		</div>
		
		<div class="box box-primary">
			<form role="form" method="POST" action="" name="form-profile">
			<input type="hidden" name="user_id" value="" />
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-xs-5">		
						<div class="form-group">
							<label for="name">Nome completo:</label> 
							<input type="text" name="name" id="name" class="form-control name" placeholder="Nome" value="" />
						</div>
					</div>
					<div class="col-xs-3">
						<div class="form-group">
							<label for="name">CEP:</label> 
							<input type="text" name="postalcode" id="postalcode" class="form-control postalcode" placeholder="CEP" value="" />
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<label for="number">Nº:</label> 
							<input type="text" name="number" id="number" class="form-control nnumberame" placeholder="Número" value="" />
						</div>
					</div>
					<div class="col-xs-3">
						<div class="form-group">
							<label for="neighborhood">Bairro:</label> 
							<input type="text" name="neighborhood" id="neighborhood" class="form-control neighborhood" placeholder="Bairro" value="" />
						</div>
					</div>
					<div class="col-xs-5">
						<div class="form-group">
							<label for="address">Endereço:</label> 
							<input type="text" name="address" id="address" class="form-control address" placeholder="Endereço" value="" />
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<label for="state">Estado:</label> 
							<input type="text" name="state" id="state" class="form-control state" placeholder="Estado" value="" />
						</div>
					</div>
					<div class="col-xs-3">
						<div class="form-group">
							<label for="city">Cidade:</label> 
							<input  type="text" name="city" id="city" class="form-control city" placeholder="Cidade" value="" />
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-info pull-right" id="save" name="save">Salvar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>