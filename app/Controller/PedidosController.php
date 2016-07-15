<?php
App::uses('AppController', 'Controller');
/**
 * Pedidos Controller
 *
 * @property Pedido $Pedido
 * @property PaginatorComponent $Paginator
 */
class PedidosController extends AppController {

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
		$this->Pedido->recursive = 0;
		$this->set('pedidos', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */


public function listar() 
{
	$pedido = array();

	$this->Pedido->unbindModel(array('belongsTo' => array('Situacao')));				

	if(empty($this->request->data['search']))
	{
			##monta array que verifica se já existe uma categoria cadastrada no sistema
		$pedido = $this->Pedido->find('all', array(
			'conditions' => array(
				'Pedido.cliente_id' => $this->request->data['cliente_id'],
				'Pedido.situacao_pedido_id <> '=> array('1', '2', '3', '4', '5')
				)
			));
	}
	else
	{
		$pedido = $this->Pedido->find('all', 

			array('conditions' => array(
				'Pedido.cliente_id' => $this->request->data['cliente_id'],
				'Pedido.situacao_pedido_id <> '=> array('1', '2', '3', '4', '5'),
				'OR' => array(
					'Pedido.valor_total LIKE ' => "%{$this->request->data['search']}%",
					'FormaPagamento.descricao LIKE ' => "%{$this->request->data['search']}%",
					'Usuario.nome LIKE ' => "%{$this->request->data['search']}%",
					'Pedido.id LIKE ' => "%{$this->request->data['search']}%",
					'Usuario.email LIKE ' => "%{$this->request->data['search']}%"
					)
				)
			)
			);				
	}



	$this->DadosArray = $pedido;
	$this->EncodeReturn();
}

public function in_progress()
{
	$pedidos = array();
	$this->Pedido->unbindModel(array('belongsTo' => array('')));    

	if(empty($this->request->data['search']))
	{
   ##monta array que verifica se já existe uma categoria cadastrada no sistema
		$pedidos = $this->Pedido->find('all', array(
			'conditions' => array(
				'Pedido.cliente_id' => $this->request->data['cliente_id'],
				'Pedido.situacao_pedido_id <> '=> array('6', '7', '8')     
				)
			));
	}
	else
	{
		$pedidos = $this->Pedido->find('all', 
			array('conditions' => array(
				'Pedido.cliente_id' => $this->request->data['cliente_id'],
				'Pedido.situacao_pedido_id <> '=> array('6', '7', '8'),      
				'OR' => array(
					'Pedido.valor_total LIKE ' => "%{$this->request->data['search']}%",
					'SituacaoPedido.descricao LIKE ' => "%{$this->request->data['search']}%",
					'Usuario.nome LIKE ' => "%{$this->request->data['search']}%",
					'Usuario.email LIKE ' => "%{$this->request->data['search']}%"
					)
				)
			)
			);   
	}
	$this->DadosArray = $pedidos;
	$this->EncodeReturn();
}
public function situacao_atualizar(){			
	$this->loadModel('Cliente');
	$POST = array();	
	$cliente = array();
	$valortaxa = array();	
	$ped_val = array();
	$ped_low = '0.00';
	$POST = array('Pedido'=>$this->request->data);

	if ($this->request->data['situacao_pedido_id'] <> '8' and $this->request->data['situacao_pedido_id'] <> '1' and $this->request->data['situacao_pedido_id'] <> '2') 
	{
		//busca o pedido, e o cliente, pra pegar os créditos e pá 
		$cliente = $this->Cliente->find('first', array(
			'conditions' => array(
				'Cliente.id' => $this->request->data['cliente_id'])));
		$ped_val = $this->Pedido->find('first', array(
			'conditions' => array(
				'Pedido.id' => $this->request->data['id'])));

		//Verifica se tem crédito disponível
		if ($ped_val['Pedido']['valor_taxa'] <= $cliente['Cliente']['credito']) 
		{
	 		//Faz a subtração, do valor da taxa do pedido, com o Crédito que o cliente tem disponivel
			$valortaxa = $cliente['Cliente']['credito'] - $ped_val['Pedido']['valor_taxa'];
			$valortaxa2 = array ('credito' => $valortaxa, 'id'=>$cliente ['Cliente']['id'] );
			$this->Cliente->save($valortaxa2);
			//Faz a atualização dos créditos do cliente. EM CIMA 

			//Aqui vamos tirar o Valor da taxa do pedido, para não somar todas as vezes que passar pelo código
			$ped_low2 = array('valor_taxa' => $ped_low, 'id' => $ped_val['Pedido']['id'], 'cliente_id'=>$cliente['Cliente']['id']);
			$this->Pedido->save($ped_low2);
		}
		else
		{
			$this->Message = 'Ocorreu um erro na atualização do status.';
			$this->Return = false;	
			$this->EncodeReturn();
		}	

	}	
	if ($this->Pedido->save($POST)) 
	{
		$this->Message = 'Pedido atualizado com sucesso';
		$this->Return = true;	
	} 

	else 
	{
		$this->Message = 'Ocorreu um erro na atualização do status.';
		$this->Return = false;	
	}

	$this->EncodeReturn();	
}

	public function view_request(){

		$pedido = array();

		$pedido = $this->Pedido->find('first', array(
			'conditions' => array(
					'Pedido.id' => $this->request->data['id']
				)
		));

		if(!empty($pedido['PedidoProduto']))
		{
			$this->loadModel('Produto');
			$i = 0;

			foreach($pedido['PedidoProduto'] as $row)
			{								
				$produto = $this->Produto->find('first', array(
					'conditions' => array(
							'Produto.id' => $row['produto_id']
						)
				));
				
				$pedido['PedidoProduto'][$i]['dados_produto'] = $produto;
				$i++;
			}			

		}
		
		$this->DadosArray = $pedido;
		$this->EncodeReturn();
	}


	public function situation()
	{
		$situacao = array();
		$this->loadModel('SituacaoPedido');
		$this->SituacaoPedido->unbindModel(array('belongsTo' => array('Pedido')));
		$situacao = $this->SituacaoPedido->find('all');
		$this->DadosArray = $situacao;
		$this->EncodeReturn();
	}

	public function listar_detalhes()
	{
		
		$pedido = array();

		$this->Pedido->unbindModel(array());				
		##monta array que verifica se já existe uma categoria cadastrada no sistema
		$pedido = $this->Pedido->find('first', array(
			'conditions' => array(
					'Pedido.id' => $this->request->data['id']
				)
		));
		if(!empty($pedido['PedidoProduto']))
		{
			$this->loadModel('Produto');
			$i = 0;
			
			foreach($pedido['PedidoProduto'] as $row)
			{				
				$produto = $this->Produto->find('first', array(
					'conditions' => array(
							'Produto.id' => $row['produto_id']
						)
				));
				
				$pedido['PedidoProduto'][$i]['dados_produto'] = $produto;
				$i++;
			}			

		}
		
		$this->DadosArray = $pedido;
		$this->EncodeReturn();
	}

	public function find()
	{		
		if(empty($this->request->data['id_pedido']) || empty($_POST['id_usuario']))
		{
			$this->Message = 'Ocorreu um erro na sua solicitação. Tente novamente. Informar id do pedido e id do usuario';
			$this->Return = false;
			$this->EncodeReturn();
		}

		$pedido = $this->Pedido->find('first', array(

			'conditions' => array(			
				'Pedido.id' => $this->request->data['id_pedido'], 
				'Pedido.cliente_id' => $this->request->data['id_cliente'],
				'Pedido.usuario_id' => $this->request->data['id_usuario']
				)
		));
		
		if(empty($pedido['Pedido']['id']))
		{
			$this->Message = 'Ocorreu um erro na sua solicitação. Tente novamente (Pedido não encontrado)';
			$this->Return = false;
			$this->EncodeReturn();
		}
		
		$this->loadModel('Produto');
		foreach ($pedido['PedidoProduto'] as $key => $array) 
		{
			$produto = $this->Produto->find('first', array(

				'conditions'=>array(
					'Produto.id' => $array['produto_id']
					)
			));

			$pedido['produtos'][] = $produto;
		}
		
		$this->DadosArray = $pedido;
		$this->EncodeReturn();
	}

	public function search()
	{
		$PedidosArray = array();
		if(empty($this->request->data['id_usuario']))
		{
			$this->Message = 'Ocorreu um erro na sua solicitação. Tente novamente (Usuário não encontrado)';
			$this->Return = false;
			$this->EncodeReturn();
		}

		$pedidos = $this->Pedido->find('all', array(
			'conditions' => array(							
				'Pedido.cliente_id' => $this->request->data['id_cliente'],
				'Pedido.usuario_id' => $this->request->data['id_usuario']				
				),
			'order' => "Pedido.id DESC"
		));
		
		$this->loadModel('Produto');
		foreach($pedidos as $pedido)
		{
			foreach ($pedido['PedidoProduto'] as $key => $array) 
			{
				$produto = $this->Produto->find('first', array(

				'conditions'=>array(
					'Produto.id' => $array['produto_id']
					)
				));

				$pedido['produtos'][] = $produto;
			}
			
			$PedidosArray[]	= $pedido;
		}

		$this->DadosArray = $PedidosArray;
		$this->EncodeReturn();
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		if(empty($this->request->data['carrinho']))
		{
			$this->Return = false;
			$this->Message = 'Enviar pedido para finalizar';
			$this->EncodeReturn();	
		}

		$CartSession = json_decode(base64_decode($this->request->data['carrinho']), 1);

		$valor_borda = 0;
		if(!empty($CartSession['valor_borda']))
			$valor_borda = $CartSession['valor_borda'];

		$troco = $this->request->data['troco'];
		if(empty($this->request->data['troco']))
			$troco = 0;

		$maquina = 'N';
		if($this->request->data['tipo_pagamento']!=3)
			$maquina = 'S';

		 ##calculando taxa e valores totais do pedido
		 $percentual = $this->percentual / 100.0; 		 
		 $valor_total = $CartSession['total'] + $CartSession['valor_cep'];		 		 
		 $valor_total_antigo = $valor_total;
		 $valor_total = $valor_total + ($percentual * $valor_total);
		 $valor_percentual = $valor_total - $valor_total_antigo;
		 $valor_total_taxas = $valor_percentual + $CartSession['valor_cep'] + $valor_borda;

		##realizando primeiro insert de pedido
		$POST = array('Pedido'=>array(
					'data_pedido'=>date('Y-m-d H:i:s'),
					'usuario_id'=>$CartSession['usuario_id'],
					'acompanhamento'=>'S',
					'endereco'=>$CartSession['endereco']['endereco'],
					'numero'=>$CartSession['endereco']['numero'],
					'bairro'=>$CartSession['endereco']['bairro'],
					'cidade'=>$CartSession['endereco']['cidade'],
					'estado'=>$CartSession['endereco']['estado'],
					'complemento'=>$CartSession['endereco']['complemento'],
					'cep'=>$CartSession['endereco']['cep'],
					'situacao_pedido_id'=>2,
					'forma_pagamento_id'=> $this->request->data['tipo_pagamento'],
					'troco' => $troco,
					'maquina' => $maquina,
					'valor_borda' => $valor_borda,
					'valor_cep' => $CartSession['valor_cep'],
					'valor_taxa' => $valor_percentual,
					'valor_total' => $valor_total,
					'valor_total_taxas' => $valor_total_taxas,
					'cliente_id' => $this->request->data['cliente_id']
				));

		if ($this->request->is('post')) 
		{
			$this->Pedido->create();
			if ($this->Pedido->save($POST)) 
			{				
				$pedido_id = $this->Pedido->getLastInsertId();				
				$this->loadModel('PedidoProduto');

				foreach($CartSession['item'] as $item)
				{
					$metade = 'N';
					if(!empty($item['usuario_metade']))
						$metade = 'S';

					$broto = 'N';
					if(!empty($item['usuario_broto']))
						$broto = 'S';
											
					$POSTITEM = array('PedidoProduto'=>array(
									'pedido_id' => $pedido_id,
									'produto_id' => $item['Produto']['id'],
									'metade' => $metade,
									'broto' => $broto,
									'valor' => $item['Produto']['valor']
						));

					$this->PedidoProduto->create();
					if (!$this->PedidoProduto->save($POSTITEM)) 
					{	
						$this->Message = 'Ocorreu um Erro na geração dos itens do seu pedido. Tente novamente';								
						$this->Return = false;
						$this->EncodeReturn();		
					}
				}
			} 
			else 
			{					
				$this->Message = 'Ocorreu um Erro na geração do pedido';								
				$this->Return = false;
				$this->EncodeReturn();		
			}

			$this->Message = 'Pedido realizado com sucesso';
			$this->DadosArray['pedido_id'] = $pedido_id;
			$this->EncodeReturn();		
		}	
	}


	public function accompaniment()
	{			
		##buscando status do pedido
		$status = $this->Pedido->query(
				sprintf("Select descricao FROM situacao_pedidos where id = (Select situacao_pedido_id FROM pedidos where id = %d LIMIT 1)", $this->request->data['pedido_id']));
		
		$this->DadosArray['status'] = $status[0]['situacao_pedidos']['descricao'];
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
		if (!$this->Pedido->exists($id)) {
			throw new NotFoundException(__('Invalid pedido'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Pedido->save($this->request->data)) {
				$this->Flash->success(__('The pedido has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The pedido could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Pedido.' . $this->Pedido->primaryKey => $id));
			$this->request->data = $this->Pedido->find('first', $options);
		}
		$usuarios = $this->Pedido->Usuario->find('list');
		$situacaoPedidos = $this->Pedido->SituacaoPedido->find('list');
		$formaPagamentos = $this->Pedido->FormaPagamento->find('list');
		$this->set(compact('usuarios', 'situacaoPedidos', 'formaPagamentos'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Pedido->id = $id;
		if (!$this->Pedido->exists()) {
			throw new NotFoundException(__('Invalid pedido'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Pedido->delete()) {
			$this->Flash->success(__('The pedido has been deleted.'));
		} else {
			$this->Flash->error(__('The pedido could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function atualizar_status() {			

		$POST = array();

		unset($this->request->data['TokenRequest']);	
		$POST = array('Pedido'=>$this->request->data);	
		if ($this->Pedido->save($POST)) 
		{
			$this->Message = 'Pedido editada com sucesso';
			$this->Return = true;	
		} 

		else 
		{
			$this->Message = 'Ocorreu um erro na edição do seu pedido.';
			$this->Return = false;	
		}

		$this->EncodeReturn();	
	}



}
