<?php

use Illuminate\Support\Str;

// Parse Postgres connection from common envs (DATABASE_URL, POSTGRES_URL, DATABASE_PUBLIC_URL, PG* vars) into parts
$__databaseUrl = env('DATABASE_URL') ?: env('POSTGRES_URL') ?: env('POSTGRESQL_URL') ?: env('DATABASE_PUBLIC_URL');
$__pgFromUrl = null;
if ($__databaseUrl && is_string($__databaseUrl)) {
    $parts = @parse_url($__databaseUrl);
    if (is_array($parts)) {
        $host = $parts['host'] ?? null;
        $port = isset($parts['port']) ? (string)$parts['port'] : null;
        $user = $parts['user'] ?? null;
        $pass = $parts['pass'] ?? null;
        $path = $parts['path'] ?? '';
        $db = is_string($path) ? ltrim($path, '/') : null;
        $__pgFromUrl = [
            'host' => $host,
            'port' => $port,
            'database' => $db,
            'username' => $user ? urldecode($user) : null,
            'password' => $pass ? urldecode($pass) : null,
        ];
    }
}
// Compute PG* envs version (Railway/Heroku compatible)
$__pgFromPgVars = null;
{
    $pgHost = env('PGHOST');
    $pgDb   = env('PGDATABASE');
    $pgUser = env('PGUSER');
    $pgPass = env('PGPASSWORD');
    $pgPort = env('PGPORT');
    if (!$pgHost) {
        // Fallback to Railway TCP proxy vars if provided
        $tcpHost = env('RAILWAY_TCP_PROXY_DOMAIN');
        $tcpPort = env('RAILWAY_TCP_PROXY_PORT');
        if ($tcpHost) { $pgHost = $tcpHost; }
        if ($tcpPort) { $pgPort = $tcpPort; }
    }
    if ($pgHost && $pgDb && $pgUser) {
        $__pgFromPgVars = [
            'host' => $pgHost,
            'port' => $pgPort ?: '5432',
            'database' => $pgDb,
            'username' => $pgUser,
            'password' => $pgPass,
        ];
    }
}
// Prefer PG* envs if URL points to localhost or this web service itself
if ($__pgFromUrl) {
    $host = strtolower((string)($__pgFromUrl['host'] ?? ''));
    $self = strtolower((string)env('RAILWAY_PRIVATE_DOMAIN', ''));
    $looksLocal = in_array($host, ['127.0.0.1','localhost'], true) || str_ends_with($host, '.internal');
    $pointsToSelf = $self && $host === strtolower($self);
    if (($looksLocal || $pointsToSelf) && $__pgFromPgVars) {
        $__pgFromUrl = $__pgFromPgVars;
    }
} elseif (!$__pgFromUrl && $__pgFromPgVars) {
    $__pgFromUrl = $__pgFromPgVars;
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    // If a Postgres URL or PG* vars are present, prefer pgsql by default; otherwise fall back to sqlite
    'default' => env('DB_CONNECTION', ($__databaseUrl || $__pgFromUrl) ? 'pgsql' : 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            // Use standard Laravel variable name so Railway's DATABASE_URL (if present) is honored
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            // Use DATABASE_URL parts if present; fallback to explicit envs, else sensible defaults
            'url' => env('DATABASE_URL') ?: env('POSTGRES_URL') ?: env('POSTGRESQL_URL') ?: env('DATABASE_PUBLIC_URL'),
            'host' => $__pgFromUrl['host'] ?? env('DB_HOST', '127.0.0.1'),
            'port' => $__pgFromUrl['port'] ?? env('DB_PORT', '5432'),
            'database' => $__pgFromUrl['database'] ?? env('DB_DATABASE', 'laravel'),
            'username' => $__pgFromUrl['username'] ?? env('DB_USERNAME', 'root'),
            'password' => $__pgFromUrl['password'] ?? env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('PG_SCHEMA', 'public'),
            // Allow overriding sslmode (disable|allow|prefer|require|verify-ca|verify-full)
            'sslmode' => env('PGSSLMODE', 'prefer'),
            // NOTE: Removed 'options' => env('DB_PG_OPTIONS') because Laravel expects an array of PDO options, not a string.
            // If you need PDO options, define them here manually as key => value pairs using PDO::ATTR_* constants.
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
