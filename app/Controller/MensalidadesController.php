<?php
App::uses('AppController', 'Controller');
/**
 * Mensalidades Controller
 *
 * @property Mensalidade $Mensalidade
 * @property PaginatorComponent $Paginator
 */
class MensalidadesController extends AppController {

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
		$this->Mensalidade->recursive = 0;
		$this->set('mensalidades', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Mensalidade->exists($id)) {
			throw new NotFoundException(__('Invalid mensalidade'));
		}
		$options = array('conditions' => array('Mensalidade.' . $this->Mensalidade->primaryKey => $id));
		$this->set('mensalidade', $this->Mensalidade->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Mensalidade->create();
			if ($this->Mensalidade->save($this->request->data)) {
				$this->Flash->success(__('The mensalidade has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The mensalidade could not be saved. Please, try again.'));
			}
		}
		$situacaos = $this->Mensalidade->Situacao->find('list');
		$this->set(compact('situacaos'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Mensalidade->exists($id)) {
			throw new NotFoundException(__('Invalid mensalidade'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Mensalidade->save($this->request->data)) {
				$this->Flash->success(__('The mensalidade has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The mensalidade could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Mensalidade.' . $this->Mensalidade->primaryKey => $id));
			$this->request->data = $this->Mensalidade->find('first', $options);
		}
		$situacaos = $this->Mensalidade->Situacao->find('list');
		$this->set(compact('situacaos'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Mensalidade->id = $id;
		if (!$this->Mensalidade->exists()) {
			throw new NotFoundException(__('Invalid mensalidade'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Mensalidade->delete()) {
			$this->Flash->success(__('The mensalidade has been deleted.'));
		} else {
			$this->Flash->error(__('The mensalidade could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
