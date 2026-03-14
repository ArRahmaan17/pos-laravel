<?php

/**
 * This script ensures that the database specified in the environment variables exists.
 * It is intended to be run during the Docker container startup process.
 */

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$db   = getenv('DB_DATABASE') ?: 'laravel';

echo "Connecting to MySQL at $host:$port as $user...\n";

$maxTries = 10;
$tries = 0;

while ($tries < $maxTries) {
    try {
        // First try connecting to the server (without DB)
        $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "Connected to MySQL server.\n";

        // Create Database if not exists
        echo "Ensuring database '$db' exists...\n";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        echo "Database '$db' ensured.\n";

        // If we are root and we want a different user, we can ensure that user has access.
        // But usually MYSQL_USER/PASSWORD env vars in docker-compose.yml handle this automatically 
        // for the FIRST time the volume is created. This script helps if things are changed later.
        
        if ($user === 'root' && getenv('DB_USERNAME') !== 'root') {
            $newUser = getenv('DB_USERNAME');
            $newPass = getenv('DB_PASSWORD');
            echo "Granting privileges to '$newUser' on '$db'...\n";
            $pdo->exec("CREATE USER IF NOT EXISTS '$newUser'@'%' IDENTIFIED BY '$newPass';");
            $pdo->exec("GRANT ALL PRIVILEGES ON `$db`.* TO '$newUser'@'%';");
            $pdo->exec("FLUSH PRIVILEGES;");
            echo "Privileges granted.\n";
        }
        
        exit(0);
    } catch (PDOException $e) {
        // If connection fails, maybe it's because the user doesn't exist yet but we have ROOT_PASSWORD?
        // Let's try connecting with root if DB_USERNAME is defined but connection failed.
        if ($user !== 'root' && getenv('MYSQL_ROOT_PASSWORD')) {
            echo "Connection failed for '$user'. Trying with root...\n";
            $user = 'root';
            $pass = getenv('MYSQL_ROOT_PASSWORD');
            continue; // Retry with root
        }

        $tries++;
        echo "Attempt $tries/$maxTries: Could not connect to MySQL. Error: " . $e->getMessage() . "\n";
        if ($tries < $maxTries) {
            echo "Waiting 5 seconds before next attempt...\n";
            sleep(5);
        }
    }
}

echo "Failed to connect to MySQL after $maxTries attempts.\n";
exit(1);
