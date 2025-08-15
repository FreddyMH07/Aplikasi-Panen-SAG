<?php
// Quick DB inspection script (not meant for production commit)
// Reads .env or .env.railway manually (no Laravel bootstrap) and outputs table counts.

$envFile = null;
foreach (['.env', '.env.railway'] as $candidate) {
    if (is_file(__DIR__ . DIRECTORY_SEPARATOR . $candidate)) { $envFile = __DIR__ . DIRECTORY_SEPARATOR . $candidate; break; }
}
if (!$envFile) {
    fwrite(STDERR, "No .env or .env.railway file found.\n");
    exit(1);
}
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#')) continue;
    if (!str_contains($line, '=')) continue;
    [$k,$v] = explode('=', $line, 2);
    $k = trim($k);
    $v = trim($v);
    $v = trim($v, "\"' ");
    if ($k !== '') $env[$k] = $v;
}
$required = ['DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD','PGSSLMODE'];
foreach ($required as $r) {
    if (!isset($env[$r])) { fwrite(STDERR, "Missing env key: $r\n"); exit(1);} }
$dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;sslmode=%s', $env['DB_HOST'],$env['DB_PORT'],$env['DB_DATABASE'],$env['PGSSLMODE']);
try {
    $pdo = new PDO(
        $dsn,
        $env['DB_USERNAME'],
        $env['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5, // seconds (may not be honored by pgsql but harmless)
        ]
    );
    echo "DB_CONNECT=OK\n";
    $tables = $pdo->query("select tablename from pg_catalog.pg_tables where schemaname='public' order by tablename")->fetchAll(PDO::FETCH_COLUMN);
    echo 'TABLES=' . ($tables ? implode(',', $tables) : '(none)') . "\n";
    $focus = ['migrations','users','master_data','panen_harians','panen_bulanans'];
    foreach ($focus as $t) {
        if (in_array($t, $tables, true)) {
            $cnt = $pdo->query('select count(*) from "' . $t . '"')->fetchColumn();
            echo strtoupper($t) . '_COUNT=' . $cnt . "\n";
        } else {
            echo strtoupper($t) . '_MISSING' . "\n";
        }
    }
} catch (Throwable $e) {
    echo 'DB_ERROR=' . $e->getMessage() . "\n";
    exit(2);
}
