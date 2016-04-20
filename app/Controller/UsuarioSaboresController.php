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
				$this->Flash->success(__('The usuario sabore has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The usuario sabore could not be saved. Please, try again.'));
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
		if (!$this->UsuarioSabore->exists($id)) {
			throw new NotFoundException(__('Invalid usuario sabore'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->UsuarioSabore->save($this->request->data)) {
				$this->Flash->success(__('The usuario sabore has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The usuario sabore could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('UsuarioSabore.' . $this->UsuarioSabore->primaryKey => $id));
			$this->request->data = $this->UsuarioSabore->find('first', $options);
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
		$this->UsuarioSabore->id = $id;
		if (!$this->UsuarioSabore->exists()) {
			throw new NotFoundException(__('Invalid usuario sabore'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->UsuarioSabore->delete()) {
			$this->Flash->success(__('The usuario sabore has been deleted.'));
		} else {
			$this->Flash->error(__('The usuario sabore could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
