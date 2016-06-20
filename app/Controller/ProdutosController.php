<?php
App::uses('AppController', 'Controller'); 
/**
 * Produtos Controller
 * 
 * @property Produto $Produto
 * @property PaginatorComponent $Paginator
 */
class ProdutosController extends AppController {


	##buscando produtos da home
	public function home()
	{
		$ProdutosAleatorios = array();
		$destaques = array();
		$QtdAleatorio = 6;

		if(empty($this->request->data['categoria_id']))
		{
			$this->Return = false;
			$this->Message = 'Informar categoria do produto';
			$this->EncodeReturn();
		}

		##realizar aqui busca por produtos em destaque
		$this->Produto->unbindModel(array('belongsTo' => array('Categoria', 'Situacao')));				
		$destaques = $this->Produto->find('all', array(			
		    	'conditions' => array(		        
		        'Produto.categoria_id' => $this->request->data['categoria_id'],
		        'Produto.destaque' => 'S',
		        'Produto.situacao_id' => $this->SituacaoOK
		    ),
		    'limit' => 3		    
		));	

		##verifica quantos aleatorios vai buscar de acordo com a quantidade de destaques para montar grids com 6 produtos
		$restQtd = $QtdAleatorio - count($destaques);

		##busca produtos aleatorios
		$this->Produto->unbindModel(array('belongsTo' => array('Categoria', 'Situacao')));		
		$aleatorios = $this->Produto->find('all', array(			
		    'conditions' => array(		        
		        'Produto.categoria_id' => $this->request->data['categoria_id'],
		        'Produto.destaque <> ' => 'S',
		        'Produto.situacao_id' => $this->SituacaoOK
		    ),		    
		));	

		##obrigatorio ter no minimo 6 produtos cadastrados
		if(count($aleatorios) < $restQtd)
		{
			$this->Return = false;
			$this->Message = 'Nenhum produto encontrado. Inserir produtos no site';
			$this->EncodeReturn();			
		}

		##sorteando array de produtos retornados sem destaque
		$SortAleatorios = array_rand($aleatorios, $restQtd);
		foreach($SortAleatorios as $key => $value)
		{						
			$ProdutosAleatorios[] = $aleatorios[$value];			
		}

		##inserindo aleatorios em destaques se o destaque não completar 3
		$u = 0;
		for ($i=count($destaques); $i < 3 ; $i++) 
		{ 
			$destaques[] = $ProdutosAleatorios[$u];
			unset($ProdutosAleatorios[$u]);
			$u++;
		}		
				
		$this->DadosArray['aleatorios'] = $ProdutosAleatorios;
		$this->DadosArray['destaques']  = $destaques;		
		$this->EncodeReturn();		
	}

	##buscando produtos de uma categoria e categorias validas
	public function find()
	{
		$carrinho = array();
		if(empty($this->request->data['categoria_placeholder']) || empty($this->request->data['id_cliente']))
		{
			$this->Return = false;
			$this->Message = 'Informar categoria e id do cliente';
			$this->EncodeReturn();		
		}

		$this->Produto->unbindModel(array('belongsTo' => array('Situacao')));	
		$produtos = $this->Produto->find('all', array(
			'conditions' => array(
					'Categoria.placeholder' => $this->request->data['categoria_placeholder'],
					'Produto.situacao_id' => $this->SituacaoOK
				),
			'order' => "Produto.nome ASC"
		));
		
		if(empty($produtos[3]['Produto']))
		{
			$this->Return = false;
			$this->Message = 'Essa categoria não tem 4 produtos cadastrados';
			$this->EncodeReturn();	
		}

		$categorias = $this->Produto->query(sprintf('SELECT Categorias.id, Categorias.nome, Categorias.placeholder FROM categorias Categorias where cliente_id = %d and (Select count(*) FROM produtos where categoria_id = Categorias.id) > 3', $this->request->data['id_cliente']));	
		
		$this->DadosArray['ProdutoArray'] = $produtos;
		$this->DadosArray['CategoriaArray'] = $categorias;
		$this->EncodeReturn();	
	}

