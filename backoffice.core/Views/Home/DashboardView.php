<?php

// pre($this);die; 
// // // pre($ordersDay);die;
// // $count = 0;
// foreach($ordersDay as $key => $rowOrder){
// //     echo $rowOrder['Marketplace'];
//     echo "<input type='hidden' class='itemsOrders' marketplace='{$rowOrder['Marketplace']}' id='{$count}' day='{$rowOrder['date']}' value='{$rowOrder['totalDay']}' />";
//     $count++;
// }
// // pre($ordersMonth);die; 
$count = 0;
foreach($ordersMonth as $key => $rowMonth){
//     echo $rowMonth['Marketplace']; 
    echo "<input type='hidden' class='ordersMonth' marketplace='{$rowMonth['Marketplace']}' id='{$count}' month='{$rowMonth['year']} {$rowMonth['monthname']}' value='{$rowMonth['totalpedido']}' />";
    $count++;
}

?>
	<!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-aqua">
                <div class="inner">
                  <h3><?php  echo $dashboardModelo->total_orders; ?></h3>
                  <p>Pedidos</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="/Orders/Manage" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-green">
                <div class="inner">
                  <h3><sup style="font-size: 20px">R$ </sup><?php  echo $dashboardModelo->total_revenues; ?></h3>
                  <p>Faturado</p>
                </div>
                <div class="icon">
                  <i class="ion ion-cash"></i>
                </div>
                <a href="/Orders/Manage" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-yellow">
                <div class="inner">
                  <h3><?php  echo $dashboardModelo->total_customers; ?></h3>
                  <p>Clientes</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
                <a href="/Customer/ManageCustomers" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-red">
                <div class="inner">
                  <h3><?php  echo $dashboardModelo->total_products; ?></h3>
                  <p>Produtos</p>
                </div>
                <div class="icon">
                  <i class="ion ion-cube"></i>
                </div>
                <a href="/Products/AvailableProducts" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
          </div><!-- /.row -->
          
          <?php  if($this->userdata['email']  != 'casebre@casebredecoracoes.com.br'){ ?>
          <!-- Main row -->
          <div class="row">
          
            <!-- Left col -->
            <section class="col-lg-7 connectedSortable">
            
            
              <!-- Custom tabs (Charts with tabs)-->
              <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs pull-right">
                  <li class="active"><a href="#revenue-chart" data-toggle="tab">Area</a></li>
                  <li class="pull-left header"><i class="fa fa-inbox"></i> Vendas</li>
                </ul>
                <div class="tab-content no-padding">
                  <!-- Morris chart - Sales -->
                  <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>
                </div>
              </div><!-- /.nav-tabs-custom -->
            <!-- Log box -->
              <div class="box box-success">
                <div class="box-header">
                  <i class="fa  fa-info-circle"></i>
                  <h3 class="box-title">Sincronização <?php echo date("d/m/Y"); ?></h3>
                </div>
                <div class="box-body scroll">
                  <?php $dashboardModelo->getLogSync();?>	
                <div class="box-footer">
                  </div>
                </div>
              </div><!-- /.box (chat box) -->
              
               <!-- PRODUCT LIST -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Documentação Recente</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <ul class="products-list product-list-in-box">
                  
                  
                  <?php foreach ($listLastDocuments as $fetch){
	             
                      
                      $title = $fetch['module']." ".$fetch['type']." ".$fetch['title'];
                      $color = $fetch['status'] == 'Desenvolvimento' ? 'primary' : 'success' ;
	                 
                    echo "<li class='item'>
                      <div >
                        <a href='' class='product-title'>{$title}<span class='label label-{$color} pull-right'>{$fetch['status']}</span></a>
                        <span class='product-description'>
                          {$fetch['description']}
                        </span>
                      </div>
                    </li>";
                    
                  } ?>
                  </ul>
                </div><!-- /.box-body -->
                <div class="box-footer text-center">
                  <a href="javascript::;" class="uppercase">Veja toda documentação</a>
                </div><!-- /.box-footer -->
              </div><!-- /.box -->

              
              
             

            </section><!-- /.Left col -->
            <!-- right col (We are only adding the ID to make the widgets sortable)-->
            <section class="col-lg-5 connectedSortable">


              <!-- solid sales graph -->
              <div class="box box-solid bg-teal-gradient">
                <div class="box-header">
                  <i class="fa fa-th"></i>
                  <h3 class="box-title">Desenpenho</h3>
                  <div class="box-tools pull-right">
                    <button class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body border-radius-none">
                  <div class="chart" id="line-chart" style="height: 250px;"></div>
                </div><!-- /.box-body -->
                <div class="box-footer no-border">
                  <div class="row">
                    
                    <?php 
                    
                    foreach($result as $key => $total){
                    	echo "<div class='col-xs-4 text-center' style='border-right: 1px solid #f4f4f4'>
                    	<input type='text' class='knob' data-readonly='true' value='{$total}' data-width='60' data-height='60' data-fgColor='#39CCCC'>
                    	<div class='knob-label'>{$key}</div>
                    	</div>";
                    }
                    
                    
                    ?>
                    
                  </div><!-- /.row -->
                </div><!-- /.box-footer -->
              </div><!-- /.box -->


            </section><!-- right col -->
            
            <section class='col-lg-5'>
              
             
              
             
              <!-- PRODUCT LIST -->
              
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">5 Mais Vendidos Últimos 7 Dias</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <ul class="products-list product-list-in-box">
                  
                  <?php 
//                   pre($listBestSellers);die;
                  $count = 1;
                  foreach ($listBestSellers57 as $fetch){
                      
                    $title = $fetch['title'];
                    $fat = $fetch['fat'] > 0 ? $fetch['fat'] : 1 ;
					$tktMedio =  $fat / $fetch['qtd'];
// 					$urlImages = getUrlImageFromId($this->db, $this->storedata['id'], $fetch['id']);
	                    echo "<li class='item'>
	                      <div class='product-img'>
	                        <img src='{$fetch['thumbnail']}' width='60px' height='60px' alt='Product Image'>
	                      </div>
	                      <div class='product-info'>
	                        <a href='/Products/Product/{$fetch['id']}' class='product-title' target='_blank'> SKU: {$fetch['SKU']} / {$fetch['color']} / {$fetch['variation']}</a><span class='badge bg-green pull-right'>{$fetch['qtd']}</span>
	                        <span class='product-description'>
	                          {$title}
	                        </span>
	                      </div>
	                    </li>";
	                    
	                    $count++;
                    } 
	               ?>
                  </ul>
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">5 Mais Vendidos Últimos 30 Dias</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <ul class="products-list product-list-in-box">
                  
                  <?php 
//                   pre($listBestSellers);die;
                  $count = 1;
                  foreach ($listBestSellers530 as $fetch){
                      
                    $title = $fetch['title'];
                    $fat = $fetch['fat'] > 0 ? $fetch['fat'] : 1 ;
					$tktMedio =  $fat / $fetch['qtd'];
// 					$urlImages = getUrlImageFromId($this->db, $this->storedata['id'], $fetch['id']);
	                    echo "<li class='item'>
	                      <div class='product-img'>
	                        <img src='{$fetch['thumbnail']}' width='60px' height='60px' alt='Product Image'>
	                      </div>
	                      <div class='product-info'>
	                        <a href='/Products/Product/{$fetch['id']}' class='product-title' target='_blank'> SKU: {$fetch['SKU']} / {$fetch['color']} / {$fetch['variation']}</a><span class='badge bg-green pull-right'>{$fetch['qtd']}</span>
	                        <span class='product-description'>
	                          {$title}
	                        </span>
	                      </div>
	                    </li>";
	                    
	                    $count++;
                    } 
	               ?>
                  </ul>
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
              
               <!-- PRODUCT LIST -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">10 Mais Vendidos Últimos 90 Dias</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <ul class="products-list product-list-in-box">
                  
                  <?php 
//                   pre($listBestSellers);die;
                  $count = 1;
                  foreach ($listBestSellers as $fetch){
                      
                    $title = $fetch['title'];
                    $fat = $fetch['fat'] > 0 ? $fetch['fat'] : 1 ;
					$tktMedio =  $fat / $fetch['qtd'];
// 					$urlImages = getUrlImageFromId($this->db, $this->storedata['id'], $fetch['id']);
	                    echo "<li class='item'>
	                      <div class='product-img'>
	                        <img src='{$fetch['thumbnail']}' width='60px' height='60px' alt='Product Image'>
	                      </div>
	                      <div class='product-info'>
	                        <a href='/Products/Product/{$fetch['id']}' class='product-title' target='_blank'> SKU: {$fetch['SKU']} / {$fetch['color']} / {$fetch['variation']}</a><span class='badge bg-green pull-right'>{$fetch['qtd']}</span>
	                        <span class='product-description'>
	                          {$title}
	                        </span>
	                      </div>
	                    </li>";
	                    
	                    $count++;
                    } 
	               ?>
                  </ul>
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
              
            
            
            </section>
            
          </div><!-- /.row (main row) -->
          <?php }?>

