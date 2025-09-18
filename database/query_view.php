<?php
declare(strict_types=1);

$db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');

echo "inventory_items:\n";
$stmt = $db->query('SELECT item_code, name, quantity, status FROM inventory_items');
foreach ($stmt as $row) {
	echo $row['item_code'] . ' ' . $row['name'] . ' -> ' . $row['quantity'] . ' (' . $row['status'] . ')' . PHP_EOL;
}

echo "\nemployee_job_stats:\n";
$stmt = $db->query('SELECT employee_name, minutes_worked, jobs_worked FROM employee_job_stats');
foreach ($stmt as $row) {
	echo $row['employee_name'] . ' -> ' . $row['jobs_worked'] . ' jobs, ' . $row['minutes_worked'] . ' mins' . PHP_EOL;
}

echo "\nbooking_counts_by_day:\n";
$stmt = $db->query("SELECT day, status, cnt FROM booking_counts_by_day ORDER BY day, status");
foreach ($stmt as $row) {
	echo $row['day'] . ' ' . $row['status'] . ' -> ' . $row['cnt'] . PHP_EOL;
}


