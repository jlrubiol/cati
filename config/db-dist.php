<?php

return [
    'app' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=wopr',
        'username' => 'falken',
        'password' => 'joshua',
        'charset' => 'utf8mb4',

        // Schema cache options (for production environment)
        //'enableSchemaCache' => true,  // Whether to enable schema caching at all.
        //'schemaCacheDuration' => 60,  // Number of seconds that table metadata can remain valid in cache.
        //'schemaCache' => 'cache',     // Which cache component to use for caching.
    ],
    # Se usaba en la función findIdentidadByNip() del modelo User.php, pero se pasó a usar un web service.
    #'identidades' => [
    #    'class' => 'apaoww\oci8\Oci8DbConnection',  // Requires apaoww/yii2-oci8
    #    'dsn' => 'oci8:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=oraculo.unizar.es)(PORT=1521))(CONNECT_DATA=(SID=DELFOS)));charset=WE8ISO8859P1;',
    #    'username' => 'dodona',
    #    'password' => 'PopolWuj',
    #    'attributes' => [],
    #],
];
