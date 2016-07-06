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

	##verifica se pizzaria esta aberta ou fechada
	public function open_close()
	{

		$this->Cliente->unbindModel(array('belongsTo' => array('UsuarioSabore', 'Mensalidade', 'Situacao')));		
		$this->Cliente->unbindModel(array('hasMany' => array('Categoria')));		
		$open = $this->Cliente->find('first', array(
			'conditions' => array(
				'Cliente.id' => $this->request->data['id_cliente']

				),
			'fields' => array('Cliente.open')
		));
		
		$this->DadosArray = $open;
		$this->EncodeReturn();
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
			$this->DadosArray['Cliente']['placeholder'] = $categoria['Categoria']['placeholder'];
			$this->DadosArray['Cliente']['categoria_id'] = $categoria['Categoria']['id'];		

			$categorias = $this->Cliente->query(sprintf('SELECT Categorias.id, Categorias.nome, Categorias.placeholder FROM categorias Categorias where cliente_id = %d and (Select count(*) FROM produtos where categoria_id = Categorias.id) > 3', $this->DadosArray['Cliente']['id']));

			$this->DadosArray['CategoriaArray'] = $categorias;	
		}

		$banner = $this->Cliente->query(sprintf("SELECT * FROM cliente_banners where id_cliente = %d", $this->DadosArray['Cliente']['id']));
		$this->DadosArray['banner_info'] = $banner;
		
		$this->EncodeReturn();		
	}
	public function deletar() {

		$this->Cliente->id = $this->request->data['id'];

		##verifica se categoria existe
		if (!$this->Cliente->exists()) 
		{
			$this->Message = 'Cliente não existe';
			$this->Return = false;
		}

		$this->request->allowMethod('post', 'delete');

		##faz o delete
		if ($this->Cliente->delete()) 
		{
			$this->Message = 'Cliente excluido com sucesso';
			$this->Return = true;
		} 
		else 
		{
			$this->Message = 'Ocorreu um erro na exclusão do Cliente';
			$this->Return = false;
		}		

		$this->EncodeReturn();
	}
	public function find_first()
	{
		$clientes = array();

		$this->Cliente->unbindModel(array('belongsTo' => array()));				
		##se não foi enviado id retorna erro
		if(empty($this->request->data['id']))
		{
			$this->Message = 'ID de cliente não foi informado';
			$this->Return = false;
			$this->EncodeReturn();
		}

		$clientes = $this->Cliente->find('first', array(
			'conditions' => array(
					'Cliente.id' => $this->request->data['id']
				)
		));

		$this->DadosArray = $clientes;
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


	public function read() 
	{
		$this->loadModel('Usuario');
		$clientes = array();
		if(empty($this->request->data['search']))
		{
			$clientes = $this->Usuario->find('all', 
				array ('conditions' => array (
					'Usuario.cliente_id' => $this->request->data['cliente_id'],


				)
			));
		}
		else
		{
			$clientes = $this->Usuario->find('all', 
				array('conditions' => array(				
				'OR' => array(

					'Usuario.nome LIKE ' => "%{$this->request->data['search']}%",
					'Usuario.telefone LIKE ' => "%{$this->request->data['search']}%",
					'Usuario.email LIKE ' => "%{$this->request->data['search']}%"
					)
				)
			)
			);
		}

	$this->DadosArray = $clientes;
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

				$this->DadosArray['KM'] = $KM;
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
 * add method
 *
 * @return void
 */
	public function cadastrar() {		
		if ($this->request->is('post')) 
		{			
			unset($this->request->data['TokenRequest']);			
			$POST = array('Cliente'=>$this->request->data);			
			$this->Cliente->create();

			if ($this->Cliente->save($POST)) 
			{
				$this->Message = 'Cadastrado de dados obteve sucesso';
				$this->Return = true;
			} 
			else 
			{	
				$this->Message = 'Ocorreu um Erro no cadastro de dados';								
				$this->Return = false;
			}
		}

		$this->EncodeReturn();	
	}

	public function editar() {			

			##variavel que vai receber os dados para enviar p/ banco de dados
		$POST = array();

			##apagando indice de tokenrequest pois ele não existe na tabela de categorias
		unset($this->request->data['TokenRequest']);	

		$POST = array('Cliente'=>$this->request->data);	
		if ($this->Cliente->save($POST)) 
		{
			$this->Message = 'Edição de dados obteve sucesso';
			$this->Return = true;	
		} 

		else 
		{
			$this->Message = 'Ocorreu um erro durante a edição de dados.';
			$this->Return = false;	
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


/**
 * create method
 *
 * @return void
 */
	public function banner_add(){

		$POST = array();
		
		if ($this->request->is('post')) 
		{										
			unset($this->request->data['TokenRequest']);			

			$this->loadModel('ClienteBanner');

			##verifica quantos banners tem cadastrado 
			$bannerQTD = $this->ClienteBanner->query(sprintf("Select count(*) as count FROM cliente_banners where id_cliente = %d", $this->request->data['id_cliente']));
			
			if($bannerQTD[0][0]['count'] > 4)
			{
				$this->Message = 'Não é possível cadastrar mais de 5 textos rotativos.';								
				$this->Return = false;
				$this->EncodeReturn();	
			}
			
			$POST = array('ClienteBanner'=>$this->request->data);	

			$this->ClienteBanner->create();	
			if ($this->ClienteBanner->save($POST)) 
			{
				$this->DadosArray['ID'] = $this->ClienteBanner->getLastInsertId();				
				$this->Message = 'Banner cadastrado com sucesso!';
				$this->Return = true;
			} 
			else 
			{	
				$this->Message = 'Ocorreu um erro no cadastro do banner.';								
				$this->Return = false;
			}
		}

		$this->EncodeReturn();	
	}



/**
 * read method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

	public function banner_list(){		
		$banners = array();
		$this->loadModel('ClienteBanner');
		$banners = $this->ClienteBanner->find('all', 
				array('conditions'=>array('ClienteBanner.id_cliente'=>$this->request->data['cliente_id'])));

		$this->DadosArray = $banners;
		$this->EncodeReturn();
	}


	public function banner_find(){

		$banner = array();
		$this->loadModel('ClienteBanner');
		$banner = $this->ClienteBanner->find('first', array(
			'conditions' => array(
					'ClienteBanner.cliente_id' => $this->request->data['cliente_id'],
					'ClienteBanner.id' => $this->request->data['id']
				)
		));
		
		##buscando imagem do banner
		$IMGBANNER = $this->ClienteBanner->query(sprintf("Select banner1 FROM clientes where id = %d", $this->request->data['cliente_id'])); 

		$this->DadosArray = $banner;
		$this->DadosArray['img_banner'] = $IMGBANNER[0]['clientes']['banner1'];
		$this->EncodeReturn();
	}

	public function banner_img_edit()
	{
		if(!empty($this->request->data['banner1']) && !empty($this->request->data['cliente_id']))
		{	
			$this->request->data['banner1'] = $this->DIRUPLOAD.$this->request->data['banner1'];	

			$update = $this->Cliente->query(sprintf("Update clientes set banner1 ='%s' WHERE id = %d", $this->request->data['banner1'], $this->request->data['cliente_id']));	
			
			$this->EncodeReturn();		
		}
	}

	public function banner_find_img()
	{
		if(!empty($this->request->data['cliente_id']))
		{
			##buscando imagem do banner
			$IMGBANNER = $this->Cliente->query(sprintf("Select banner1 FROM clientes where id = %d", $this->request->data['cliente_id'])); 
			$this->DadosArray['img_banner'] = $IMGBANNER[0]['clientes']['banner1'];
			$this->EncodeReturn();		
		}
	}

/**
 * update method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function banner_edit() {			

		$POST = array();

		unset($this->request->data['TokenRequest']);

		$this->loadModel('ClienteBanner');
			
		$POST = array('ClienteBanner'=>$this->request->data);	
		if ($this->ClienteBanner->save($POST)) 
		{
			$this->Message = 'Banner editado com sucesso';
			$this->Return = true;	
		} 

		else 
		{
			$this->Message = 'Ocorreu um erro na edição do Banner.';
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
	public function banner_delete(){

		$this->loadModel('ClienteBanner');
		$this->ClienteBanner->id = $this->request->data['id'];
		$this->request->allowMethod('post', 'delete');		

		if ($this->ClienteBanner->delete()) 
		{
			$this->Message = 'Banner excluído com sucesso!';
			$this->Return = true;
		} 
		else 
		{
			$this->Message = 'Ocorreu um erro na exclusão do Banner';
			$this->Return = false;
		}		

		$this->EncodeReturn();
	}


}
