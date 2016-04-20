<?php
App::uses('SituacaoPedido', 'Model');

/**
 * SituacaoPedido Test Case
 */
class SituacaoPedidoTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.situacao_pedido',
		'app.pedido',
		'app.usuario',
		'app.forma_pagamento',
		'app.pedido_produto',
		'app.produto',
		'app.situacao',
		'app.classe'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SituacaoPedido = ClassRegistry::init('SituacaoPedido');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SituacaoPedido);

		parent::tearDown();
	}

}
