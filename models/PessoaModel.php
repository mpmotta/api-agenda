<?php

class Pessoa {
    private $id, $avatar, $nome, $fone, $email;
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getAvatar() { return $this->avatar; }
    public function setAvatar($avatar) { $this->avatar = $avatar; }
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getFone() { return $this->fone; }
    public function setFone($fone) { $this->fone = $fone; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
}

class PessoaModel {
    private $conn, $tabela = "pessoas";
    public function __construct($pdo) { $this->conn = $pdo; }
    public function consulta() {
        $sql = "SELECT id, avatar, nome, fone, email FROM $this->tabela";
        $stmt = $this->conn->prepare($sql); $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function consultaID($id) {
        $sql = "SELECT id, avatar, nome, fone, email FROM $this->tabela WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function inserir(Pessoa $pessoa) {
        $sql = "INSERT INTO $this->tabela (avatar, nome, fone, email) VALUES (:avatar, :nome, :fone, :email)";
        $stmt = $this->conn->prepare($sql);
        $avatar = $pessoa->getAvatar() ?: 'avatar.jpg';
        $stmt->bindParam(':avatar', $avatar);
        $stmt->bindParam(':nome', $pessoa->getNome());
        $stmt->bindParam(':fone', $pessoa->getFone());
        $stmt->bindParam(':email', $pessoa->getEmail());
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    public function editar(Pessoa $pessoa, $id) {
        $sql = "UPDATE $this->tabela SET avatar = :avatar, nome = :nome, fone = :fone, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':avatar', $pessoa->getAvatar());
        $stmt->bindParam(':nome', $pessoa->getNome());
        $stmt->bindParam(':fone', $pessoa->getFone());
        $stmt->bindParam(':email', $pessoa->getEmail());
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function excluir($id) {
        $sql = "DELETE FROM $this->tabela WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
