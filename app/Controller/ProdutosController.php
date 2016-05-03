<?php
App::uses('AppController', 'Controller');
/**
 * Produtos Controller
 *
 * @property Produto $Produto
 * @property PaginatorComponent $Paginator
 */
class ProdutosController extends AppController {


	##buscando produtos da home
	public function home()
	{
		$ProdutosAleatorios = array();
		$destaques = array();
		$QtdAleatorio = 6;

		if(empty($this->request->data['categoria_id']))
		{
			$this->Return = false;
			$this->Message = 'Informar categoria do produto';
			$this->EncodeReturn();
		}

		##realizar aqui busca por produtos em destaque
		$this->Produto->unbindModel(array('belongsTo' => array('Categoria', 'Situacao')));				
		$destaques = $this->Produto->find('all', array(			
		    'conditions' => array(		        
		        'Produto.categoria_id' => $this->request->data['categoria_id'],
		        'Produto.destaque' => 'S',
		        'Produto.situacao_id' => $this->SituacaoOK
		    ),
		    'limit' => 3		    
		));	

		##verifica quantos aleatorios vai buscar de acordo com a quantidade de destaques para montar grids com 6 produtos
		$restQtd = $QtdAleatorio - count($destaques);

		##busca produtos aleatorios
		$this->Produto->unbindModel(array('belongsTo' => array('Categoria', 'Situacao')));		
		$aleatorios = $this->Produto->find('all', array(			
		    'conditions' => array(		        
		        'Produto.categoria_id' => $this->request->data['categoria_id'],
		        'Produto.destaque <> ' => 'S',
		        'Produto.situacao_id' => $this->SituacaoOK
		    ),		    
		));	

		##obrigatorio ter no minimo 6 produtos cadastrados
		if(count($aleatorios) < $restQtd)
		{
			$this->Return = false;
			$this->Message = 'Nenhum produto encontrado. Inserir produtos no site';
			$this->EncodeReturn();			
		}

		##sorteando array de produtos retornados sem destaque
		$SortAleatorios = array_rand($aleatorios, $restQtd);
		foreach($SortAleatorios as $key => $value)
		{						
			$ProdutosAleatorios[] = $aleatorios[$value];			
		}

		##inserindo aleatorios em destaques se o destaque não completar 3
		$u = 0;
		for ($i=count($destaques); $i < 3 ; $i++) 
		{ 
			$destaques[] = $ProdutosAleatorios[$u];
			unset($ProdutosAleatorios[$u]);
			$u++;
		}		
				
		$this->DadosArray['aleatorios'] = $ProdutosAleatorios;
		$this->DadosArray['destaques']  = $destaques;		
		$this->EncodeReturn();		
	}

	##buscando produtos de uma categoria e categorias validas
	public function find()
	{

		if(empty($this->request->data['categoria_placeholder']) || empty($this->request->data['id_cliente']))
		{
			$this->Return = false;
			$this->Message = 'Informar categoria e id do cliente';
			$this->EncodeReturn();		
		}

		$this->Produto->unbindModel(array('belongsTo' => array('Situacao')));	
		$produtos = $this->Produto->find('all', array(
			'conditions' => array(
					'Categoria.placeholder' => $this->request->data['categoria_placeholder'],
					'Produto.situacao_id' => $this->SituacaoOK
				),
			'order' => "Produto.nome ASC"
		));
		
		if(empty($produtos[3]['Produto']))
		{
			$this->Return = false;
			$this->Message = 'Essa categoria não tem 4 produtos cadastrados';
			$this->EncodeReturn();	
		}

		$categorias = $this->Produto->query(sprintf('SELECT Categorias.id, Categorias.nome, Categorias.placeholder FROM categorias Categorias where cliente_id = %d and (Select count(*) FROM produtos where categoria_id = Categorias.id) > 3', $this->request->data['id_cliente']));	
		
		$this->DadosArray['ProdutoArray'] = $produtos;
		$this->DadosArray['CategoriaArray'] = $categorias;
		$this->EncodeReturn();	
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Produto->exists($id)) {
			throw new NotFoundException(__('Invalid produto'));
		}
		$options = array('conditions' => array('Produto.' . $this->Produto->primaryKey => $id));
		$this->set('produto', $this->Produto->find('first', $options));
	}


/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Produto->create();
			if ($this->Produto->save($this->request->data)) {
				$this->Flash->success(__('The produto has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The produto could not be saved. Please, try again.'));
			}
		}
		$situacaos = $this->Produto->Situacao->find('list');
		$classes = $this->Produto->Classe->find('list');
		$this->set(compact('situacaos', 'classes'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Produto->exists($id)) {
			throw new NotFoundException(__('Invalid produto'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Produto->save($this->request->data)) {
				$this->Flash->success(__('The produto has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The produto could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Produto.' . $this->Produto->primaryKey => $id));
			$this->request->data = $this->Produto->find('first', $options);
		}
		$situacaos = $this->Produto->Situacao->find('list');
		$classes = $this->Produto->Classe->find('list');
		$this->set(compact('situacaos', 'classes'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Produto->id = $id;
		if (!$this->Produto->exists()) {
			throw new NotFoundException(__('Invalid produto'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Produto->delete()) {
			$this->Flash->success(__('The produto has been deleted.'));
		} else {
			$this->Flash->error(__('The produto could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
