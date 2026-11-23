<?php
/* =========================================================================
   config.php — Configuração do banco e conexão PDO
   Incluir no topo de toda página PHP: require_once 'includes/config.php';
   Os valores padrão funcionam no XAMPP. No Docker eles vêm das variáveis
   de ambiente do docker-compose.yml. Na hospedagem, troque os padrões.
   ========================================================================= */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'clone_airbnb');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    // cookie da sessão inacessível ao JavaScript e não enviado por outros sites
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

/* Devolve sempre a mesma conexão PDO (criada na primeira chamada) */
function conectar(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
    return $pdo;
}
