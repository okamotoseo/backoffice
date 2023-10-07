<?php
/**
 * Modelo para gerenciar perfil
 *
 * @package 
 * @since 0.1
 */
class UserProfileModel extends MainModel
{
    

    
    public function __construct( $db = false, $controller = null ) {
        // Configura o DB (PDO)
        $this->db = $db;
        
        // Configura o controlador
        $this->controller = $controller;
        
        // Configura os parâmetros
        $this->parametros = $this->controller->parametros;
        
        // Configura os dados do usuário
        $this->userdata = $this->controller->userdata;
    }
    
    
    
} 