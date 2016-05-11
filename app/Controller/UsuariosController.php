<?php
App::uses('AppController', 'Controller');
/**
 * Usuarios Controller
 *
 * @property Usuario $Usuario
 * @property PaginatorComponent $Paginator
 */
class UsuariosController extends AppController {

	##inserindo usuario
	public function add() {

		if ($this->request->is('post')) 
		{			

			unset($this->request->data['TokenRequest']);
			$this->request->data['senha'] = md5($this->request->data['senha']);
			$POST = array('Usuario'=>$this->request->data);			
			$this->Usuario->create();

			if ($this->Usuario->save($POST)) 
			{
				$this->Message = 'Usuário cadastrado com sucesso';
				$this->Return = true;
			} 
			else 
			{	
				$this->Message = 'Ocorreu um Erro no seu cadastro';				
				foreach ($this->Usuario->validationErrors as $array) 
				{
					$this->MessageArray[] = $array[0];
				}				
				$this->Return = false;
			}
		}

		$this->EncodeReturn();		
	}

	##realizando login de usuario
	public function login()
	{		

		if(empty($this->request->data['usuario']) || empty($this->request->data['senha']))
		{
			$this->Message = 'Informar login e senha para realizar login';	
			$this->Return = false;
			$this->EncodeReturn();						
		}

		##realizar aqui busca por cliente
		$this->Usuario->unbindModel(array('hasMany' => array('Pedido')));	
		$EmailSearch = $this->Usuario->find('first', array(
		    'conditions' => array(		        
		        'Usuario.usuario' => $this->request->data['usuario']		        
		    ),
		    'limit' => 1,
		    'fields' => array('email')
		));		

		$password = $this->request->data['senha'];
		$email = $this->request->data['usuario'];
		if(!empty($EmailSearch['Usuario']['email']))
			$email = $EmailSearch['Usuario']['email'];

		##login
		$this->Usuario->unbindModel(array('hasMany' => array('Pedido')));	
		$login = $this->Usuario->find('first', array(
			'conditions' => array(
				'Usuario.email' => $email,
				'Usuario.senha' => $password				
			),
			'limit' => 1
		));

		if(empty($login['Usuario']['id']))
		{
			$this->Message = 'Informar login e senha para realizar login';	
			$this->Return = false;
			$this->EncodeReturn();
		}	

		$this->DadosArray = $login;
		$this->EncodeReturn();
	}

	##gerando token para troca de senha
	public function password_token()
	{
		if(empty($this->request->data['email']))
		{	
			$this->Message = 'Informar email para recuperar senha';
			$this->Return = false;
			$this->EncodeReturn();
		}

		$this->Usuario->unbindModel(array('hasMany' => array('Pedido')));	
		$email = $this->Usuario->find('first', array(
			'conditions' => array(
				'Usuario.email' => $this->request->data['email']
			),
			'limit' => 1
		));

		if(empty($email['Usuario']['id']))
		{
			$this->Message = 'Email não encontrado';
			$this->Return = false;
			$this->EncodeReturn();
		}
		
		$token = $this->GenerateTokenSenha();

		if(!$this->Usuario->save(array('Usuario'=>array('id'=>$email['Usuario']['id'], 'token_senha'=>$token)))) 
		{
			$this->Message = 'Ocorreu um erro na sua solicitação. Tente novamente';
			$this->Return = false;
			$this->EncodeReturn();
		}
		
		$email['token_senha'] = $token;
		$this->DadosArray = $email;
		$this->EncodeReturn();
	}


	public function new_password()
	{
		if(empty($this->request->data['token']))
		{
			$this->Message = 'Informar token para troca de senha';
			$this->Return = false;
			$this->EncodeReturn();
		}

		if(substr($this->request->data['token'], 0, 1) != 'S')
		{
			$this->Message = 'Token inválido ou já utilizado';
			$this->Return = false;
			$this->EncodeReturn();
		}

		$this->Usuario->unbindModel(array('hasMany' => array('Pedido')));
		$token = $this->Usuario->find('first', array(
			'conditions' => array(
				'Usuario.token_senha' => $this->request->data['token']
			)
		));

		if(empty($token['Usuario']['id']))
		{			
			$this->Message = 'Token inválido ou já utilizado';
			$this->Return = false;
			$this->EncodeReturn();
		}

		if(!$this->Usuario->save(array('Usuario'=>array('id'=>$token['Usuario']['id'], 'token_senha'=>'', 'senha'=>$this->request->data['senha'])))) 
		{
			$this->Message = 'Ocorreu um erro na sua solicitação. Tente novamente';
			$this->Return = false;
			$this->EncodeReturn();
		}		

		$this->EncodeReturn();
	}	

	##gerando token para senha
	private function GenerateTokenSenha()
	{
		$token = 'S';
		$caracteres = '';		
		$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$num = '1234567890';		
		$caracteres .= $lmai;
		$caracteres .= $num;
		
		$len = strlen($caracteres);					
		for ($i=0; $i < 25; $i++) 
		{ 
			$rand = mt_rand(1, $len);		
			$token .= $caracteres[$rand-1];
		}
			
		return $token;
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		if (!$this->Usuario->exists($this->request->data['id'])) 
		{
			$this->Message = 'Ocorreu um erro no seu cadastro';
			$this->Return = false;	
		}

		if ($this->request->is(array('post', 'put'))) 
		{
			unset($this->request->data['TokenRequest']);			
			$POST = array('Usuario'=>$this->request->data);		

			if ($this->Usuario->save($POST)) 
			{
				$this->Message = 'Usuário editado com sucesso';
				$this->Return = true;
			} 
			else 
			{
				$this->Message = 'Ocorreu um Erro no seu cadastro';				
				foreach ($this->Usuario->validationErrors as $array) 
				{
					$this->MessageArray[] = $array[0];
				}				
				$this->Return = false;
			}
		} 

		$this->EncodeReturn();

	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Usuario->id = $id;
		if (!$this->Usuario->exists()) {
			throw new NotFoundException(__('Invalid usuario'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Usuario->delete()) {
			$this->Flash->success(__('The usuario has been deleted.'));
		} else {
			$this->Flash->error(__('The usuario could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
