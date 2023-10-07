<?php

class SacController extends MainController
{

	/**
	 * $login_required
	 *
	 * Se a página precisa de login
	 *
	 * @access public
	 */
	public $login_required = true;
	
	/**
	 * $permission_required
	 *
	 * Permissão necessária
	 *
	 * @access public
	 */
	public $permission_required = 'any';
	
	/**
	 * $panel
	 *
	 * Painel de controle
	 *
	 * @access public
	 */
	public $panel = 'SAC';

	
    public function Questions(){
    	
    	$this->title = 'Atendimento';
    	
    	$this->menu = array("Sac" => "active", "Questions"  => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
//         pre($parametros);die;
        $questionsModel = $this->load_model('Sac/QuestionsModel');
        
        if($questionsModel->ValidateForm()){
        	        	
            $questions = $questionsModel->GetQuestions();
        	
        }else{
        	
        	$questions = $questionsModel->ListQuestions();
            
        }
        $totalReg = $questionsModel->TotalQuestions();
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Sac/QuestionsView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    
}