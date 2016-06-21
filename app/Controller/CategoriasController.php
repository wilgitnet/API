<?php
App::uses('AppController', 'Controller'); 
/** 
 * Categorias Controller
 *
 * @property Categoria $Categoria
 * @property PaginatorComponent $Paginator
 */
class CategoriasController extends AppController {

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
		$this->Categoria->recursive = 0;
		$this->set('categorias', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Categoria->exists($id)) {
			throw new NotFoundException(__('Invalid categoria'));
		}
		$options = array('conditions' => array('Categoria.' . $this->Categoria->primaryKey => $id));
		$this->set('categoria', $this->Categoria->find('first', $options));
	}

	public function find()
	{
		$categorias = array();

		$this->Categoria->unbindModel(array('belongsTo' => array('Cliente', 'Situacao')));				
		##monta array que verifica se já existe uma categoria cadastrada no sistema
		$categorias = $this->Categoria->find('all', 

				array('conditions' => array(							
							'Categoria.cliente_id' => $this->request->data['cliente_id'],
							'Categoria.situacao_id' => $this->SituacaoOK
						)
					)
			);

		$this->DadosArray = $categorias;
		$this->EncodeReturn();
	}

	public function find_first()
	{
		$categoria = array();

		$this->Categoria->unbindModel(array('belongsTo' => array('Cliente', 'Situacao')));				
		##se não foi enviado id retorna erro
		if(empty($this->request->data['id']))
		{
			$this->Message = 'ID de categoria não foi informado';
			$this->Return = false;
			$this->EncodeReturn();
		}

		$categoria = $this->Categoria->find('first', array(
			'conditions' => array(
					'Categoria.id' => $this->request->data['id']
				)
		));

		$this->DadosArray = $categoria;
		$this->EncodeReturn();
	}


/**
 * add method
 *
 * @return void
 */
	public function add() {

		##variavel que vai receber os dados para enviar p/ banco de dados
		$POST = array();

		$QtdeCategoriaPrincipal = 0;

		##verificando se post com dados do admin foram informados
		if ($this->request->is('post')) 
		{							
			##apagando indice de tokenrequest pois ele não existe na tabela de categorias
			unset($this->request->data['TokenRequest']);			
			
			if ($this->request->data['principal']=='S') {
				$this->ValidarQtdCategoria($this->request->data['cliente_id']);
			}
			

			##preparando dados para cadastrar Categoria = Nome da model			
			$POST = array('Categoria'=>$this->request->data);				

			$this->Categoria->create();
			if ($this->Categoria->save($POST)) 
			{
				$this->Message = 'Categoria cadastrada com sucesso';
				$this->Return = true;
			} 
			else 
			{	
				$this->Message = 'Ocorreu um Erro no seu cadastro de categoria';								
				$this->Return = false;
			}
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
	public function edit() {			

			##variavel que vai receber os dados para enviar p/ banco de dados
		$POST = array();

			##apagando indice de tokenrequest pois ele não existe na tabela de categorias
		unset($this->request->data['TokenRequest']);	
		if ($this->request->data['principal']=='S') {
			$this->ValidarQtdCategoria($this->request->data['cliente_id'], $this->request->data['id']);
		}
		if ($this->request->data['principal']=='N') {
			$this->ValidarQtdCategoria($this->request->data['cliente_id'], $this->request->data['id']);
		}

			##preparando dados para cadastrar Categoria = Nome da model			
		$POST = array('Categoria'=>$this->request->data);	
		if ($this->Categoria->save($POST)) 
		{
			$this->Message = 'Categoria editada com sucesso';
			$this->Return = true;	
		} 

		else 
		{
			$this->Message = 'Ocorreu um erro na edição de sua categoria.';
			$this->Return = false;	
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
	public function delete() {

		$this->Categoria->id = $this->request->data['id'];

		##verifica se categoria existe
		if (!$this->Categoria->exists()) 
		{
			$this->Message = 'Categoria não existe';
			$this->Return = false;
		}

		$this->request->allowMethod('post', 'delete');

		##verifica se existe produto com essa categoria
		$this->loadModel('Produto');
		$validProduto = $this->Produto->find('count', array(
			'conditions' => array(
				'Produto.categoria_id' => $this->request->data['id'],
				'Produto.situacao_id' => $this->SituacaoOK
				)
		));

		##se existe um produto cadastrado para categoria não é possivel excluir
		if($validProduto > 0)
		{
			$this->Message = 'Ops, você nao pode excluir uma categoria com produto ativo cadastrado. Para retirar do site você pode bloquear';
			$this->Return = false;
			$this->EncodeReturn();
		}
		
		##faz o delete
		if ($this->Categoria->delete()) 
		{
			$this->Message = 'Categoria excluida com sucesso';
			$this->Return = true;
		} 
		else 
		{
			$this->Message = 'Ocorreu um erro na exclusão da categoria';
			$this->Return = false;
		}		

		$this->EncodeReturn();
	}


	private function ValidarQtdCategoria($cliente_id, $categoria_id = 0)
	{		
		##monta array que verifica se já existe uma categoria cadastrada no sistema
		$QtdeCategoriaPrincipal = $this->Categoria->find('count', 

			array(
					'conditions' => array(
						'Categoria.principal' => 'S',
						'Categoria.cliente_id' => $cliente_id,
						'Categoria.situacao_id' => $this->SituacaoOK,
						'Categoria.id <>' => $categoria_id
					)
				)
			);

		##verifica se cliente já tem uma categoria como principal
		if($QtdeCategoriaPrincipal > 0)
		{
			$this->Message = 'Ops, você já tem uma categoria cadastrada no sistema como principal';
			$this->Return = false;
			$this->EncodeReturn();					
		}
		if($QtdeCategoriaPrincipal = 0)
		{
			$this->Message = 'Ops, você tem que ter uma categoria cadastrada no sistema como principal';
			$this->Return = false;
			$this->EncodeReturn();					
		}
	}

}
