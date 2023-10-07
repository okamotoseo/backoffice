<?php
/**
 * MainModel - Modelo geral
 *
 * @package 
 * @since 0.1
 */
class MainModel
{
     /**
     * $form_data
     *
     * Os dados de formulários de envio.
     *
     * @access public
     */ 
     private $form_data;
     
     /**
     * $form_msg
     *
     * As mensagens de feedback para formulários.
     *
     * @access public
     */ 
     public $form_msg = '';
     
     
     public $field_error = array();
     
     /**
     * $form_confirma
     *
     * Mensagem de confirmação para apagar dados de formulários
     *
     * @access private
     */
     private $form_confirma;
     
     /**
     * $db
     *
     * O objeto da nossa conexão MYSQL PDO
     *
     * @access private
     */
     private $db;
     
     /**
      * $pg
      *
      * O objeto da nossa conexão POSTGRES PDO
      *
      * @access private
      */
     private $pg;
     
     /**
     * $controller
     *
     * O controller que gerou esse modelo
     *
     * @access private
     */
     private $controller;
     
     /**
     * $parametros
     *
     * Parâmetros da URL
     *
     * @access private
     */
     private $parametros;
     
     /**
     * $userdata
     *
     * Dados do usuário
     *
     * @access private
     */
     private $userdata;
     
     /**
      * $modulesdata
      *
      * Dados do usuário
      *
      * @access private
      */
     private $moduledata;
     
     
     public $pagina_atual;
     
     
     public $linha_inicial = 0;
     
     /**
     * Inverte datas 
     *
     * Obtém a data e inverte seu valor.
     * De: d-m-Y H:i:s para Y-m-d H:i:s ou vice-versa.
     *
     * @since 0.1
     * @access public
     * @param string $data A data
     */
     public function inverte_data( $data = null ) {
     
     // Configura uma variável para receber a nova data
         $nova_data = null;
         
         // Se a data for enviada
         if ( $data ) {
         
         // Explode a data por -, /, : ou espaço
             $data = preg_split('/-|/|s|:/', $data);
             
             // Remove os espaços do começo e do fim dos valores
             $data = array_map( 'trim', $data );
             
             // Cria a data invertida
             $nova_data .= chk_array( $data, 2 ) . '-';
             $nova_data .= chk_array( $data, 1 ) . '-';
             $nova_data .= chk_array( $data, 0 );
             
             // Configura a hora
             if ( chk_array( $data, 3 ) ) {
                $nova_data .= ' ' . chk_array( $data, 3 );
             }
             
             // Configura os minutos
             if ( chk_array( $data, 4 ) ) {
                $nova_data .= ':' . chk_array( $data, 4 );
             }
             
             // Configura os segundos
             if ( chk_array( $data, 5 ) ) {
                $nova_data .= ':' . chk_array( $data, 5 );
             }
         }
         
         // Retorna a nova data
         return $nova_data;
     
     } // inverte_data
     
     
     public function logSystem($db, $userId, $productId = null,  $type, $controller, $method, $action, $information){
         $db->insert("log_system", array(
             "store_id" => $this->store_id,
             "user_id" => $userId,
             "product_id" => $productId,
             "type" => $type,
             "controller" => $controller,
             "method" => $method,
             "action" => $action,
             "information" => strip_tags($information)
         ));
         
         
     }
     
     public function productsLog($productId, $description, $dataLog){
     	
     	if(empty($productId)){
     		return ;
     	}
     	if(isset( $this->controller)){
     		$dataLog = isset($dataLog) ? json_encode($dataLog, JSON_PRETTY_PRINT) : null;
	     	$this->db->insert('products_log', array(
	     			'store_id' => $this->controller->userdata['store_id'],
	     			'product_id' => $productId,
	     			'description' => $description,
	     			'user' => $this->controller->userdata['name'],
	     			'created' => date('Y-m-d H:i:s'),
	     			'json_response' => $dataLog,
	     	));
     	}
     }
     
     public function listLog($db, $filter = array()){
         
         $where_fields = "log_system.store_id = {$this->store_id} AND ";
         foreach($filter as $key => $value){
             if(!empty($this->{$key})){
                 switch($key){
                     case 'product_id': $where_fields .= "log_system.{$key} = '{$this->$key}' AND ";break;
                     case 'user_id': $where_fields .= "log_system.{$key} = '{$this->$key}' AND ";break;
                     case 'controller': $where_fields .= "log_system.{$key} LIKE '{$this->$key}' AND ";break;
                     case 'method': $where_fields .= "log_system.{$key} LIKE '{$this->$key}' AND ";break;
                     case 'action': $where_fields .= "log_system.{$key} LIKE '{$this->$key}' AND ";break;
                     case 'created': $where_fields .= "log_system.{$key} >= '".dbDate($this->$key)."' AND ";break;
                     
                 }
             }
             
         }
         
         $where_fields = substr($where_fields, 0,-4);
         $sql = "SELECT log_system.product_id,
            log_system.type,log_system.controller,
            log_system.method,log_system.action,
            log_system.information,log_system.created,
            users.name, available_products.title
            FROM `log_system` 
            LEFT JOIN users ON users.id = log_system.user_id
            LEFT JOIN available_products ON available_products.id = log_system.product_id
            WHERE {$where_fields} ORDER BY log_system.created DESC LIMIT 50";
         $query = $db->query($sql);
         
         if ( ! $query ) {
             
             return array();
         }
         return $query->fetchAll(PDO::FETCH_ASSOC);
         
         
     }
} // MainModel