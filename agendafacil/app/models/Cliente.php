<?php
require_once __DIR__ . '/BaseModel.php';

class Cliente extends BaseModel
{
    protected string $tabela   = 'clientes';
    protected array  $campos   = ['nome', 'telefone', 'email'];
    protected string $colunaFk = 'cliente_id';
}
