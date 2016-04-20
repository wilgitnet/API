<?php
App::uses('AppController', 'Controller');
/**
 * Situacaos Controller
 *
 * @property Situacao $Situacao
 * @property PaginatorComponent $Paginator
 */
class SituacaosController extends AppController {

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
		$this->Situacao->recursive = 0;
		$this->set('situacaos', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Situacao->exists($id)) {
			throw new NotFoundException(__('Invalid situacao'));
		}
		$options = array('conditions' => array('Situacao.' . $this->Situacao->primaryKey => $id));
		$this->set('situacao', $this->Situacao->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Situacao->create();
			if ($this->Situacao->save($this->request->data)) {
				$this->Flash->success(__('The situacao has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The situacao could not be saved. Please, try again.'));
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
		if (!$this->Situacao->exists($id)) {
			throw new NotFoundException(__('Invalid situacao'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Situacao->save($this->request->data)) {
				$this->Flash->success(__('The situacao has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The situacao could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Situacao.' . $this->Situacao->primaryKey => $id));
			$this->request->data = $this->Situacao->find('first', $options);
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
		$this->Situacao->id = $id;
		if (!$this->Situacao->exists()) {
			throw new NotFoundException(__('Invalid situacao'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Situacao->delete()) {
			$this->Flash->success(__('The situacao has been deleted.'));
		} else {
			$this->Flash->error(__('The situacao could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
