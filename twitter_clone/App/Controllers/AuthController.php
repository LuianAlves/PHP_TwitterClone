<?php

    namespace App\Controllers;

    //os recursos do miniframework
    use MF\Controller\Action;
    use MF\Model\Container;

        class AuthController extends Action {

            public function autenticar () {
                //Criar uma instancia do Objeto [Usuario] que é responsável por manipular os dados no Banco de Dados
                $usuario = Container::getModel('Usuario');

                // Setando os atributos que estamos recebendo via POST
                $usuario->__set('email', $_POST['email']);
                $usuario->__set('senha', md5($_POST['senha'])); // Utilizar criptografia MD5

                // Chamando o método autenticar() que é responsável por analisar no Banco de Dados se existe o usuário
                $retorno = $usuario->autenticar();
                    // Após executar o método autenticar() obtemos o ID e Nome do Usuário

                // Verificando se o método autenticar() recuperou o Id e Nome do usuário
                if($usuario->__get('id') != '' && $usuario->__get('nome') != '') {
                    
                    // Utilizando a super Global SESSION após a autenticação
                        session_start();

                        $_SESSION['id'] = $usuario->__get('id');
                        $_SESSION['nome'] = $usuario->__get('nome');

                        header('Location: /timeline');
                } else {
                    echo 'Erro na Autenticação!';
                }        
            }

            // FAZER LOGOFF 
            public function sair() {
                session_start();        // Sempre informar que estamos trabalho com sessão
                session_destroy();      // Destruir a sessão que utilizamos

                header('Location: /');  // Redirecionar a página após o logoff
            }
        }

?>