	##verifica se um produto é válido e retorna com array com seus dados
	public function purchase()
	{
		if(empty($this->request->data['id_cliente']) || empty($this->request->data['produto_id']))
		{
			$this->Return = false;
			$this->Message = 'Informar produto e codigo do cliente';
			$this->EncodeReturn();	
		}

		$ProdutoID = $this->request->data['produto_id'];
		$ClienteID = $this->request->data['id_cliente'];

		##buscando produto informado
		$this->Produto->unbindModel(array('belongsTo' => array('Situacao')));
		$produto = $this->Produto->find('first', array(

			'conditions' => array(

				'Produto.id' => $ProdutoID,
				'Categoria.cliente_id' => $ClienteID,
				'Produto.situacao_id' => $this->SituacaoOK
			)
		));

		if(empty($produto['Produto']['id']))
		{
			$this->Return = false;
			$this->Message = 'Produto inválido';
			$this->EncodeReturn();	
		}

		##verifica se é para adicionar ao carrinho ou apenas validar produto informado
		if(!empty($this->request->data['add']))
		{
			if(!empty($this->request->data['carrinho']))
			{
				$CartSession = json_decode(base64_decode($this->request->data['carrinho']), 1);
				$CartNew 	 = $CartSession;				

				##verificando se vai add qtd de um item já no carrinho ou add um novo item								
				$i = 0;			
							
				$CartNew['item'][] = $produto;
								
				##realiza soma de valores
				$this->CountPurchase($CartNew);			
			}
			else
			{					
				$borda = 'N';				
				##definindo borda
				if($produto['Categoria']['borda'] == 'S')
				{
					$borda = 'S';					
				}

				##definindo valor de produto inicial
				$valor = $produto['Produto']['valor'];				

				##criando carrinho de compra				
				$carrinho['item'][] 		= $produto;
				$carrinho['total']  		= $valor;
				$carrinho['qtd']    		= 1;
				$carrinho['valor_cep']    	= 0;
				$carrinho['taxa']	    	= 0;
				$carrinho['status_pedido']	= 1;				
				$carrinho['borda']			= $borda;
				
				$this->DadosArray['carrinho'] = $carrinho;					
			}
		}
		else
		{
			$this->DadosArray = $produto;	
		}
		
		$this->EncodeReturn();	
	}

	##deletando um produto de compra
	public function delete_product_purchase()
	{
		if(empty($this->request->data['id_cliente']) || empty($this->request->data['produto_id']))
		{
			$this->Return = false;
			$this->Message = 'Informar produto e codigo do cliente';
			$this->EncodeReturn();	
		}

		$CartSession = json_decode(base64_decode($this->request->data['carrinho']), 1);

		$i = 0;				
		foreach ($CartSession['item'] as $item) 
		{
			if($item['Produto']['id'] == $this->request->data['produto_id'] && $i == $this->request->data['indice'])
			{
				unset($CartSession['item'][$i]);				
			}

			$i++;
		}		

		sort($CartSession['item']);	

		##realiza soma de valores
		$this->CountPurchase($CartSession);	
		$this->EncodeReturn();	
	}


	public function valid_purchase($verify=false)
	{
		if(empty($this->request->data['id_cliente']) || empty($this->request->data['carrinho']))
		{	
			$this->Return = false;
			$this->Message = 'Dados inválidos';
			$this->EncodeReturn();
		}

		$CartSession = json_decode(base64_decode($this->request->data['carrinho']), 1);

		$meiaBroto 		= 0;
		$meia     		= 0;
		$inteira   		= 0;
		$inteiraBroto   = 0;
		foreach($CartSession['item'] as $item)
		{
			if(!empty($item['Produto']['usuario_metade']) && !empty($item['Produto']['usuario_broto']))
			{
				$meiaBroto++;
			}

			if(!empty($item['Produto']['usuario_metade']) && empty($item['Produto']['usuario_broto']))
			{
				$meia++;
			}

			if($item['Categoria']['borda'] == 'S' && empty($item['Produto']['usuario_broto']) && empty($item['Produto']['usuario_metade']))
			{
				$inteira++;
			}

			if($item['Categoria']['borda'] == 'S' && !empty($item['Produto']['usuario_broto']) && empty($item['Produto']['usuario_metade']))
			{
				$inteiraBroto++;
			}
		}
		
		##quantide de produtos completos
		$CartSession['qtd_produto_completo'] = $inteira + $inteiraBroto;

		if($meiaBroto % 2 == 0)
		{		
			$div = $meiaBroto / 2;		
			$CartSession['qtd_produto_completo'] = $div + $CartSession['qtd_produto_completo'];			
		}
		else
		{
			if(!$verify)
			{
				$this->Return = false;
				$this->Message = 'Favor completar a outra metade de sua pizza broto';
				$this->EncodeReturn();	
			}
			
		}

		if($meia % 2 == 0)
		{
			$div = $meia / 2;			
			$CartSession['qtd_produto_completo'] = $div + $CartSession['qtd_produto_completo'];			
		}
		else
		{
			if(!$verify)
			{
				$this->Return = false;
				$this->Message = 'Favor completar a outra metade de sua pizza';
				$this->EncodeReturn();	
			}			
		}

		if(!$verify)
		{
			$this->DadosArray['carrinho'] = $CartSession;
			$this->EncodeReturn();		
		}
		else
		{
			return $CartSession;
		}
		
	}

