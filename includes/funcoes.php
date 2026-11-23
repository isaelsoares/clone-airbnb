<?php
/* =========================================================================
   funcoes.php — Funções auxiliares e regras de negócio
   Separadas do HTML para serem reaproveitadas pelas páginas e pelo painel.
   ========================================================================= */

/* ---------- Saída e navegação ---------- */

/* Escapa um valor para ser exibido no HTML (evita XSS) */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/* Redireciona para outra página e encerra o script */
function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* Formata um valor em reais: 1234.5 -> "R$ 1.234,50" */
function formatarMoeda(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/* Converte "AAAA-MM-DD" em "DD/MM/AAAA" */
function formatarData(string $iso): string
{
    $data = dataValida(substr($iso, 0, 10));
    return $data ? $data->format('d/m/Y') : $iso;
}

/* ---------- Mensagens entre páginas (guardadas na sessão) ---------- */

function definirMensagem(string $tipo, string $texto): void
{
    $_SESSION['mensagem'] = ['tipo' => $tipo, 'texto' => $texto];
}

/* Devolve o HTML da mensagem pendente (uma única vez) */
function exibirMensagem(): string
{
    if (empty($_SESSION['mensagem'])) {
        return '';
    }
    $m = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
    return alerta($m['tipo'], $m['texto']);
}

/* Monta um alerta no padrão do guia de estilo (sucesso, erro ou info) */
function alerta(string $tipo, string $texto): string
{
    $icones = ['sucesso' => 'bi-check-circle', 'erro' => 'bi-exclamation-circle', 'info' => 'bi-info-circle'];
    $icone = $icones[$tipo] ?? $icones['info'];
    $papel = $tipo === 'erro' ? 'alert' : 'status';
    return '<p class="alerta alerta-' . e($tipo) . '" role="' . $papel . '"><i class="bi ' . $icone
        . '" aria-hidden="true"></i><span>' . e($texto) . '</span></p>';
}

/* Mensagem de erro abaixo de um campo do formulário.
   Usa o mesmo id que o js/validacao.js cria ("<id do input>-erro"),
   assim o JavaScript reaproveita o parágrafo em vez de criar outro. */
function erroCampo(array $erros, string $campo, string $idInput): string
{
    if (empty($erros[$campo])) {
        return '';
    }
    return '<p class="erro-campo" id="' . e($idInput) . '-erro" aria-live="polite">' . e($erros[$campo]) . '</p>';
}

/* Atributos aria para campos com erro vindo do servidor */
function invalido(array $erros, string $campo, string $idInput): string
{
    return empty($erros[$campo]) ? '' : ' aria-invalid="true" aria-describedby="' . e($idInput) . '-erro"';
}

/* ---------- Entrada de dados (sanitização) ---------- */

/* Lê um texto enviado por POST, sem espaços nas pontas */
function textoPost(string $campo): string
{
    $valor = filter_input(INPUT_POST, $campo, FILTER_UNSAFE_RAW);
    return is_string($valor) ? trim($valor) : '';
}

/* Lê um texto enviado por GET, sem espaços nas pontas */
function textoGet(string $campo): string
{
    $valor = filter_input(INPUT_GET, $campo, FILTER_UNSAFE_RAW);
    return is_string($valor) ? trim($valor) : '';
}

/* Lê um inteiro (GET ou POST) dentro de um intervalo; devolve null se inválido */
function inteiro(int $origem, string $campo, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): ?int
{
    $valor = filter_input($origem, $campo, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => $min, 'max_range' => $max],
    ]);
    return is_int($valor) ? $valor : null;
}

/* Mantém só os dígitos (CPF, telefone, cartão) */
function somenteDigitos(string $texto): string
{
    return preg_replace('/\D/', '', $texto);
}

/* ---------- Validações ---------- */

/* Converte "AAAA-MM-DD" em data; devolve null se a data não existir */
function dataValida(?string $texto): ?DateTimeImmutable
{
    if (!$texto) {
        return null;
    }
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', $texto);
    return ($data && $data->format('Y-m-d') === $texto) ? $data : null;
}

