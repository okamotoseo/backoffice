<?php
header("Content-Type: text/html; charset=utf-8");

define( 'HOME_URI', 'https://'.$_SERVER['HTTP_HOST']);

//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Models/Shipping/PickingModel.php';
require_once $path .'/../Models/Orders/ManageOrdersModel.php';
require_once $path .'/../Functions/global-functions.php';
include($path .'/../library/BarcodeD/src/BarcodeGenerator.php');
include($path .'/../library/BarcodeD/src/BarcodeGeneratorPNG.php');

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? $_REQUEST["store_id"] : null ;
$orderId = isset($_REQUEST["order_id"]) && $_REQUEST["order_id"] != "" ? $_REQUEST["order_id"] : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;


if (empty ( $action ) and empty ( $storeId )) {
	if(isset($_SERVER ['argv'] [1])){
		$paramAction = explode ( "=", $_SERVER ['argv'] [1] );
		$action = $paramAction [0] == "action" ? $paramAction [1] : null;
	}
	if(isset($_SERVER ['argv'] [2])){
		$paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
		$storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
	}
	if(isset($_SERVER ['argv'] [3])){
		$paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
		$accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
	}

	$request = "System";
}
if(isset($storeId)){
    
    $db = new DbConnection();
    
    switch($action){
        
        case "add_order_document":
            
            $PedidoId = isset($_REQUEST["pedido_id"]) && $_REQUEST["pedido_id"] != "" ? $_REQUEST["pedido_id"] : null ;
            
            $pickingId = isset($_REQUEST["picking_id"]) && $_REQUEST["picking_id"] != "" ? $_REQUEST["picking_id"] : '' ;
            
            if( !empty($pickingId) ){
                
                if(isset($PedidoId) AND !empty($PedidoId)){
                    
                    $sql = "SELECT * FROM picking_product_orders WHERE store_id = {$storeId}
                    AND picking_id = {$pickingId} AND PedidoId LIKE '{$PedidoId}' ORDER BY quantity ASC LIMIT 1";
                    $query = $db->query($sql);
                    
                    $pickingOrders = $query->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($pickingOrders['order_id'])){
                        
                        $ordersModel = new ManageOrdersModel($db);
                        
                        $ordersModel->store_id = $storeId;
                        
                        $ordersModel->id = $pickingOrders['order_id'];
                        
                        $orders = $ordersModel->GetOrderDetails();
                        
                        $order = $orders[0];
                        //                             pre($orders);die;
//                         $dataEmissao = str_replace('-', '/', $order['nf_emissao']);
                        $dataEmissao = dateTimeBr($order['nf_emissao'],'/');
                        $obsParts = explode(';Vendedor',  $order['nf_info_fisco']);
                        $htmlOrder = '';
                        $htmlOrder .= "<div class='boxes usuario table-danfe-etiqueta' >
                                <div style='background: #f5f5f5; border-bottom: 1px solid #000;'>
                                <h4 style='margin-bottom:0px;margin-left:10px;display:inline-block;margin-top: 0px;'>DANFE - Simplificado Etiqueta</h4>
                            </div>
                            
							<table class='table table-danfe-etiqueta' cellpadding='1' cellspacing='0'  width='100%' style='margin-bottom:0px;' >
								<tbody>
                                    <tr>
										<td width='25%'>
                                            <img src='https://backoffice.sysplace.com.br/Views/_uploads/images/store/160x160/4.png' style='max-width:100px;max-height:100px'>
                                        </td>
										<td colspan='3'  width='75%'>
                                            Fanlux Comércio de Produtos Elétricos<br>
                                            Rua Tenente Antonio João, 215<br>
                                            Marília/SP CNPJ: 24.973.647/0001-68<br>
                                            <i class='fa fa-whatsapp'></i> (14)9990-1232
                                            <i class='fa fa-phone'></i> (14)3434-1410
                                            
                                        </td>
									</tr>
									<tr>
										<td colspan='2'><b>N. Nota: </b> {$order['nota_numero']}</td>
                                        <td width='20%'><b>Serie: </b> {$order['nf_serie']}</td>
                                        <td><b>Tipo:</b> {$order['nf_tipo']} Saída</td>
                                    </tr>";
                        if(!empty( $order['fiscal_key'])){
                            $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                            
                            $htmlOrder .= "<tr><td colspan='4' style='text-align: center;'><strong>Chave de acesso:</strong><br>";
                            $htmlOrder .= '<img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($order['fiscal_key'], $generatorPNG::TYPE_CODE_128_C, 1, 40)) . '"><br>'.$order['fiscal_key'].'</td></tr>';
//                             $htmlOrder .= "<tr><td colspan='4'><strong>Chave de acesso:</strong><br>
// 									    <img alt='Código de Barras' src='/library/Barcode/barcode.php?codetype=code128&size=50&text=".$order['fiscal_key']."&print=true' width='100%'/></td></tr>";
//                             $htmlOrder .= "<tr><td colspan='4'><strong>Chave de acesso:</strong><br>
// 									    <img alt='Código de Barras' src='/library/Barcode/barcode.php?codetype=code128&size=50&text=".$order['fiscal_key']."&print=true' width='100%'/></td></tr>";
                        }
                        $htmlOrder .= "<tr>
                                        <td><b class='pull-right'>Emissao:</b></td><td><span class='pull-left'>{$dataEmissao}</span></td>
                                        <td><b class='pull-right'>Hora:</b></td><td><span class='pull-left'>{$order['nf_hora_emissao']}</span></td>
                                    </tr>
                                </tbody>
                            </table>
							<div style='background: #f5f5f5; border-bottom: 1px solid #000; margin-top:0px;'>
								<h5 style='margin-bottom:0px;margin-left:10px;margin-top: 0px;'>DESTINATÁRIO</h5>
							</div>
							<table class='table-danfe-etiqueta' cellpadding='1' cellspacing='0'  width='100%'>
								<tbody>
									<tr>
										<td><strong>CPF/CNPJ:</strong></td>
										<td>{$order['customer']['CPFCNPJ']}</td>
									</tr>
                                    <tr>
										<td><strong>Nome:</strong></td>
										<td>{$order['Nome']}</td>
									</tr>
									<tr>
										<td><strong>Endereço:</strong></td>
										<td>{$order['Endereco']}, {$order['Numero']} {$order['Complemento']}</td>
							        </tr>";
                        
                                    if(!empty($order['Bairro'])){
                                        $htmlOrder .= "<tr>
									       	    <td><strong>Bairro:</strong></td>
							                    <td>{$order['Bairro']}</td>
							                 </tr>";
                                    }

							        $htmlOrder .= "<tr>
										<td><strong>Cidade/UF:</strong></td>
							            <td>{$order['Cidade']} - {$order['Estado']}</td>
							        </tr>
							        <tr>
										<td><strong>CEP:</strong></td>
							            <td>{$order['Cep']}</td>
							        </tr>
								</tbody>
							</table>
                            <div class='box-footer'>
                                <div class='row text-muted well well-sm no-shadow'><small><b>Obs Fiscal:</b> {$obsParts[0]}</small></div>
                            </div>
						</div>";
                        
                        $qtdItens = 0;
                        $sumQtdItens = 0;
                        $htmlitem = '';
                        foreach($order['items'] as $i => $item){
                            
                            $sumQtdItens += $item['Quantidade'];
//                          $styleClassTotal ='btn btn-warning';
                            $styleClassTotal ='btn bg-olive';
                            
                            if($sku == $item['SKU']){
                                
                                $styleClass = "btn bg-olive";
                                if($item['Quantidade'] == 1){
                                    $styleClass = "btn bg-olive";
                                }else{
                                    $styleClass = "btn btn-warning";
                                }
                                $added = 1;
                                
                            }else{
                                $styleClass = "btn waiting-pickup"; //waiting-pickup
                                $added = 0;
                            }
                            $htmlitem .= "<tr class='tr-order tr-order-{$pickingOrders['order_id']}' id='tr-{$item['SKU']}' ean='{$item['ean']}' sku='{$item['SKU']}'>
                            
                                            <td width='15%' ><img src='{$item['UrlImagem']}' width='50' /></td>
                    					    <td width='15%' ><b id='item-sku-{$item['SKU']}'>{$item['SKU']}</b></td>
                    					   <td width='64%' >{$item['Nome']}<br>";
                            foreach($item['item_attributes'] as $keyItemAttr =>$attr){
                                $htmlitem .= "<small text-muted>{$attr['Nome']}  - {$attr['Valor']} </small>";
                            }
                            
                            $htmlitem .= "
                					</td>
                					
                                    <td align='center' valign='center' width='3%' ><h3><span class='qtd_added {$styleClass}' id='{$item['SKU']}-qtd_added'>{$added}</span></h3></td>
                                    <td align='center' valign='center' width='3%' ><h3><span class='qtd_total {$styleClassTotal}' id='{$item['SKU']}-qtd_total'>{$item['Quantidade']}</span></h3></td>
                				</tr>";
                            $qtdItens++;
                            
                        }
                        echo "success|{$order['PedidoId']}|{$htmlOrder}|{$qtdItens}|{$htmlitem}|{$sumQtdItens}";
                        
                        
                    }else{
                        
                        echo "error|Pedido Não Localizado!...";
                        
                    }
                    
                    
                }
                
            }else{
                echo "error|Não Foi Possível Localizar o Pedido do Produto Informado!...";
            }
            
            
            break;
        case "add_product_package":
            
            $PedidoId = isset($_REQUEST["pedido_id"]) && $_REQUEST["pedido_id"] != "" ? $_REQUEST["pedido_id"] : null ;
            
            $code = isset($_REQUEST["code"]) && $_REQUEST["code"] != "" ? $_REQUEST["code"] : '' ;
            
            $pickingId = isset($_REQUEST["picking_id"]) && $_REQUEST["picking_id"] != "" ? $_REQUEST["picking_id"] : '' ;
            
            if( !empty($pickingId) and !empty($code) ){
                
                $sku = '';
                
                $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND ean LIKE '{$code}'";
                $query = $db->query($sql);
                $product = $query->fetch(PDO::FETCH_ASSOC);
                $sku = isset($product['sku']) ? $product['sku'] : '' ;
                
                if(empty($sku)){
                    $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$code}'";
                    $query = $db->query($sql);
                    $product = $query->fetch(PDO::FETCH_ASSOC);
                    $sku = isset($product['sku']) ? $product['sku'] : '' ;
                }
                
                
                if(!empty($sku)){
                    
                    if(isset($PedidoId) AND !empty($PedidoId)){
                        
                        
                    }else{
                    
                        $sql = "SELECT * FROM picking_product_orders WHERE store_id = {$storeId}
                        AND picking_id = {$pickingId} AND sku LIKE '{$sku}' ORDER BY quantity ASC LIMIT 1";
                        $query = $db->query($sql);
                        $pickingOrders = $query->fetch(PDO::FETCH_ASSOC);
//                         pre($pickingOrders);die;
                        if(isset($pickingOrders['order_id'])){
                            
                            $ordersModel = new ManageOrdersModel($db);
                            
                            $ordersModel->store_id = $storeId;
                            
                            $ordersModel->id = $pickingOrders['order_id'];
                            
                            $orders = $ordersModel->GetOrderDetails();
                            
                            $order = $orders[0];
                            
//                             pre($orders);die;
//                             $dataEmissao = str_replace('-', '/', $order['nf_emissao']);
                            
                            $dataEmissao = dateTimeBr($order['nf_emissao'], '/');
                            $htmlOrder = '';
                      
                            $htmlOrder .= "<div class='boxes usuario' >
                                    <div style='background: #f5f5f5; border-bottom: 1px solid #000;'>
                                    <h4 style='margin-bottom:0px;margin-left:10px;display:inline-block;'>DANFE - Simplificado Etiqueta</h4>
                                </div>
								
								<table class='table' cellpadding='1' cellspacing='0'  width='100%' style='margin-bottom:0px;' >
									<tbody>
                                        <tr>
											<td width='25%'>
                                                <img src='https://backoffice.sysplace.com.br/Views/_uploads/images/store/160x160/4.png' style='max-width:100px;max-height:100px'>
                                            </td>
											<td colspan='3'  width='75%'>
                                                Fanlux Comércio de Produtos Elétricos<br>
                                                Rua Tenente Antônio João, 215<br>
                                                Marília/SP CNPJ: 24.973.647/0001-68<br>
                                                <i class='fa fa-whatsapp'></i> (14)9990-1232                                                
                                                <i class='fa fa-phone'></i> (14)3434-1410
                                            </td>
										</tr>
										<tr>
											<td colspan='2'><b>N. Nota: </b>{$order['id_nota_saida']}</td>
                                            <td width='20%'><b>Serie: </b> {$order['nf_serie']}</td>
                                            <td><b>Tipo:</b> {$order['nf_tipo']} Saída</td>

                                        </tr>";
    									if(!empty( $order['fiscal_key'])){
    									    $htmlOrder .= "<tr><td colspan='4'><p><strong>Chave de acesso:</strong>
    									    <img alt='Código de Barras' src='/library/Barcode/barcode.php?codetype=code128&size=40&text=".$order['fiscal_key']."&print=true' width='100%'/></p></td></tr>";
    									}
									$htmlOrder .= "<tr>
                                            <td><b class='pull-right'>Emissao:</b></td><td><span class='pull-left'>{$dataEmissao}</span></td>
                                            <td><b class='pull-right'>Hora Emissão:</b></td><td><span class='pull-left'>{$order['nf_hora_emissao']}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
								<div style='background: #f5f5f5; border-bottom: 1px solid #000;'>
									<h4 style='margin-bottom:0px;margin-left:10px;'>DESTINATÁRIO</h4>
								</div>
								<table cellpadding='1' cellspacing='0'  width='100%'>
									<tbody>
										<tr>
											<td><strong>Nome:</strong></td>
											<td>{$order['Nome']}</td>
										</tr>
										<tr>
											<td><strong>Endereço:</strong></td>
											<td>{$order['Endereco']}, {$order['Numero']} {$order['Complemento']}</td>
								        </tr>
								        <tr>
											<td><strong>Bairro:</strong></td>
								            <td>{$order['Bairro']}</td>
								        </tr>
								        <tr>
											<td><strong>Cidade/UF:</strong></td>
								            <td>{$order['Cidade']} - {$order['Estado']}</td>
								        </tr>
								        <tr>
											<td><strong>CEP:</strong></td>
								            <td>{$order['Cep']}</td>
								        </tr>
								        <tr>
											<td><strong>CPF/CNPJ:</strong></td>
											<td>{$order['customer']['CPFCNPJ']}</td>
										</tr>
									</tbody>
								</table>
                                <div class='box-footer'>
                                    <div class='row text-muted well well-sm no-shadow'><small><b>Obs Fiscal:</b> {$order['nf_info_fisco']}</small></div>
                                </div>
							</div>";
								
						    $qtdItens = 0;
						    $sumQtdItens = 0;
							$htmlitem = '';
                            foreach($order['items'] as $i => $item){
                                
                                $sumQtdItens += $item['Quantidade'];
                                
//                                 $styleClassTotal ='btn btn-warning';
                                $styleClassTotal ='btn bg-olive';
                                
                                if($sku == $item['SKU']){
                                    
                                    $styleClass = "btn bg-olive";
                                    if($item['Quantidade'] == 1){
                                        $styleClass = "btn bg-olive";
                                    }else{
                                        $styleClass = "btn btn-warning";
                                    }
                                    $added = 1;
                                    
                                }else{
                                    $styleClass = "btn waiting-pickup"; //waiting-pickup
                                    $added = 0;
                                }
                				 $htmlitem .= "<tr class='tr-order tr-order-{$pickingOrders['order_id']}' id='tr-{$item['SKU']}' ean='{$item['ean']}' sku='{$item['SKU']}'>
                                                                     					
                                                <td width='15%' ><img src='{$item['UrlImagem']}' width='50' /></td>
                        					    <td width='15%' ><b id='item-sku-{$item['SKU']}'>{$item['SKU']}</b></td>
                        					   <td width='64%' >{$item['Nome']}<br>";
                				            foreach($item['item_attributes'] as $keyItemAttr =>$attr){
                				                $htmlitem .= "<small text-muted>{$attr['Nome']}  - {$attr['Valor']} </small>";
                                            }
                                                        
                                $htmlitem .= "
                    					</td>
                    					
                                        <td align='center' valign='center' width='3%' ><h3><span class='qtd_added {$styleClass}' id='{$item['SKU']}-qtd_added'>{$added}</span></h3></td>
                                        <td align='center' valign='center' width='3%' ><h3><span class='qtd_total {$styleClassTotal}' id='{$item['SKU']}-qtd_total'>{$item['Quantidade']}</span></h3></td>   
                    				</tr>";
                                $qtdItens++;

                            }
                            echo "success|{$order['PedidoId']}|{$htmlOrder}|{$qtdItens}|{$htmlitem}|{$sumQtdItens}";
                            
                            
                        }else{
                            
                            echo "error|Pedido Não Localizado!...";
                            
                        }
                        
                        
                    }
                
                }else{
                    echo "error|Não Foi Possível Localizar o Produto na Separação Selecionada!...";
                }
                
            }else{
                echo "error|Não Foi Possível Localizar o Pedido do Produto Informado!...";
            }
            
            
        break;
    }
    
    
}