	##meia ou inteira
	public function half()
	{

		if(empty($this->request->data['id_cliente']) || empty($this->request->data['produto_id']) || emptY($this->request->data['inteira']))
		{
			$this->Return = false;
			$this->Message = 'Informar produto e codigo do cliente e tipo (inteira ou meia)';
			$this->EncodeReturn();	
		}

		$ProdutoID = $this->request->data['produto_id'];
		$ClienteID = $this->request->data['id_cliente'];		

		##buscando produto informado
		$this->Produto->unbindModel(array('belongsTo' => array('Situacao')));
		$produto = $this->Produto->find('first', array(

			'conditions' => array(

				'Produto.id' => $ProdutoID,
				'Categoria.cliente_id' => $ClienteID,
				'Produto.situacao_id' => $this->SituacaoOK
			)
		));
		
		if(empty($produto['Produto']['id']))
		{
			$this->Return = false;
			$this->Message = 'Produto inválido';
			$this->EncodeReturn();	
		}

		$CartSession = json_decode(base64_decode($this->request->data['carrinho']), 1);
		//sort($CartSession['item']);		

		##alterando valor de pizza
		$i = 0;		
		foreach($CartSession['item'] as $item)
		{
			if($item['Produto']['id'] == $produto['Produto']['id'] && $i == $this->request->data['indice'])
			{			
				##pizza inteira
				if($this->request->data['inteira'] == 'true')
				{					
					if(!empty($this->request->data['meia']))
					{
						unset($CartSession['item'][$i]['Produto']['usuario_metade']);
					}

					if(!empty($this->request->data['broto']))
					{
						unset($CartSession['item'][$i]['Produto']['usuario_broto']);
					}

					$CartSession['item'][$i]['Produto']['valor'] = $produto['Produto']['valor'];

					if(!empty($CartSession['item'][$i]['Produto']['usuario_metade']))
					{
						$CartSession['item'][$i]['Produto']['valor'] = $produto['Produto']['valor_metade'];
					}

					if(!empty($CartSession['item'][$i]['Produto']['usuario_broto']))
					{
						$CartSession['item'][$i]['Produto']['valor'] = $produto['Produto']['valor_mini'];
					}
					
				}

				##meia pizza ou broto
				else
				{
					##meia pizza
					if(!empty($this->request->data['meia']))
					{
						if($produto['Produto']['metade'] == 'S')
						{
							$CartSession['item'][$i]['Produto']['valor'] = $produto['Produto']['valor_metade'];
							$CartSession['item'][$i]['Produto']['usuario_metade'] = 'S';
						}

					}			

					##broto
					if(!empty($this->request->data['broto']))
					{
						if($produto['Produto']['mini'] == 'S')
						{
							$CartSession['item'][$i]['Produto']['valor'] = $produto['Produto']['valor_mini'];
							$CartSession['item'][$i]['Produto']['usuario_broto'] = 'S';
						}
					}	

					if(!empty($CartSession['item'][$i]['Produto']['usuario_broto']) && !empty($CartSession['item'][$i]['Produto']['usuario_metade']))
					{
						$CartSession['item'][$i]['Produto']['valor'] = $produto['Produto']['valor_mini_metade'];						
					}		
				}
			}
			
			$i++;
		}

		##realiza soma de valores
		$this->CountPurchase($CartSession);			
		$this->EncodeReturn();	
	}

	##borda recheada
	public function edge()
	{
		##buscando valor da borda
		$CartSession = json_decode(base64_decode($this->request->data['carrinho']), 1);
		$SearchEdge  = true;
		$ValorBorda  = 0;

		foreach ($CartSession['item'] as $item) 
		{
			if($SearchEdge && $this->request->data['borda'] == 'true')
			{
				if($item['Categoria']['borda'] == 'S')
				{					
					$ValorBorda = $item['Categoria']['valor_borda'];
					$SearchEdge = false;
				}
			}
		}
		
		##buscando quantidade de produtos
		$array = $this->valid_purchase(true);
		$qtd_produto_completo = $array['qtd_produto_completo'];		

		if($this->request->data['borda'] == 'true')
		{			
			$CartSession['valor_borda'] = $ValorBorda * $qtd_produto_completo;		
			$CartSession['total'] 		= $CartSession['total'] + $CartSession['valor_borda'];		

		}	
		else
		{			
			$CartSession['total'] 		= $CartSession['total'] - $CartSession['valor_borda'];
			$CartSession['valor_borda'] = 0;		
		}
		
		$this->DadosArray['carrinho'] = $CartSession;	
		$this->EncodeReturn();	
	}

