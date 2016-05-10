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
	public function view($id = null) {
		if (!$this->Pedido->exists($id)) {
			throw new NotFoundException(__('Invalid pedido'));
		}
		$options = array('conditions' => array('Pedido.' . $this->Pedido->primaryKey => $id));
		$this->set('pedido', $this->Pedido->find('first', $options));
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
		 $valor_total_taxas = $valor_percentual + $CartSession['valor_cep'] + $CartSession['valor_borda'];

		##realizando primeiro insert de pedido
		$POST = array('Pedido'=>array(
					'data_pedido'=>date('Y/m/d h:i:s'),
					'usuario_id'=>$CartSession['usuario_id'],
					'endereco'=>$CartSession['endereco']['endereco'],
					'numero'=>$CartSession['endereco']['numero'],
					'bairro'=>$CartSession['endereco']['bairro'],
					'cidade'=>$CartSession['endereco']['cidade'],
					'estado'=>$CartSession['endereco']['estado'],
					'complemento'=>$CartSession['endereco']['complemento'],
					'cep'=>$CartSession['endereco']['cep'],
					'situacao_pedido_id'=>1,
					'forma_pagamento_id'=> $this->request->data['tipo_pagamento'],
					'troco' => $troco,
					'maquina' => $maquina,
					'valor_borda' => $CartSession['valor_borda'],
					'valor_cep' => $CartSession['valor_cep'],
					'valor_taxa' => $valor_percentual,
					'valor_total' => $valor_total,
					'valor_total_taxas' => $valor_total_taxas
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
			$this->EncodeReturn();		
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
}
