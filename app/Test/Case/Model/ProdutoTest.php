<?php
App::uses('Produto', 'Model');

/**
 * Produto Test Case
 */
class ProdutoTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
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
		$this->Produto = ClassRegistry::init('Produto');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Produto);

		parent::tearDown();
	}

}
