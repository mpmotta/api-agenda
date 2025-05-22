<?php
require_once __DIR__ . '/../models/PessoaModel.php';

class PessoaController {
    private $model;
    public function __construct($pdo) {
        $this->model = new PessoaModel($pdo);
    }
    public function index() { return $this->model->consulta(); }
    public function show($id) { return $this->model->consultaID($id); }
    public function store($data) {
        $pessoa = new Pessoa();
        $pessoa->setNome(sanitize($data['nome'] ?? ''));
        $pessoa->setFone(sanitize($data['fone'] ?? ''));
        $pessoa->setEmail(sanitize($data['email'] ?? ''));
        $pessoa->setAvatar(sanitize($data['avatar'] ?? 'avatar.jpg'));
        return $this->model->inserir($pessoa);
    }
    public function update($id, $data) {
        $pessoa = new Pessoa();
        $pessoa->setNome(sanitize($data['nome'] ?? ''));
        $pessoa->setFone(sanitize($data['fone'] ?? ''));
        $pessoa->setEmail(sanitize($data['email'] ?? ''));
        $pessoa->setAvatar(sanitize($data['avatar'] ?? 'avatar.jpg'));
        return $this->model->editar($pessoa, $id);
    }
    public function destroy($id) {
        return $this->model->excluir($id);
    }
}
