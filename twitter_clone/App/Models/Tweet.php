<?php

    namespace App\Models;

    use MF\Model\Model; // É esta class diz como que é feita a conexao com o banco

        class Tweet extends Model {

            // Atributos
            private $id;
            private $id_usuario;
            private $tweet;
            private $data;

            // Métodos GET e SET
            public function __get($atributo) {
                return $this->$atributo;
            }

            public function __set($atributo, $valor) {
                $this->$atributo = $valor;
            }

            // Salvar Tweet
            public function salvar() {
                $query = "
                    INSERT INTO
                        tweets(id_usuario, tweet)
                    VALUES
                        (?, ?)
                ";

                $stmt = $this->db->prepare($query);
                $stmt->bindValue(1, $this->__get('id_usuario'));
                $stmt->bindValue(2, $this->__get('tweet'));

                $stmt->execute();

                return $this; // Retornando o próprio Tweet
            }

            // Recuperar para Visualizar Tweet
            public function getAll() { // DATA_FORMAT - Formatando a data para padrao dd/mm/aaaa e depois apelidando para data
                $query = "
                    SELECT 
                        t.id, t.id_usuario, u.nome, t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y - %H:%i') as data
                    FROM
                        tweets as t
                    LEFT JOIN
                        usuarios as u on (t.id_usuario = u.id)
                    WHERE
                        id_usuario = :id_usuario 
                    OR
                        t.id_usuario in (
                            SELECT 
                                id_usuario_seguindo
                            FROM
                                usuarios_seguidores
                            WHERE
                                id_usuario = :id_usuario
                        )  
                    ORDER BY
                        t.data desc               
                    
                "; // Utilizar o WHERE que compara o id_usuario ao parametro id_usuario[esse id é o de SESSION]
                   // Utilizar o Left Join para ligar as 2 colunas e assim conseguir mostrar o nome do usuario nos tweets [Necessário para utilizar a tag de identificação do php dentro de timeline.phtml]
                   // Utilizar () para uma sub-consulta utilizando o 'in' para consultar quais usuarios o [usuario autenticado via $_SESSION] está seguindo
                
                $stmt = $this->db->prepare($query); 
                $stmt->bindValue(':id_usuario', $this->__get('id_usuario')); // Filtrando para que cada usuario visualize somente seu tweet
                $stmt->execute();

                return $stmt->fetchAll(\PDO::FETCH_ASSOC); // Retornando um array associativo
            }
            

        }
?>