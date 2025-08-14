#!/usr/bin/env bash
set -euo pipefail
HOST="${DB_HOST:?unset}"; PORT="${DB_PORT:-5432}"; DB="${DB_DATABASE:?unset}"; USER="${DB_USERNAME:?unset}";
echo "[pg_diag] Host=$HOST Port=$PORT DB=$DB User=$USER SSLMODE=${PGSSLMODE:-(unset)}";
echo "[pg_diag] Date=$(date -u +%Y-%m-%dT%H:%M:%SZ)";

if command -v getent >/dev/null; then
	getent hosts "$HOST" || echo "[pg_diag] getent failed";
else
	echo "[pg_diag] getent not available";
fi

if command -v nc >/dev/null; then
	(nc -vz -w 3 "$HOST" "$PORT" 2>&1 || true) | sed 's/^/[pg_diag] nc: /';
else
	echo "[pg_diag] nc not available";
fi

if command -v traceroute >/dev/null; then
	traceroute -m 5 -q 1 "$HOST" 2>&1 | sed 's/^/[pg_diag] traceroute: /';
else
	echo "[pg_diag] traceroute not available";
fi

if command -v curl >/dev/null; then
	curl -s -o /dev/null -w "[pg_diag] curl_tcp_exit=%{exitcode}\n" "tcp://$HOST:$PORT" || true;
fi

SSLMODES=("${PGSSLMODE:-require}" prefer disable)
TRIED=()
for mode in "${SSLMODES[@]}"; do
	[[ " ${TRIED[*]} " == *" $mode "* ]] && continue
	TRIED+=("$mode")
	printf '[pg_diag] PDO test sslmode=%s ... ' "$mode"
	php -r "try{new PDO('pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE') . ';sslmode=$mode', getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo 'OK';}catch(Throwable $e){echo 'FAIL:' . preg_replace('/\s+/', ' ', substr(
	$e->getMessage(),0,160));}" || true
	echo
done
echo "[pg_diag] Done.";
