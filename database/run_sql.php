<?php
// Simple utility to execute a .sql file against database/database.sqlite using PDO.
// Usage: php database/run_sql.php path/to/file.sql
// This keeps things simple on Windows/PowerShell where quoting inline code is tricky.

declare(strict_types=1);

function fail(string $message, int $code = 1): void {
	fwrite(STDERR, $message . PHP_EOL);
	exit($code);
}

if ($argc < 2) {
	fail('Usage: php database/run_sql.php <sql_file>');
}

$sqlFile = $argv[1];
if (!is_file($sqlFile)) {
	fail("SQL file not found: {$sqlFile}");
}

// Ensure the database directory exists
$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'database.sqlite';

try {
	$db = new PDO('sqlite:' . $dbPath, null, null, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]);
	$sql = file_get_contents($sqlFile);
	if ($sql === false) {
		fail('Failed to read SQL file: ' . $sqlFile);
	}
	$db->beginTransaction();
	$db->exec($sql);
	$db->commit();
	echo "Executed SQL file: {$sqlFile}" . PHP_EOL;
} catch (Throwable $e) {
	if (isset($db) && $db->inTransaction()) {
		$db->rollBack();
	}
	fail('Error executing SQL: ' . $e->getMessage());
}


