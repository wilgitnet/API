<?php
App::uses('AppController', 'Controller');
/**
 * Clientes Controller
 *
 * @property Cliente $Cliente
 * @property PaginatorComponent $Paginator
 */
class ClientesController extends AppController {

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
		$this->Cliente->recursive = 0;
		$this->set('clientes', $this->Paginator->paginate());
	}

	##buscando dados a partir de dominio
	public function find_dominio()
	{
		##realizar aqui busca por cliente
		$this->DadosArray = $this->Cliente->find('first', array(
		    'conditions' => array(		        
		        'dominio' => $this->request->data['dominio']
		    )		    
		));		
		$this->EncodeReturn();
		exit;
	}


	##buscando dados de cliente
	public function find()
	{			
		$this->Cliente->unbindModel(array('belongsTo' => array('UsuarioSabore', 'Mensalidade', 'Situacao')));		
		$this->Cliente->unbindModel(array('hasMany' => array('Categoria')));		

		##realizar aqui busca por cliente
		$this->DadosArray = $this->Cliente->find('first', array(
		    'conditions' => array(		        
		        'Cliente.id' => $this->request->data['id_cliente'],
		        'Cliente.situacao_id' => $this->SituacaoOK        
		    )		    
		));		
		
		if(empty($this->DadosArray['Cliente']['id']))
		{
			$this->Return = false;
		}

		$this->EncodeReturn();
		exit;
	}


/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Cliente->exists($id)) {
			throw new NotFoundException(__('Invalid cliente'));
		}
		$options = array('conditions' => array('Cliente.' . $this->Cliente->primaryKey => $id));
		$this->set('cliente', $this->Cliente->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Cliente->create();
			if ($this->Cliente->save($this->request->data)) {
				$this->Flash->success(__('The cliente has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The cliente could not be saved. Please, try again.'));
			}
		}
		$mensalidades = $this->Cliente->Mensalidade->find('list');
		$usuarioSabores = $this->Cliente->UsuarioSabore->find('list');
		$situacaos = $this->Cliente->Situacao->find('list');
		$this->set(compact('mensalidades', 'usuarioSabores', 'situacaos'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Cliente->exists($id)) {
			throw new NotFoundException(__('Invalid cliente'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Cliente->save($this->request->data)) {
				$this->Flash->success(__('The cliente has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The cliente could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Cliente.' . $this->Cliente->primaryKey => $id));
			$this->request->data = $this->Cliente->find('first', $options);
		}
		$mensalidades = $this->Cliente->Mensalidade->find('list');
		$usuarioSabores = $this->Cliente->UsuarioSabore->find('list');
		$situacaos = $this->Cliente->Situacao->find('list');
		$this->set(compact('mensalidades', 'usuarioSabores', 'situacaos'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Cliente->id = $id;
		if (!$this->Cliente->exists()) {
			throw new NotFoundException(__('Invalid cliente'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Cliente->delete()) {
			$this->Flash->success(__('The cliente has been deleted.'));
		} else {
			$this->Flash->error(__('The cliente could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
