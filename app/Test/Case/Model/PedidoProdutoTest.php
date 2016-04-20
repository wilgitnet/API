<?php
App::uses('PedidoProduto', 'Model');

/**
 * PedidoProduto Test Case
 */
class PedidoProdutoTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.pedido_produto',
		'app.pedido',
		'app.produto'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PedidoProduto = ClassRegistry::init('PedidoProduto');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->PedidoProduto);

		parent::tearDown();
	}

}
