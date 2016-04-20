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
	public function view($id = null) {
		if (!$this->UsuarioCliente->exists($id)) {
			throw new NotFoundException(__('Invalid usuario cliente'));
		}
		$options = array('conditions' => array('UsuarioCliente.' . $this->UsuarioCliente->primaryKey => $id));
		$this->set('usuarioCliente', $this->UsuarioCliente->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->UsuarioCliente->create();
			if ($this->UsuarioCliente->save($this->request->data)) {
				$this->Flash->success(__('The usuario cliente has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The usuario cliente could not be saved. Please, try again.'));
			}
		}
		$clientes = $this->UsuarioCliente->Cliente->find('list');
		$this->set(compact('clientes'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->UsuarioCliente->exists($id)) {
			throw new NotFoundException(__('Invalid usuario cliente'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->UsuarioCliente->save($this->request->data)) {
				$this->Flash->success(__('The usuario cliente has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The usuario cliente could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('UsuarioCliente.' . $this->UsuarioCliente->primaryKey => $id));
			$this->request->data = $this->UsuarioCliente->find('first', $options);
		}
		$clientes = $this->UsuarioCliente->Cliente->find('list');
		$this->set(compact('clientes'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->UsuarioCliente->id = $id;
		if (!$this->UsuarioCliente->exists()) {
			throw new NotFoundException(__('Invalid usuario cliente'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->UsuarioCliente->delete()) {
			$this->Flash->success(__('The usuario cliente has been deleted.'));
		} else {
			$this->Flash->error(__('The usuario cliente could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