function hoje(): DateTimeImmutable
{
    return new DateTimeImmutable('today');
}

/* Idade em anos completos a partir de "AAAA-MM-DD" */
function idade(string $nascimento): int
{
    $data = dataValida($nascimento);
    return $data ? $data->diff(hoje())->y : 0;
}

/* Valida o CPF pelo cálculo dos dígitos verificadores */
function cpfValido(string $cpf): bool
{
    $n = somenteDigitos($cpf);
    if (strlen($n) !== 11 || preg_match('/^(\d)\1{10}$/', $n)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += (int) $n[$i] * ($t + 1 - $i);
        }
        $digito = (($soma * 10) % 11) % 10;
        if ($digito !== (int) $n[$t]) {
            return false;
        }
    }
    return true;
}

/* =========================================================================
   REGRAS DE NEGÓCIO (as mesmas do js/dados.js, agora validadas no servidor)
   RN1 — O checkout deve ser posterior ao check-in, o check-in não pode ser
         no passado e a estadia deve respeitar o mínimo de noites do anúncio.
   RN2 — O número de hóspedes não pode ultrapassar a capacidade do anúncio.
   RN3 — Estadias de 7 noites ou mais recebem 10% de desconto nas diárias.
   ========================================================================= */
const TAXA_LIMPEZA = 120;       // valor fixo por reserva
const TAXA_SERVICO = 0.14;      // 14% sobre as diárias (após desconto)
const DESCONTO_SEMANAL = 0.10;  // RN3
const NOITES_DESCONTO = 7;

function contarNoites(string $checkin, string $checkout): int
{
    $ini = dataValida($checkin);
    $fim = dataValida($checkout);
    if (!$ini || !$fim) {
        return 0;
    }
    return (int) $ini->diff($fim)->format('%r%a');
}

/* Valida a reserva e devolve os erros por campo (vazio = reserva válida) */
function validarReserva(array $acomodacao, string $checkin, string $checkout, ?int $hospedes): array
{
    $erros = [];
    $ini = dataValida($checkin);
    $fim = dataValida($checkout);

    // RN1 — datas e mínimo de noites
    if (!$ini) {
        $erros['checkin'] = 'Informe uma data de check-in válida.';
    } elseif ($ini < hoje()) {
        $erros['checkin'] = 'O check-in não pode ser em uma data passada.';
    }

    if (!$fim) {
        $erros['checkout'] = 'Informe uma data de checkout válida.';
    } elseif ($ini && $fim <= $ini) {
        $erros['checkout'] = 'O checkout deve ser depois do check-in.';
    } elseif ($ini && contarNoites($checkin, $checkout) < $acomodacao['minNoites']) {
        $erros['checkout'] = "Este anúncio exige no mínimo {$acomodacao['minNoites']} noite(s).";
    }

    // RN2 — capacidade
    if (!$hospedes || $hospedes < 1) {
        $erros['hospedes'] = 'Informe ao menos 1 hóspede.';
    } elseif ($hospedes > $acomodacao['capacidade']) {
        $erros['hospedes'] = "Esta acomodação comporta no máximo {$acomodacao['capacidade']} hóspedes.";
    }

    return $erros;
}

/* Calcula os valores da reserva; RN3 aplica o desconto semanal */
function calcularReserva(array $acomodacao, int $noites): array
{
    $subtotal = $acomodacao['diaria'] * $noites;
    $desconto = $noites >= NOITES_DESCONTO ? $subtotal * DESCONTO_SEMANAL : 0;
    $servico = ($subtotal - $desconto) * TAXA_SERVICO;
    $total = $subtotal - $desconto + TAXA_LIMPEZA + $servico;
    return [
        'noites' => $noites,
        'subtotal' => round($subtotal, 2),
        'desconto' => round($desconto, 2),
        'limpeza' => TAXA_LIMPEZA,
        'servico' => round($servico, 2),
        'total' => round($total, 2),
    ];
}
