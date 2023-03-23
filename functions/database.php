<?php

/**
 * Vanaf php 8.2 kan je #[\SensitiveParameter] gebruiken bij paswoord
 * @param string $user
 * @param string $pass
 * @param string $db
 * @param string $host
 * @return PDO
 */
function dbConnect(string $user, string $pass, string $db, string $host = 'localhost'): PDO
{
    $connection = new PDO("mysql:host={$host};dbname={$db}", $user, $pass);

    return $connection;
}
