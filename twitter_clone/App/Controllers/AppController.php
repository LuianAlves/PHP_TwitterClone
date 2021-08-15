<?php

    namespace App\Controllers;

    //os recursos do miniframework
    use MF\Controller\Action;
    use MF\Model\Container;

        class AppController extends Action {
            
            public function timeline() {

                // Verificando se o usuário está autenticado, caso tentar acessar a partir do url sem fazer login não conseguirá               
                $this->validaAutenticacao(); // Método que verifica se o id ou nome está setado || // PARA NÃO ACESSAR ATRAVEZ DA URL SEM TER FEITO LOGIN

                // Recuperando os tweet para visualiza-los na timeline
                $tweet = Container::getModel('Tweet');  
                // Atribuindo o id_usuario ao id da SESSION para que podemos filtrar os tweets de cada usuario
                $tweet->__set('id_usuario', $_SESSION['id']); // Setando o valor do [id_usuario]  ao id de Sessão                      
                $tweets = $tweet->getAll(); // Como getAll retorna um array de tweets podemos associa-lo a uma nova variavel $tweets

                $this->view->tweets = $tweets; // Terá os valores recuperados pelo foreach em timeline.phtml || // Criar um atributo dinamico [tweets] que recebe o array dos tweets [$tweets]
                
                    // Instanciando o Objeto [Usuario] para contagem de tweets, seguidores ...
                    $usuario = Container::getModel('Usuario');
                    $usuario->__set('id', $_SESSION['id']); // Setar o id com o valor da Sessão

                    // Associando diretamente ás variaveis da view
                    $this->view->info_usuario = $usuario->getInfoUsuario();
                    $this->view->total_tweets = $usuario->getTotalTweets();
                    $this->view->total_seguindo = $usuario->getTotalSeguindo();
                    $this->view->total_seguidores = $usuario->getTotalSeguidores();
                
                $this->render('timeline');                                     
            }

            public function tweet() {
            // SOMENTE SE TIVER LOGADO PODERÁ FAZER A GRAVAÇÃO DO TWEET             
                    
                // Verificando se o usuário está autenticado, caso tentar acessar a partir do url sem fazer login não conseguirá
                $this->validaAutenticacao(); // Método que verifica se o id ou nome está setado || // PARA NÃO ACESSAR ATRAVEZ DA URL SEM TER FEITO LOGIN   

                // Instanciando o Model [Tweet] utilizando o COntainer
                $tweet = Container::getModel('Tweet'); // Este método getmodel('') retorna a conexão com o banco já configurada
                $tweet->__set('tweet', $_POST['tweet']); // Atravez da instancia do Objeto, podemos utilizar o set para setar o atributo $tweet em [Model/tweet.php]
                $tweet->__set('id_usuario', $_SESSION['id']); // Associar e Atribuir o valor da Session ID ao ID recebido via POST

                $tweet->salvar(); // Salvar o tweet no banco

                header('Location: /timeline'); // Redirecionar a página após a postagem do tweet                        
            }

            public function validaAutenticacao() { // Método criado para verificar se o usuario está autenticado e impedir de acessar via url
                session_start(); // Iniciar a sessão

                if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') { // Verificar se não está setado o ID [operador de negação !] 
                    header('Location: /?login=erro'); // Qualquer situação que seja o usuário será redirecionado para página raiz
                } 
            }

            // PESQUISAR PESSOAS PELO INPUT
            public function seguirPessoas() {

                $this->validaAutenticacao(); // Verifica a autenticacao do usuario
              
                $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : ''; // Caso o indice da super global estiver setado [1° get], atribuiremos o valor recido por esse indice [2° get] caso contrario deixar vazio

                $usuarios = array(); // Mesma variavel criada dentro do if, mas caso não entre na condição listamos esta variavel aqui como vazia para que não de nenhum erro

                // Se digitar um valor no campo
                if($pesquisarPor != '') {
                    $usuario = Container::getModel('Usuario'); // Utilizar o container para instanciar o Objeto [Usuario], retornando um objeto com conexao ao banco

                    $usuario->__set('nome', $pesquisarPor); // Setando o atributo nome com o valor recebido via super global GET
                    $usuario->__set('id', $_SESSION['id']); // Associando o id da pesquisa com o usuario autenticado para que não ache ele mesmo pesquisando

                    $usuarios = $usuario->getAll(); // Retorna um array executando o método e pesquisando com o fetchAll no banco de dados
                }

                $this->view->usuarios = $usuarios; // Recebendo o array da pesquisa

                    // Instanciando o Objeto [Usuario] para contagem de tweets, seguidores ...
                    $usuario = Container::getModel('Usuario');
                    $usuario->__set('id', $_SESSION['id']); // Setar o id com o valor da Sessão

                    // Associando diretamente ás variaveis da view
                    $this->view->info_usuario = $usuario->getInfoUsuario();
                    $this->view->total_tweets = $usuario->getTotalTweets();
                    $this->view->total_seguindo = $usuario->getTotalSeguindo();
                    $this->view->total_seguidores = $usuario->getTotalSeguidores();
                    
                $this->render('quemSeguir');
            }

            // SEGUIR OU DEIXAR DE SEGUIR
            public function acao() {
                $this->validaAutenticacao(); // Verifica a autenticacao do usuario

                $acao = isset($_GET['acao']) ? $_GET['acao'] : ''; // Recuperar parametro via GET apenas se existir
                $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

                $usuario = Container::getModel('Seguidores'); // Instanciar o Objeto [Seguidores]
                $usuario->__set('id', $_SESSION['id']); // Setamos o id [Sessão] dentro do id atributo do objeto

                    // Com base na acao() será SEGUIR OU DEIXAR DE SEGUIR
                    if ($acao == 'seguirUsuario') { // Se for seguir, recuperamos a instancia do objeto e executamos o metodo seguirUsuario(' No parametro colocar o id do suaurio que vmaos seguir')                
                        $usuario->seguirUsuario($id_usuario_seguindo); 

                    } else if ($acao == 'deixarSeguir') {
                        $usuario->deixarSeguir($id_usuario_seguindo);                        
                    }
                
                header('Location: /seguirPessoas');
            }
            
    }

?>