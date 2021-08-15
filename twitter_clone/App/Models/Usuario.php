<?php

namespace App\Models;

use MF\Model\Model; // É esta class diz como que é feita a conexao com o banco

class Usuario extends Model {
    // Atributos - Identificam as colunas no Banco de Dados
    private $id;
    private $nome;
    private $email;
    private $senha;

    // Métodos GET e SET
    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    // O Model irá permitir: 
    // SALVAR
    public function salvar() {
        // Adicionar um query de INSERT para inserir os dados no banco de dados

        $query = "
            INSERT INTO
                usuarios(nome, email, senha)
            VALUES (?, ?, ?)
        ";

        $stmt = $this->db->prepare($query); // Utilizamos o $this->db pois ele herda de Model-[Na class Model em __construct recebe o parametro (\PDO $db) e o associa ao atributo da class $db]
        $stmt->bindValue(1, $this->__get('nome'));  // Por ter utilizado em VALUES o (?), podemos setalo em ordem aqui (1° {?}, 2° {?})
        $stmt->bindValue(2, $this->__get('email')); // Utilizar o get pois o atributo é privado
        $stmt->bindValue(3, $this->__get('senha')); // Utilizar criptografia MD5 para gravar no banco de dados um 'hash' de 32 caracteres em vez da senha

        $stmt->execute(); // Executando a query

        return $this; // Retornando o próprio Objeto
    }

    // VALIDAR SE UM CADASTRO PODE SER FEITO
    public function validarCadastro() {
        // Verificando se o cadastro é válido
            $cadastroValido = true;
            
            if(strlen($this->__get('nome')) < 3 || strlen($this->__get('email')) < 3 || strlen($this->__get('senha')) < 3) { // Caso o nome tenha menos que 3 caracteres não será válido
                $cadastroValido = false;
            }

            return $cadastroValido;
    }

    // VERIFICAR SE UM CADASTRO JA NÃO FOI INSERIDO ANTERIORMENTE
    public function getUsuarioPorEmail() {
        
        // Verificando se o email é igual
            $query = "
                SELECT 
                    nome, email
                FROM
                    usuarios
                WHERE
                    email = ?
            ";
            
            $stmt = $this->db->prepare($query); // Utilizando o prepare para evitar SQL INJECTION
            $stmt->bindValue(1, $this->__get('email')); // Associando 'email' ao { ? }
            $stmt->execute(); // Executando a query

            return $stmt->fetchAll(\PDO::FETCH_ASSOC); // Retornando um array associativo
    }

    // FAZER LOGIN - VERICAR SE EXISTE O USUÁRIO NO BANCO DE DADOS
    public function autenticar() { 

        $query = "
            SELECT
                id, nome, email, senha
            FROM
                usuarios
            WHERE
                email = ? 
            AND 
                senha = ?
        ";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $this->__get('email')); // Em AuthController utilizamos o set para setar estes valores no Objeto e usando o get aqui recuperamos estes valores
        $stmt->bindValue(2, $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC); // Criar a variavel $usuario que recebe o $stmt pra recuperar com o fetch apenas um único registro do Banco ('Não utilizar o fetchAll pois no banco só existirá apenas um único registro com este dado')
        
        if ($usuario && $usuario['id'] != '' && $usuario['nome'] != '') { // Se o ID ou NOME for dirente de vazio quer dizer que corresponde com algum usuário no banco           
            $this->__set('id', $usuario['id']); // Setando o id e nome do próprio objeto
            $this->__set('nome', $usuario['nome']);
        } else {
            header('Location: /?login=erro');
        }

        // return $usuario; EM VEZ DE RETORNAR O RESULTADO QUE VEm DO BANCO // Retornando a váriavel com valor de um array associativo
        return $this; // RETORNAR O PRÓPRIO OBJETO
    }

    // RECUPERAR OS USUARIOS DE ACORDO COM O ? DE PESQUISA
    public function getAll() { // Utilizando uma sub-consulta para verificar se um usuario ja está seguindo e não mostrar o button de seguir
        $query = "
            SELECT
                u.id, u.nome, u.email,
                (
                    SELECT
                        count(*)
                    FROM
                        usuarios_seguidores as us
                    WHERE
                        us.id_usuario = :id_usuario
                    AND
                        us.id_usuario_seguindo = u.id
                ) as seguindo_sn
            FROM
                usuarios as u
            WHERE
                u.nome like :nome
            AND 
                u.id != :id_usuario
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%' . $this->__get('nome') . '%'); // Adicionar o like [%] que indica caracteres a esquerda e direita
        $stmt->bindValue(':id_usuario', $this->__get('id')); // Verificar se o id é diferente do id do usuario auteticado para não pesquisar ele mesmo
        
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC); // Retornando a lista de arrays

    }

    // Informações do usuario
    public function getInfoUsuario() {
        $query = "
            SELECT 
                nome
            FROM
                usuarios
            WHERE
                id = ? 
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC); // Retorna apena o fetch normal pois é uma informação apenas   COUNT    
    }

    // Total de Tweets do usuário
    public function getTotalTweets() {
        $query = "
            SELECT 
                COUNT(*) as total_tweets               
            FROM
                tweets
            WHERE
                id_usuario = ? 
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC); // Retorna apena o fetch normal pois é uma informação apenas  COUNT     
    }

    // Total de usuarios que estamos seguindo
    public function getTotalSeguindo() {
        $query = "
            SELECT 
                COUNT(*) as total_seguindo              
            FROM
                usuarios_seguidores
            WHERE
                id_usuario = ? 
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC); // Retorna apena o fetch normal pois é uma informação apenas       
    }

    // Total de seguidores
    public function getTotalSeguidores() {
        $query = "
            SELECT 
                COUNT(*) as total_seguidores               
            FROM
                usuarios_seguidores
            WHERE
                id_usuario_seguindo = ? 
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC); // Retorna apena o fetch normal pois é uma informação apenas       
    }





}
    



?>