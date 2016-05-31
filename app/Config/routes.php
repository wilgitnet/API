<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	

	##rotas de cliente
	Router::connect('/cliente/dominio', array('controller' => 'clientes', 'action' => 'find_dominio'));
	Router::connect('/cliente/buscar', array('controller' => 'clientes', 'action' => 'find'));
	Router::connect('/cliente/cep', array('controller' => 'clientes', 'action' => 'find_cep'));
	Router::connect('/cliente/open-close', array('controller' => 'clientes', 'action' => 'open_close'));

	##rotas de usuario
	Router::connect('/usuario/salvar', array('controller' => 'usuarios', 'action' => 'add'));
	Router::connect('/usuario/editar', array('controller' => 'usuarios', 'action' => 'edit'));
	Router::connect('/usuario/login', array('controller' => 'usuarios', 'action' => 'login'));
	Router::connect('/usuario/novasenha_token', array('controller' => 'usuarios', 'action' => 'password_token'));
	Router::connect('/usuario/troca_senha', array('controller' => 'usuarios', 'action' => 'new_password'));

	##produtos
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));	
	Router::connect('/produtos/home', array('controller' => 'produtos', 'action' => 'home'));
	Router::connect('/produtos/buscar', array('controller' => 'produtos', 'action' => 'find'));
	Router::connect('/produtos/validar', array('controller' => 'produtos', 'action' => 'purchase'));
	Router::connect('/produtos/adicionar', array('controller' => 'produtos', 'action' => 'purchase'));
	Router::connect('/produtos/excluir', array('controller' => 'produtos', 'action' => 'delete_product_purchase'));
	Router::connect('/produtos/meia-inteira', array('controller' => 'produtos', 'action' => 'half'));
	Router::connect('/produtos/broto', array('controller' => 'produtos', 'action' => 'half'));
	Router::connect('/produtos/borda', array('controller' => 'produtos', 'action' => 'edge'));
	Router::connect('/produtos/validar-pedido', array('controller' => 'produtos', 'action' => 'valid_purchase'));
	Router::connect('/produtos/cadastrar', array('controller' => 'produtos', 'action' => 'add'));

	##pedidos
	Router::connect('/pedidos/finalizar', array('controller' => 'pedidos', 'action' => 'add'));
	Router::connect('/pedidos/validar', array('controller' => 'pedidos', 'action' => 'find'));
	Router::connect('/pedidos/acompanhamento', array('controller' => 'pedidos', 'action' => 'accompaniment'));
	Router::connect('/pedidos/buscar', array('controller' => 'pedidos', 'action' => 'search'));


	##categorias
	Router::connect('/categoria/cadastrar', array('controller' => 'categorias', 'action' => 'add'));

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
