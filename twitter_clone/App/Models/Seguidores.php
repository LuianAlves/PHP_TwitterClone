<?php

namespace App\Models;

use MF\Model\Model; // É esta class diz como que é feita a conexao com o banco

    class Seguidores extends Model {
        // Atributos
        private $id;
        private $id_usuario;
        private $id_usuario_seguindo;

        // Métodos GET e SET
        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        // Método para seguir e deixar de seguiur
        public function seguirUsuario($id_usuario_seguindo) { // Receber como parametro o $id_usuario_seguindo
            $query = "
                INSERT INTO
                    usuarios_seguidores(id_usuario, id_usuario_seguindo)
                VALUES (?, ?)
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $this->__get('id')); // ID Usuario da $_SESSION
            $stmt->bindValue(2, $id_usuario_seguindo); // ID Usuario do $_GET
            $stmt->execute();
           
            return true; // Return true para inserção no banco            
        }

        public function deixarSeguir($id_usuario_seguindo) {
            $query = "
                DELETE FROM
                    usuarios_seguidores
                WHERE
                    id_usuario = ?
                AND
                    id_usuario_seguindo = ?
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $this->__get('id')); // ID Usuario da $_SESSION || Podemos utilizar o __get('id') porque em AppController estamos setando o valor da Session antes de entrar na COndição
            $stmt->bindValue(2, $id_usuario_seguindo); // ID Usuario do $_GET
            $stmt->execute();
            
            return true; // Return true para delete no banco            
        }

    }
?>