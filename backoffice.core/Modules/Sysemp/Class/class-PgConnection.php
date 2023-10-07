<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
class PgConnection{
  	

    
    public $host       = null, 
           $store_id   = null,
           $db_name    = null,   
           $password   = null,  
           $user       = null,   
           $pdo        = null,        
           $port       = null,        
           $charset    = 'utf-8',     
           $debug      = true,
           $error      = true,        
           $last_id    = null;        
    
    
    private $db = null;


    public function __construct(
        $db       = null,
        $storeId  = null,
        $host     = null,
        $port     = null,
        $db_name  = null,
        $password = null,
        $user     = null
        ) {
            if(!is_null($db) AND !is_null($storeId)){
                $this->db = $db;
                $this->store_id = $storeId;
                $query = $this->db->query('SELECT * FROM `module_sysemp` WHERE store_id = ?',
                    array($this->store_id)
                    );
                if ( ! $query ) {
                    return array();
                }
                $store = $query->fetch();
            }
            $this->host       = $host       ? $host       : $store['host'];
            $this->port       = $port       ? $port       : $store['port'];
            $this->db_name    = $db_name    ? $db_name    : $store['dbname'];
            $this->password   = $password   ? $password   : $store['password'];
            $this->user       = $user       ? $user       : $store['user'];
            
            $this->charset  = defined( 'DB_CHARSET'  ) ? DB_CHARSET  : $this->charset;
            $this->debug    = defined( 'DEBUG'       ) ? DEBUG       : $this->debug;
            
            $this->connect();
            
    } 
    
    final protected function connect() {
        
        /* Os detalhes da nossa conexão PDO */
        $pdo_details  = "pgsql:host={$this->host};";
        $pdo_details .= "port={$this->port};";
        $pdo_details .= "dbname={$this->db_name};";
        
        // Tenta conectar
        try {
            
            $this->pdo = new PDO($pdo_details, $this->user, $this->password);
            $this->pdo->query("SET app.user_session = '1'");
            // Verifica se devemos debugar
            if ( $this->debug === true ) {
                // Configura o PDO ERROR MODE
                $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
                
            }
            // Não precisamos mais dessas propriedades
            unset( $this->host     );
            unset( $this->port     );
            unset( $this->db_name  );
            unset( $this->password );
            unset( $this->user     );
            unset( $this->charset  );
            
        } catch (PDOException $e) {
            
            // Verifica se devemos debugar
            if ( $this->debug === true ) {
                // Mostra a mensagem de erro
                echo "Erro: " . $e->getMessage();
                
            }
            
            // Kills the script
            return ;
        } // catch
    } // connect
    
    
    
    /**
     * query - Consulta PDO
     *
     * @since 0.1
     * @access public
     * @return object|bool Retorna a consulta ou falso
     */
    public function describe( $table, $column = null ) {
        
        $stmt = "SELECT column_name, data_type, character_maximum_length, column_default, is_nullable,
        numeric_precision, numeric_scale
            FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='{$table}'";
        
        //         $stmt = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='{$table}'";
        if(isset($column)){
            $stmt .= " AND column_name LIKE '{$column}'";
        }
        // Prepara e executa
        $query      = $this->pdo->prepare( $stmt );
        //         pre($query);die;
        $check_exec = $query->execute(  );
        
        // Verifica se a consulta aconteceu
        if ( $check_exec ) {
            
            
            // Retorna a consulta
            return $query;
            
        } else {
            
            // Configura o erro
            $error       = $query->errorInfo();
            $this->error = $error[2];
            
            // Retorna falso
            return false;
            
        }
    }
    /**
     * query - Consulta PDO
     *
     * @since 0.1
     * @access public
     * @return object|bool Retorna a consulta ou falso
     */
    public function query( $stmt, $data_array = null ) {
        
        // Prepara e executa
        $query      = $this->pdo->prepare( $stmt );
//         pre($query);die;
        $check_exec = $query->execute( $data_array );
        
        // Verifica se a consulta aconteceu
        if ( $check_exec ) {

            
            // Retorna a consulta
            return $query;
            
        } else {
            
            // Configura o erro
            $error       = $query->errorInfo();
            $this->error = $error[2];
            
            // Retorna falso
            return false;
            
        }
    }
    
