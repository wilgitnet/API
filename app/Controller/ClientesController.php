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
	public $components = array('Email');

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
		if(empty($this->request->data['dominio']))
		{
			$this->Return = false;
			$this->Message = 'Informar dominio do cliente';
			$this->EncodeReturn();			
		}

		$this->Cliente->unbindModel(array('belongsTo' => array('UsuarioSabore', 'Mensalidade', 'Situacao')));		
		$this->Cliente->unbindModel(array('hasMany' => array('Categoria')));		

		##realizar aqui busca por cliente atraves do dominio
		$this->DadosArray = $this->Cliente->find('first', array(			
		    'conditions' => array(		        
		        'Cliente.dominio' => $this->request->data['dominio']
		    )		    
		));		
		
		if(empty($this->DadosArray['Cliente']['id']))
		{
			$this->Message = 'Cliente não encontrado';
			$this->Return = false;
			$this->EncodeReturn();			
		}

		##buscando categoria principal		
		$this->loadModel('Categoria');
		$this->Categoria->unbindModel(array('belongsTo' => array('Cliente', 'Situacao')));		
		$categoria = $this->Categoria->find('first', array(
			'conditions' => array(				
				'Categoria.principal' => 'S',
				'Categoria.situacao_id' => $this->SituacaoOK,
				'Categoria.cliente_id' => $this->DadosArray['Cliente']['id']
			),
			'limit' => 1
		));

		if(!empty($categoria['Categoria']['id']))
		{
			$this->DadosArray['Cliente']['menu_principal'] = $categoria['Categoria']['nome'];
			$this->DadosArray['Cliente']['categoria_id'] = $categoria['Categoria']['id'];
		}
		
		$this->EncodeReturn();		
	}


	##buscando dados de cliente
	public function find()
	{				

		if(empty($this->request->data['id_cliente']))
		{
			$this->Return = false;
			$this->Message = 'Informar id do cliente';
			$this->EncodeReturn();			
		}

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
			$this->Message = 'Cliente não encontrado';
			$this->Return = false;
		}

		$this->EncodeReturn();		
	}

	##funcao para buscar cep
	public function find_cep()
	{
		if(empty($this->request->data['cep']))
		{
			$this->Message = 'Informar cep para ser consultado';
			$this->Return = false;
		}

		$reg = simplexml_load_file("http://cep.republicavirtual.com.br/web_cep.php?formato=xml&cep=" . $this->request->data['cep']);
 		
		if(empty($reg->cidade))
		{
			$this->Message = 'Informar um CEP Válido';
			$this->Return = false;
			$this->EncodeReturn();
		}
		else
		{		
			$this->DadosArray['uf'] = $reg->uf;
			$this->DadosArray['cidade'] = $reg->cidade;
			$this->DadosArray['bairro'] = $reg->bairro;
			$this->DadosArray['logradouro'] = $reg->tipo_logradouro . ' ' . $reg->logradouro;

			##calculando distancia entre enderecos
			if(!empty($this->request->data['calcula_distancia']))
			{
				##endereco informado pelo usuario
				$enderecoUsuario = trim($reg->cidade).' - '.trim($reg->uf).', '.trim($this->request->data['cep']);
								   								
				##enderedo de cliente
				$this->Cliente->unbindModel(array('belongsTo' => array('UsuarioSabore', 'Mensalidade', 'Situacao')));		
				$this->Cliente->unbindModel(array('hasMany' => array('Categoria')));		
				$cliente = $this->Cliente->find('first', array(
				    'conditions' => array(		        
				        'Cliente.id' => $this->request->data['cliente_id'],		        
				    )		    
				));

				$enderecoCliente = $cliente['Cliente']['cidade'].' - '.$cliente['Cliente']['estado'].', '.$cliente['Cliente']['cep'];
				
				##calculando distancia entre ceps
				$xml = simplexml_load_file("http://maps.google.com/maps/api/directions/xml?sensor=false&origin=".$enderecoUsuario."&destination=".$enderecoCliente."");
				
				if($xml->status == 'OK')
				{
					$KM = $xml->route->leg->distance->value/1000;
					$KMCliente = floatval($cliente['Cliente']['km']);
					
					if($KMCliente < $KM)
					{
						$this->Message = 'Infelizmente seu endereço não recebe entrega de nosso site';
						$this->Return = false;
						$this->EncodeReturn();	
					}

				}
				else
				{
					$this->Message = 'Ocorreu um erro no calculo de seu endereço, Tente novamente';
					$this->Return = false;
					$this->EncodeReturn();	
				}		
			}

			$this->EncodeReturn();	
		}	
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
