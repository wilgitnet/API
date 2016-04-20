<?php
App::uses('AppController', 'Controller');
/**
 * FormaPagamentoClientes Controller
 *
 * @property FormaPagamentoCliente $FormaPagamentoCliente
 * @property PaginatorComponent $Paginator
 */
class FormaPagamentoClientesController extends AppController {

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
		$this->FormaPagamentoCliente->recursive = 0;
		$this->set('formaPagamentoClientes', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->FormaPagamentoCliente->exists($id)) {
			throw new NotFoundException(__('Invalid forma pagamento cliente'));
		}
		$options = array('conditions' => array('FormaPagamentoCliente.' . $this->FormaPagamentoCliente->primaryKey => $id));
		$this->set('formaPagamentoCliente', $this->FormaPagamentoCliente->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->FormaPagamentoCliente->create();
			if ($this->FormaPagamentoCliente->save($this->request->data)) {
				$this->Flash->success(__('The forma pagamento cliente has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The forma pagamento cliente could not be saved. Please, try again.'));
			}
		}
		$clientes = $this->FormaPagamentoCliente->Cliente->find('list');
		$formaPagamentos = $this->FormaPagamentoCliente->FormaPagamento->find('list');
		$this->set(compact('clientes', 'formaPagamentos'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->FormaPagamentoCliente->exists($id)) {
			throw new NotFoundException(__('Invalid forma pagamento cliente'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->FormaPagamentoCliente->save($this->request->data)) {
				$this->Flash->success(__('The forma pagamento cliente has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The forma pagamento cliente could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('FormaPagamentoCliente.' . $this->FormaPagamentoCliente->primaryKey => $id));
			$this->request->data = $this->FormaPagamentoCliente->find('first', $options);
		}
		$clientes = $this->FormaPagamentoCliente->Cliente->find('list');
		$formaPagamentos = $this->FormaPagamentoCliente->FormaPagamento->find('list');
		$this->set(compact('clientes', 'formaPagamentos'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->FormaPagamentoCliente->id = $id;
		if (!$this->FormaPagamentoCliente->exists()) {
			throw new NotFoundException(__('Invalid forma pagamento cliente'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->FormaPagamentoCliente->delete()) {
			$this->Flash->success(__('The forma pagamento cliente has been deleted.'));
		} else {
			$this->Flash->error(__('The forma pagamento cliente could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
