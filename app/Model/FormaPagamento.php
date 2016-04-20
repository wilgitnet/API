<?php
App::uses('AppModel', 'Model');
/**
 * FormaPagamento Model
 *
 * @property Situacao $Situacao
 * @property FormaPagamentoCliente $FormaPagamentoCliente
 * @property Pedido $Pedido
 */
class FormaPagamento extends AppModel {


	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Situacao' => array(
			'className' => 'Situacao',
			'foreignKey' => 'situacao_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'FormaPagamentoCliente' => array(
			'className' => 'FormaPagamentoCliente',
			'foreignKey' => 'forma_pagamento_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Pedido' => array(
			'className' => 'Pedido',
			'foreignKey' => 'forma_pagamento_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
