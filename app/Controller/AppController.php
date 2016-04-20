<?php

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * @package		app.Controller 
 */
class AppController extends Controller 
{

	public $Return 		= true;
	public $Message 	= '';
	public $TypeToken 	= 'bd';
	public $ArrayReturn = array();

	public function beforeFilter() 
	{
    	parent::beforeFilter();    	    	

    	if(!$this->modelClass == 'CakeError')
    	{
    		$this->Return = false;
    		$this->Message = 'Requisição inválida';
    		$this->EncodeReturn();
    	}

    	if(!$this->ValidToken())
    	{
    		$this->Return = false;    		
    		$this->EncodeReturn();	
    	}
	}

	##valida token informado do banco de dados pelo client e token gerado dinamicamente pela API
	public function ValidToken()
	{	
		$this->loadModel('Cliente');						

		if($this->TypeToken == 'bd')
		{
			if(empty($this->request->data['token']))
			{
				$this->Message = 'Informar token de Requisição';
				$this->Return = false;
				$this->EncodeReturn();
			}				

			if(!$this->Cliente->FindToken($this->request->data['token']))
			{			
				$this->Message = $this->Cliente->Message;
				$this->Return = false;	
				$this->EncodeReturn();
			}				
			
			
			$this->Message = $this->Cliente->Message;
			$this->Return = true;
			##chamar funcao para gerar token de utilizacao

			$this->EncodeReturn();			
		}
		else
		{

			echo "validar aqui token da API";exit;
		}			
	}

	##trata dados para retorno de api
	public function EncodeReturn()
	{	
		$Array = array('message'=>$this->Message, 'success'=>$this->Return);
		$Json = json_encode($Array);
		echo $Json;
		exit;
	}


}