    /**
     * insert - Insere valores
     *
     * Insere os valores e tenta retornar o último id enviado
     *
     * @since 0.1
     * @access public
     * @param string $table O nome da tabela
     * @param array ... Ilimitado número de arrays com chaves e valores
     * @return object|bool Retorna a consulta ou falso
     */
    public function insert( $table ) {
        // Configura o array de colunas
        $cols = array();
        
        // Configura o valor inicial do modelo
        $place_holders = '(';
        
        // Configura o array de valores
        $values = array();
        
        // O $j will assegura que colunas serão configuradas apenas uma vez
        $j = 1;
        
        // Obtém os argumentos enviados
        $data = func_get_args();
        
        //         print_r($data[1]);die;
        
        // É preciso enviar pelo menos um array de chaves e valores
        if ( ! isset( $data[1] ) || ! is_array( $data[1] ) ) {
            return;
        }
        
        // Faz um laço nos argumentos
        for ( $i = 1; $i < count( $data ); $i++ ) {
            
            // Obtém as chaves como colunas e valores como valores
            foreach ( $data[$i] as $col => $val ) {
                
                // A primeira volta do laço configura as colunas
                if ( $i === 1 ) {
                    $cols[] = "$col";
                }
                
                if ( $j <> $i ) {
                    // Configura os divisores
                    $place_holders .= '), (';
                }
                
                // Configura os place holders do PDO
                $place_holders .= '?, ';
                
                // Configura os valores que vamos enviar
                $values[] = $val;
                
                $j = $i;
            }
            
            // Remove os caracteres extra dos place holders
            $place_holders = substr( $place_holders, 0, strlen( $place_holders ) - 2 );
        }
        
        // Separa as colunas por vírgula
        $cols = implode(', ', $cols);
        
        // Cria a declaração para enviar ao PDO
        $stmt = "INSERT INTO $table ( $cols ) VALUES $place_holders) ";
//         print_r($values);die;
        // Insere os valores
//         pre($values);die;
        $insert = $this->query( $stmt, $values );
//                 print_r($this->error);die;
        //         print_r($insert);die;
        // Verifica se a consulta foi realizada com sucesso
//         pre($insert);die;
        if ( $insert ) {
            // Verifica se temos o último ID enviado
            if ( method_exists( $this->pdo, 'lastInsertId' )
                && $this->pdo->lastInsertId()
                ) {
                    // Configura o último ID
                    $this->last_id = $this->pdo->lastInsertId();
                }
                
                // Retorna a consulta
                return $insert;
        }
        
        // The end :)
        return;
    } // insert
    
    /**
     * Update simples
     *
     * Atualiza uma linha da tabela baseada em um campo
     *
     * @since 0.1
     * @access protected
     * @param string $table Nome da tabela
     * @param string $where_field WHERE $where_field = $where_field_value
     * @param string $where_field_value WHERE $where_field = $where_field_value
     * @param array $values Um array com os novos valores
     * @return object|bool Retorna a consulta ou falso
     */
    public function update( $table, $where_field, $where_field_value, $values ) {
        // Você tem que enviar todos os parâmetros
        if ( empty($table) || empty($where_field) || empty($where_field_value)  ) {
            return;
        }
        
        // Começa a declaração
        $stmt = " UPDATE $table SET ";
        
        // Configura o array de valores
        $set = array();
        
        // Configura a declaração do WHERE campo=valor
//         $where = " WHERE `$where_field` = ? ";

        $where = " WHERE";
        
        foreach ( $where_field as $field ) {
            $where .= " $field = ? AND";
        }
        $where = substr($where, 0, -4);
        
        // Você precisa enviar um array com valores
        if ( ! is_array( $values ) ) {
            return;
        }
        
        // Configura as colunas a atualizar
        foreach ( $values as $column => $value ) {
            $set[] = " $column = ?";
        }
        
        // Separa as colunas por vírgula
        $set = implode(', ', $set);
        
        // Concatena a declaração
        $stmt .= $set . $where;
        
//         pre($values);
//         pre($where_field_value);
        // Configura o valor do campo que vamos buscar
//         $values[] = $where_field_value;
        foreach ( $where_field_value as $value ) {
            $values[] = $value;
        }
        
        // Garante apenas números nas chaves do array
        $values = array_values($values);
        // Atualiza
        $update = $this->query( $stmt, $values );
        
        // Verifica se a consulta está OK
        if ( $update ) {
//             pre($update);die;
            // Retorna a consulta
            return $update;
        }
//         echo 123;die;
        // The end :)
        return;
    } // update
    

    
  }
?>