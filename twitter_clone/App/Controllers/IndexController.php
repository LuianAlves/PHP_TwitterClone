<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	// View Index
	public function index() {

		$this->view->login = isset($_GET['login']) ? $_GET['login'] : ''; // Criar um atributo dinamico $login, utilizando o GET para recuperar o parametro da url caso for compativel com ?login caso contrario atribuir valor vazio
		$this->render('index');
	}

	// View InscreverSe
	public function inscreverse() {
		$this->view->usuario = array (  // Associando o Objeto [View] á um novo atributo chamado usuario para criar um array
			'nome' => '',   // Este array aqui serve deixar os campos limpos quando acessar a view[inscreverse] diretamente
			'email' => '', // pois utilizamos o value para deixar os campos para correção quando houver erro
			'senha' => ''
		); 

		$this->view->erroCadastro = false; // False para que não cai no parametro quando o cadastro não tiver sucesso
		$this->render('inscreverse');
	}

	// Registrando um Usuário
	public function registrar() {

		// Receber os dados do formulário
			// Instanciar o Objeto [Usuario] utilizando o Container onde Faz a conexao com o Banco
			$usuario = Container::getModel('Usuario');
				// Recuperar o Objeto já instanciado
				$usuario->__set('nome', $_POST['nome']); // Setando os valores recebidos via post dentro do Objeto já instanciado [Usuario]
				$usuario->__set('email', $_POST['email']);
				$usuario->__set('senha', md5($_POST['senha'])); // Receber a senha via POST e Criptografar com MD5 antes de gravar no Banco de Dados 

				// Verificar se o cadastro é válido antes de ser salvo 
					if($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) { // Caso o método retorne true, o cadastro será salvo
													  // Antes de salvar precisamos ver se o email já existe no Banco de Dados

							$usuario->salvar(); // Este método possui toda a lógica de armazenamento no Banco de Dados
							$this->render('cadastro'); // Caso seja sucesso redirecionar para View de Cadastro

					} else {
						$this->view->usuario = array ( // Associando o Objeto [View] á um novo atributo chamado usuario para criar um array
							'nome' => $_POST['nome'], // Este array serve para caso der erro manter os dados digitados para correção
							'email' => $_POST['email'], // Será preciso criar um value" $this->view->usuario [' name do input ']" nos inputs
							'senha' => $_POST['senha']
						); 
						$this->view->erroCadastro = true; // Definindo um parametro de erro caso não seja sucesso
						$this->render('inscreverse'); // Caso não seja sucesso redirecionar para View de InscreverSe

					}

	}
	

}


?>