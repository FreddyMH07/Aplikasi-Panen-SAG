#!/usr/bin/env bash
set -euo pipefail
HOST="${DB_HOST:?unset}"; PORT="${DB_PORT:-5432}";
echo "[pg_diag] Host=$HOST Port=$PORT";
getent hosts "$HOST" || echo "[pg_diag] getent not available";
( command -v nc >/dev/null && nc -vz -w 3 "$HOST" "$PORT" ) || echo "[pg_diag] nc not available or port closed";
( command -v curl >/dev/null && curl -s -o /dev/null -w "[pg_diag] TCP_CONNECT_OK\n" "tcp://$HOST:$PORT" ) || true;
php -r "try{new PDO('pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE') . ';sslmode=' . getenv('PGSSLMODE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo '[pg_diag] PDO_CONNECT_OK';}catch(Throwable $e){echo '[pg_diag] PDO_CONNECT_FAIL:' . $e->getMessage();}";
echo;
