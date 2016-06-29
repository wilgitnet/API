<?php
App::uses('AppController', 'Controller');
/**
 * UsuarioSabores Controller
 *
 * @property UsuarioSabore $UsuarioSabore
 * @property PaginatorComponent $Paginator
 */
class UsuarioSaboresController extends AppController {

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
		$this->UsuarioSabore->recursive = 0;
		$this->set('usuarioSabores', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->UsuarioSabore->exists($id)) {
			throw new NotFoundException(__('Invalid usuario sabore'));
		}
		$options = array('conditions' => array('UsuarioSabore.' . $this->UsuarioSabore->primaryKey => $id));
		$this->set('usuarioSabore', $this->UsuarioSabore->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->UsuarioSabore->create();
			if ($this->UsuarioSabore->save($this->request->data)) {
				$this->Flash->success(__('Usuário Cadastrado com sucesso'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('O usuario não pode ser cadastrado.'));
			}
		}
	}

	public function cadastrar() {		
		if ($this->request->is('post')) 
		{			
			unset($this->request->data['TokenRequest']);			
			$POST = array('UsuarioSabore'=>$this->request->data);			
			$this->UsuarioSabore->create();

			if ($this->UsuarioSabore->save($POST)) 
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
	public function find_first(){
		$usuariosabore = array();

		$this->UsuarioSabore->unbindModel(array('belongsTo' => array('Cliente')));				
		##se não foi enviado id retorna erro
		if(empty($this->request->data['id']))
		{
			$this->Message = 'ID de usuario não foi informado';
			$this->Return = false;
			$this->EncodeReturn();
		}

		$usuariosabore = $this->UsuarioSabore->find('first', array(
			'conditions' => array(
					'UsuarioSabore.id' => $this->request->data['id']
				)
		));

		$this->DadosArray = $usuariosabore;
		$this->EncodeReturn();
	}


	public function find_list() {
		$usuariosabore = array();

		$this->UsuarioSabore->unbindModel(array('belongsTo' => array('Cliente')));				

		if(empty($this->request->data['search']))
		{
			##monta array que verifica se já existe uma categoria cadastrada no sistema
			$usuariosabore = $this->UsuarioSabore->find('all', 

				array('conditions' => array(							
							 $this->request->data['cliente_id'],
						)
					)
			);
		}
		else
		{
			##monta array que verifica se já existe uma categoria cadastrada no sistema
			$usuariosabore = $this->UsuarioSabore->find('all', 

				array('conditions' => array(							
							 $this->request->data['cliente_id'],
							 'OR' => array(
							 		'UsuarioSabore.nome LIKE ' => "%{$this->request->data['search']}%",
							 		'UsuarioSabore.login LIKE ' => "%{$this->request->data['search']}%",
							 		'UsuarioSabore.documento LIKE ' => "%{$this->request->data['search']}%"
							 	)
						)
					)
			);				
		}

		

		$this->DadosArray = $usuariosabore;
		$this->EncodeReturn();
	}
	
	public function deletar() {

		$this->UsuarioSabore->id = $this->request->data['id'];

		##verifica se categoria existe
		if (!$this->UsuarioSabore->exists()) 
		{
			$this->Message = 'Usuario não existe';
			$this->Return = false;
		}

		$this->request->allowMethod('post', 'delete');

		##faz o delete
		if ($this->UsuarioSabore->delete()) 
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
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function editar() {			

			##variavel que vai receber os dados para enviar p/ banco de dados
		$POST = array();

			##apagando indice de tokenrequest pois ele não existe na tabela de categorias
		unset($this->request->data['TokenRequest']);	

		$POST = array('UsuarioSabore'=>$this->request->data);	
		if ($this->UsuarioSabore->save($POST)) 
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
