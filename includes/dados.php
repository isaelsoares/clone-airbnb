<?php
/* =========================================================================
   dados.php — Funções de acesso ao banco (PDO com prepared statements)
   ========================================================================= */

require_once __DIR__ . '/config.php';

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

/* Gera o JSON das acomodações para o JavaScript (seguro dentro de <script>) */
function acomodacoesParaJs(array $acomodacoes): string
{
    return json_encode($acomodacoes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
}