	##somando valores totais
	private function CountPurchase($CartNew)
	{
		##zerando valores para nova soma
		$CartNew['total'] 	  = 0;
		$CartNew['qtd']		  = 0;
		$borda 				  = 'N';
		$valor_borda		  = 0;
		$qtd_produto_completo = 0;

		if(!empty($CartNew['valor_borda']))
		{
			if($CartNew['valor_borda'] > 0)
			{
				$array = $this->valid_purchase(true);
				$qtd_produto_completo = $array['qtd_produto_completo'];	
			}
		}
					
		foreach($CartNew['item'] as $item)
		{
			$valor = $item['Produto']['valor'];			

			if($item['Categoria']['borda'] == 'S')
			{
				$borda = 'S';
				$valor_borda = $item['Categoria']['valor_borda'] * $qtd_produto_completo;
			}
				

			$CartNew['total'] = $CartNew['total'] + $valor;			
			$CartNew['qtd']	  = $CartNew['qtd'] + 1;					
			$CartNew['borda'] = $borda;
		}	

		$CartNew['total'] 	  = $CartNew['total'] + $valor_borda;
		$this->DadosArray['carrinho'] = $CartNew;	
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Produto->exists($id)) {
			throw new NotFoundException(__('Invalid produto'));
		}
		$options = array('conditions' => array('Produto.' . $this->Produto->primaryKey => $id));
		$this->set('produto', $this->Produto->find('first', $options));
	}


	public function list()
	{
		$produtos = array();

		$this->Produto->unbindModel(array('belongsTo' => array('Situacao')));				
		##monta array que verifica se já existe uma categoria cadastrada no sistema
		$produtos = $this->Produto->find('all', 
				array('conditions' => array(							
							 $this->request->data['cliente_id'],
						)
					)
			);

		$this->DadosArray = $produtos;
		$this->EncodeReturn();
	}

public function find_first()
	{
		$produtos = array();

		$this->Produto->unbindModel(array('belongsTo' => array()));				
		##se não foi enviado id retorna erro
		if(empty($this->request->data['id']))
		{
			$this->Message = 'ID de produto não foi informado';
			$this->Return = false;
			$this->EncodeReturn();
		}

		$produtos = $this->Produto->find('first', array(
			'conditions' => array(
					'Produto.id' => $this->request->data['id']
				)
		));

		$this->DadosArray = $produtos;
		$this->EncodeReturn();
	}
/**
 * add method
 *
 * @return void
 */
	public function add() {		
		if ($this->request->is('post')) 
		{			
			unset($this->request->data['TokenRequest']);			
			$POST = array('Produto'=>$this->request->data);			
			$this->Produto->create();

			if ($this->Produto->save($POST)) 
			{
				$this->Message = 'Produto cadastrado com sucesso';
				$this->Return = true;
			} 
			else 
			{	
				$this->Message = 'Ocorreu um Erro no seu cadastro de produto';								
				$this->Return = false;
			}
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
		if (!$this->Produto->exists($id)) {
			throw new NotFoundException(__('Invalid produto'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Produto->save($this->request->data)) {
				$this->Flash->success(__('The produto has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The produto could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Produto.' . $this->Produto->primaryKey => $id));
			$this->request->data = $this->Produto->find('first', $options);
		}
		$situacaos = $this->Produto->Situacao->find('list');
		$classes = $this->Produto->Classe->find('list');
		$this->set(compact('situacaos', 'classes'));
	}

	public function editar() {			

			##variavel que vai receber os dados para enviar p/ banco de dados
		$POST = array();

			##apagando indice de tokenrequest pois ele não existe na tabela de categorias
		unset($this->request->data['TokenRequest']);	

		$POST = array('Produto'=>$this->request->data);	
		if ($this->Produto->save($POST)) 
		{
			$this->Message = 'Produto editado com sucesso';
			$this->Return = true;	
		} 

		else 
		{
			$this->Message = 'Ocorreu um erro na edição de seu produto.';
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
	public function delete($id = null) {
		$this->Produto->id = $id;
		if (!$this->Produto->exists()) {
			throw new NotFoundException(__('Invalid produto'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Produto->delete()) {
			$this->Flash->success(__('The produto has been deleted.'));
		} else {
			$this->Flash->error(__('The produto could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function remove() {

		$this->Produto->id = $this->request->data['id'];

		##verifica se categoria existe
		if (!$this->Produto->exists()) 
		{
			$this->Message = 'Produto não existe';
			$this->Return = false;
		}

		$this->request->allowMethod('post', 'delete');

		##faz o delete
		if ($this->Produto->delete()) 
		{
			$this->Message = 'Produto excluido com sucesso';
			$this->Return = true;
		} 
		else 
		{
			$this->Message = 'Ocorreu um erro na exclusão do produto';
			$this->Return = false;
		}		

		$this->EncodeReturn();
	}


}

