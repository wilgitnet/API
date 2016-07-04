<?php
App::uses('AppController', 'Controller');
/**
 * UsuarioClientes Controller
 *
 * @property UsuarioCliente $UsuarioCliente
 * @property PaginatorComponent $Paginator
 */
class UsuarioClientesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->UsuarioCliente->recursive = 0;
		$this->set('usuarioClientes', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function cadastrar() {		
		if ($this->request->is('post')) 
		{			
			unset($this->request->data['TokenRequest']);			
			$POST = array('UsuarioCliente'=>$this->request->data);			
			$this->UsuarioCliente->create();

			if ($this->UsuarioCliente->save($POST)) 
			{
				$this->Message = 'Usuario cadastrado com sucesso';
				$this->Return = true;
			} 
			else 
			{	
				$this->Message = 'Ocorreu um Erro no seu cadastro de Usuario';								
				$this->Return = false;
			}
		}

		$this->EncodeReturn();	
	}

	public function login()
	{		

		if(empty($this->request->data['usuario']) || empty($this->request->data['senha']))
		{
			$this->Message = 'Informar login e senha para realizar login';	
			$this->Return = false;
			$this->EncodeReturn();						
		}
		
		$EmailSearch = $this->UsuarioCliente->find('first', array(
		    'conditions' => array(		        
		        'UsuarioCliente.email' => $this->request->data['usuario']		        
		    ),
		    'limit' => 1,
		    'fields' => array('email')
		));		
		
		$password = $this->request->data['senha'];
		$email = $this->request->data['usuario'];
		if(!empty($EmailSearch['UsuarioCliente']['email']))
			$email = $EmailSearch['UsuarioCliente']['email'];

		$login = $this->UsuarioCliente->find('first', array(
			'conditions' => array(
				'UsuarioCliente.email' => $email,
				'UsuarioCliente.senha' => $password				
			),
			'limit' => 1
		));

		if(empty($login['UsuarioCliente']['id']))
		{			
			$this->Message = 'Usuario ou senha inválidos';	
			$this->Return = false;
			$this->EncodeReturn();
		}	

		$this->DadosArray = $login;
		$this->EncodeReturn();
	}

	public function find_first()
	{
		$usuariocliente = array();

		$this->UsuarioCliente->unbindModel(array('belongsTo' => array('Cliente')));				
		##se não foi enviado id retorna erro
		if(empty($this->request->data['id']))
		{
			$this->Message = 'ID de usuario não foi informado';
			$this->Return = false;
			$this->EncodeReturn();
		}

		$usuariocliente = $this->UsuarioCliente->find('first', array(
			'conditions' => array(
					'UsuarioCliente.id' => $this->request->data['id']
				)
		));

		$this->DadosArray = $usuariocliente;
		$this->EncodeReturn();
	}


	public function find_list() 
	{
		$usuariocliente = array();

		$this->UsuarioCliente->unbindModel(array('belongsTo' => array('Cliente')));				

		if(empty($this->request->data['search']))
		{
			##monta array que verifica se já existe uma categoria cadastrada no sistema
			$usuariocliente = $this->UsuarioCliente->find('all', 

				array('conditions' => array(							
							 $this->request->data['cliente_id'],
						)
					)
			);
		}
		else
		{
			##monta array que verifica se já existe uma categoria cadastrada no sistema
			$usuariocliente = $this->UsuarioCliente->find('all', 

				array('conditions' => array(							
							 $this->request->data['cliente_id'],
							 'OR' => array(
							 		'UsuarioCliente.nome LIKE ' => "%{$this->request->data['search']}%",
							 		'UsuarioCliente.login LIKE ' => "%{$this->request->data['search']}%",
							 		'UsuarioCliente.email LIKE ' => "%{$this->request->data['search']}%"
							 	)
						)
					)
			);				
		}

		

		$this->DadosArray = $usuariocliente;
		$this->EncodeReturn();
	}
	
	public function deletar() {

		$this->UsuarioCliente->id = $this->request->data['id'];

		##verifica se categoria existe
		if (!$this->UsuarioCliente->exists()) 
		{
			$this->Message = 'Usuario não existe';
			$this->Return = false;
		}

		$this->request->allowMethod('post', 'delete');

		##faz o delete
		if ($this->UsuarioCliente->delete()) 
		{
			$this->Message = 'Usuário excluido com sucesso';
			$this->Return = true;
		} 
		else 
		{
			$this->Message = 'Ocorreu um erro na exclusão do usuario';
			$this->Return = false;
		}		

		$this->EncodeReturn();
	}
public function editar() {			

			##variavel que vai receber os dados para enviar p/ banco de dados
		$POST = array();

			##apagando indice de tokenrequest pois ele não existe na tabela de categorias
		unset($this->request->data['TokenRequest']);	

		$POST = array('UsuarioCliente'=>$this->request->data);	
		if ($this->UsuarioCliente->save($POST)) 
		{
			$this->Message = 'Usuario editado com sucesso';
			$this->Return = true;	
		} 

		else 
		{
			$this->Message = 'Ocorreu um erro na edição de seu Usuario.';
			$this->Return = false;	
		}

		$this->EncodeReturn();	
	}
}
