<?php

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * @package		app.Controller 
 */
class AppController extends Controller 
{

	public $Return 		  	= true;
	private $RequestReturn 	= true;
	public $Message 	  	= '';
	public $TypeToken 	  	= 'bd';
	public $ArrayReturn   	= array();
	public $TokenRequest  	= '';
	public $DadosArray 		= array();
	public $SituacaoOK 		= 1;

	public function beforeFilter() 
	{
    	parent::beforeFilter();    	    	    

    	if(!$this->ValidToken())
    	{
    		$this->RequestReturn = false;    		
    		$this->EncodeReturn();	
    	}

    	/*
    	if($this->modelClass == 'CakeError')
    	{
    		$this->RequestReturn = false;
    		$this->Message = 'Requisição inválida';
    		$this->EncodeReturn();
    	}
    	*/
	}

	##valida token informado e gerado dinamicamente pela API
	private function ValidToken()
	{															
		if(!empty($this->request->data['TokenRequest']) || !empty($this->TokenRequest))
		{
			$this->TokenRequest = $this->request->data['TokenRequest'];
			$Valid1 			= substr($this->TokenRequest, 15, 1);
			$Valid2 			= substr($this->TokenRequest, 31, 1);
			$h 					= substr($this->TokenRequest, 42, 2);
			$Valid3				= substr($this->TokenRequest, 44, 1);
			$i					= substr($this->TokenRequest, 52, 2);
			$s					= substr($this->TokenRequest, 59, 2);
			$data 				= date('Y/m/d');
			$dataTime			= strtotime(date('Y/m/d h:i:s'));
			$dataRequest 		= strtotime($data.' '.$h.':'.$i.':'.$s);
			$Diff				= $dataTime - $dataRequest;
			
			if($Valid1 == 'C' && $Valid2 == 'C' && $Valid3 == 'B' && $Diff < 20)
			{
				return true;
			}
			else
			{
				$this->Message = 'Token inválido';
				return false;
			}								
		}						
	}

	##trata dados para retorno de api
	public function EncodeReturn()
	{	
		$Array = array('message'=>$this->Message, 'success'=>$this->Return, 'request'=>$this->RequestReturn, 'dados'=>$this->DadosArray);
		$Json = json_encode($Array);
		echo $Json;
		exit;
	}

	##gera token de seguranca para requisicao na API
	private function GenerateToken()
	{
		$caracteres = '';		
		$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$num = '1234567890';		
		$caracteres .= $lmai;
		$caracteres .= $num;
		
		$len = strlen($caracteres);					
		for ($i=0; $i < 15; $i++) 
		{ 
			$rand = mt_rand(1, $len);		
			$this->TokenRequest .= $caracteres[$rand-1];
		}
		
		$this->TokenRequest .= 'C';

		for ($i=0; $i < 15; $i++) 
		{ 
			$rand = mt_rand(1, $len);		
			$this->TokenRequest .= $caracteres[$rand-1];
		}

		$this->TokenRequest .= 'C';

		for ($i=0; $i < 10; $i++) 
		{ 
			$rand = mt_rand(1, $len);		
			$this->TokenRequest .= $caracteres[$rand-1];
		}

		$this->TokenRequest .= date('h');
		$this->TokenRequest .= 'B';

		for ($i=0; $i < 7; $i++) 
		{ 
			$rand = mt_rand(1, $len);		
			$this->TokenRequest .= $caracteres[$rand-1];
		}

		$this->TokenRequest .= date('i');

		for ($i=0; $i < 5; $i++) 
		{ 
			$rand = mt_rand(1, $len);		
			$this->TokenRequest .= $caracteres[$rand-1];
		}

		$this->TokenRequest .= date('s');

		for ($i=0; $i < 2; $i++) 
		{ 
			$rand = mt_rand(1, $len);		
			$this->TokenRequest .= $caracteres[$rand-1];
		}
		
		return true;
	}
}
