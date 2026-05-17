<?php
$file = __DIR__.'/../storage/logs/laravel.log';
if (!file_exists($file)) {
    echo "Log file does not exist.\n";
    exit;
}

$lines = [];
$handle = fopen($file, 'r');
if ($handle) {
    // Seek to the end
    fseek($handle, 0, SEEK_END);
    $pos = ftell($handle);
    $line_count = 0;
    
    // Read backwards
    while ($pos > 0 && $line_count < 100) {
        $pos--;
        fseek($handle, $pos);
        $char = fgetc($handle);
        if ($char === "\n") {
            $line_count++;
        }
    }
    
    // Now read from this position to the end
    while (!feof($handle)) {
        $lines[] = fgets($handle);
    }
    fclose($handle);
}

echo "--- LAST 100 LINES OF LARAVEL LOG ---\n";
echo implode("", array_slice($lines, -100));
