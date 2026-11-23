<?php
/* =========================================================================
   dados.php — Funções de acesso ao banco (PDO com prepared statements)
   ========================================================================= */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';

/* ---------- Acomodações ---------- */

/* Converte "a,b,c" em ['a', 'b', 'c'] (ignora itens vazios) */
function paraLista(string $texto): array
{
    return array_values(array_filter(array_map('trim', explode(',', $texto)), 'strlen'));
}

/* Converte uma linha da tabela no formato usado pelas páginas e pelo JS */
function montarAcomodacao(array $linha): array
{
    return [
        'id' => (int) $linha['id'],
        'titulo' => $linha['titulo'],
        'tipo' => $linha['tipo'],
        'cidade' => $linha['cidade'],
        'estado' => $linha['estado'],
        'descricao' => $linha['descricao'],
        'diaria' => (float) $linha['diaria'],
        'capacidade' => (int) $linha['capacidade'],
        'quartos' => (int) $linha['quartos'],
        'camas' => (int) $linha['camas'],
        'banheiros' => (int) $linha['banheiros'],
        'minNoites' => (int) $linha['min_noites'],
        'nota' => (float) $linha['nota'],
        'avaliacoes' => (int) $linha['avaliacoes'],
        'selo' => $linha['selo'] ?: null,
        'anfitriao' => $linha['anfitriao'],
        'anfitriaoDesde' => (int) $linha['anfitriao_desde'],
        'categorias' => paraLista($linha['categorias']),
        'imagens' => paraLista($linha['imagens']),
        'comodidades' => paraLista($linha['comodidades']),
    ];
}

function listarAcomodacoes(): array
{
    $stmt = conectar()->prepare('SELECT * FROM acomodacoes ORDER BY id');
    $stmt->execute();
    return array_map('montarAcomodacao', $stmt->fetchAll());
}

function buscarAcomodacao(int $id): ?array
{
    $stmt = conectar()->prepare('SELECT * FROM acomodacoes WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $linha = $stmt->fetch();
    return $linha ? montarAcomodacao($linha) : null;
}

/* ---------- Administradores ---------- */

function buscarAdminPorEmail(string $email): ?array
{
    $stmt = conectar()->prepare('SELECT id, nome, email, senha FROM administradores WHERE email = :email');
    $stmt->execute([':email' => $email]);
    return $stmt->fetch() ?: null;
}

/* Totais exibidos no painel */
function resumoPainel(): array
{
    $pdo = conectar();
    return [
        'acomodacoes' => (int) $pdo->query('SELECT COUNT(*) FROM acomodacoes')->fetchColumn(),
        'reservas' => (int) $pdo->query('SELECT COUNT(*) FROM reservas')->fetchColumn(),
        'usuarios' => (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn(),
        'faturamento' => (float) $pdo->query('SELECT COALESCE(SUM(total), 0) FROM reservas')->fetchColumn(),
    ];
}

/* ---------- Usuários ---------- */

function emailCadastrado(string $email): bool
{
    $stmt = conectar()->prepare('SELECT 1 FROM usuarios WHERE email = :email');
    $stmt->execute([':email' => $email]);
    return (bool) $stmt->fetchColumn();
}

function buscarUsuarioPorEmail(string $email): ?array
{
    $stmt = conectar()->prepare('SELECT id, nome, email, senha FROM usuarios WHERE email = :email');
    $stmt->execute([':email' => $email]);
    return $stmt->fetch() ?: null;
}

/* Grava o usuário com a senha protegida por password_hash; devolve o id */
function cadastrarUsuario(array $u, string $senha): int
{
    $pdo = conectar();
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, telefone, nascimento, senha)
                           VALUES (:nome, :email, :telefone, :nascimento, :senha)');
    $stmt->execute([
        ':nome' => $u['nome'],
        ':email' => $u['email'],
        ':telefone' => $u['telefone'],
        ':nascimento' => $u['nascimento'],
        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
    ]);
    return (int) $pdo->lastInsertId();
}

/* ---------- Reservas ---------- */

/* Grava a reserva feita no checkout; devolve o id */
function registrarReserva(array $r): int
{
    $pdo = conectar();
    $stmt = $pdo->prepare('INSERT INTO reservas (acomodacao_id, nome, email, cpf, checkin, checkout, hospedes, forma_pagamento, total)
                           VALUES (:acomodacao, :nome, :email, :cpf, :checkin, :checkout, :hospedes, :forma, :total)');
    $stmt->execute([
        ':acomodacao' => $r['acomodacao_id'],
        ':nome' => $r['nome'],
        ':email' => $r['email'],
        ':cpf' => somenteDigitos($r['cpf']),
        ':checkin' => $r['checkin'],
        ':checkout' => $r['checkout'],
        ':hospedes' => $r['hospedes'],
        ':forma' => $r['forma'],
        ':total' => $r['total'],
    ]);
    return (int) $pdo->lastInsertId();
}

/* ---------- Painel: acomodações ---------- */

/* Lista para o painel, com o total de reservas; filtra por título ou cidade */
function listarAcomodacoesPainel(string $busca = ''): array
{
    $sql = 'SELECT a.id, a.titulo, a.tipo, a.cidade, a.estado, a.diaria, a.capacidade, a.min_noites, a.imagens,
                   COUNT(r.id) AS reservas
            FROM acomodacoes a
            LEFT JOIN reservas r ON r.acomodacao_id = a.id
            WHERE a.titulo LIKE :busca1 OR a.cidade LIKE :busca2
            GROUP BY a.id
            ORDER BY a.id DESC';
    $stmt = conectar()->prepare($sql);
    $termo = '%' . $busca . '%';
    $stmt->execute([':busca1' => $termo, ':busca2' => $termo]);
    return $stmt->fetchAll();
}

function contarReservasDaAcomodacao(int $id): int
{
    $stmt = conectar()->prepare('SELECT COUNT(*) FROM reservas WHERE acomodacao_id = :id');
    $stmt->execute([':id' => $id]);
    return (int) $stmt->fetchColumn();
}

function excluirAcomodacao(int $id): bool
{
    $stmt = conectar()->prepare('DELETE FROM acomodacoes WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}

/* Gera o JSON das acomodações para o JavaScript (seguro dentro de <script>) */
function acomodacoesParaJs(array $acomodacoes): string
{
    return json_encode($acomodacoes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
}
