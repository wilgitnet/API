<?php
App::uses('AppController', 'Controller');
/**
 * SituacaoPedidos Controller
 *
 * @property SituacaoPedido $SituacaoPedido
 * @property PaginatorComponent $Paginator
 */
class SituacaoPedidosController extends AppController {

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
		$this->SituacaoPedido->recursive = 0;
		$this->set('situacaoPedidos', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->SituacaoPedido->exists($id)) {
			throw new NotFoundException(__('Invalid situacao pedido'));
		}
		$options = array('conditions' => array('SituacaoPedido.' . $this->SituacaoPedido->primaryKey => $id));
		$this->set('situacaoPedido', $this->SituacaoPedido->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SituacaoPedido->create();
			if ($this->SituacaoPedido->save($this->request->data)) {
				$this->Flash->success(__('The situacao pedido has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The situacao pedido could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->SituacaoPedido->exists($id)) {
			throw new NotFoundException(__('Invalid situacao pedido'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->SituacaoPedido->save($this->request->data)) {
				$this->Flash->success(__('The situacao pedido has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The situacao pedido could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SituacaoPedido.' . $this->SituacaoPedido->primaryKey => $id));
			$this->request->data = $this->SituacaoPedido->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->SituacaoPedido->id = $id;
		if (!$this->SituacaoPedido->exists()) {
			throw new NotFoundException(__('Invalid situacao pedido'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->SituacaoPedido->delete()) {
			$this->Flash->success(__('The situacao pedido has been deleted.'));
		} else {
			$this->Flash->error(__('The situacao pedido could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
