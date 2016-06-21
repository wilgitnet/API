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
	Router::connect('/cliente/editar', array('controller' => 'clientes', 'action' => 'editar'));
	Router::connect('/cliente/find_first', array('controller' => 'clientes', 'action' => 'find_first'));
	Router::connect('/cliente/cadastrar', array('controller' => 'clientes', 'action' => 'cadastrar'));
	Router::connect('/cliente/banner-listar', array('controller' => 'clientes', 'action' => 'banner_list'));
	Router::connect('/cliente/banner-cadastrar', array('controller' => 'clientes', 'action' => 'banner_add'));
	Router::connect('/cliente/banner-deletar', array('controller' => 'clientes', 'action' => 'banner_delete'));
	Router::connect('/cliente/banner-buscar', array('controller' => 'clientes', 'action' => 'banner_find'));
	Router::connect('/cliente/banner-editar', array('controller' => 'clientes', 'action' => 'banner_edit'));

	##rotas de usuario
	Router::connect('/usuario/salvar', array('controller' => 'usuarios', 'action' => 'add'));
	Router::connect('/usuario/editar', array('controller' => 'usuarios', 'action' => 'edit'));
	Router::connect('/usuario/login', array('controller' => 'usuarios', 'action' => 'login'));
	Router::connect('/usuario/novasenha_token', array('controller' => 'usuarios', 'action' => 'password_token'));
	Router::connect('/usuario/troca_senha', array('controller' => 'usuarios', 'action' => 'new_password'));

	##Rotas de Usuario_sabore
	Router::connect('/usuario_sabore/cadastrar', array('controller' => 'UsuarioSabores', 'action' => 'cadastrar'));
	Router::connect('/usuario_sabore/list', array('controller' => 'UsuarioSabores', 'action' => 'find_list'));
	Router::connect('/usuario_sabore/deletar', array('controller' => 'UsuarioSabores', 'action' => 'deletar'));
	Router::connect('/usuario_sabore/editar', array('controller' => 'UsuarioSabores', 'action' => 'editar'));
	Router::connect('/usuario_sabore/find_first', array('controller' => 'UsuarioSabores', 'action' => 'find_first'));

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
	Router::connect('/produtos/listar', array('controller' => 'produtos', 'action' => 'find_list'));
	Router::connect('/produtos/remover', array('controller' => 'produtos', 'action' => 'remove'));
	Router::connect('/produtos/editar', array('controller' => 'produtos', 'action' => 'editar'));
	Router::connect('/produtos/search', array('controller' => 'produtos', 'action' => 'find_first'));


	##pedidos
	Router::connect('/pedidos/finalizar', array('controller' => 'pedidos', 'action' => 'add'));
	Router::connect('/pedidos/validar', array('controller' => 'pedidos', 'action' => 'find'));
	Router::connect('/pedidos/acompanhamento', array('controller' => 'pedidos', 'action' => 'accompaniment'));
	Router::connect('/pedidos/buscar', array('controller' => 'pedidos', 'action' => 'search'));
	Router::connect('/pedidos/listar', array('controller' => 'pedidos', 'action' => 'listar'));

	##categorias
	Router::connect('/categoria/cadastrar', array('controller' => 'categorias', 'action' => 'add'));
	Router::connect('/categoria/editar', array('controller' => 'categorias', 'action' => 'edit'));
	Router::connect('/categoria/listar', array('controller' => 'categorias', 'action' => 'find'));
	Router::connect('/categoria/buscar', array('controller' => 'categorias', 'action' => 'find_first'));
	Router::connect('/categoria/excluir', array('controller' => 'categorias', 'action' => 'delete'));
	Router::connect('/categoria/find_categoria', array('controller' => 'categorias', 'action' => 'find_categoria'));

	##clientes
	Router::connect('/clientes/listar', array('controller' => 'clientes', 'action' => 'read'));


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
