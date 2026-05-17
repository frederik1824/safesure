<?php
$file = __DIR__.'/../storage/logs/laravel.log';
if (!file_exists($file)) {
    echo "Log file does not exist.\n";
    exit;
}

$handle = fopen($file, 'r');
$found = 0;
while (!feof($handle)) {
    $line = fgets($handle);
    if (strpos($line, 'Firebase Filtered Sync Error') !== false) {
        echo $line;
        $found++;
        if ($found >= 10) break;
    }
}
fclose($handle);
if ($found === 0) {
    echo "No 'Firebase Filtered Sync Error' entries found.\n";
}
