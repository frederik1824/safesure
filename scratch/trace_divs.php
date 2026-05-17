<?php
$content = file_get_contents('resources/views/livewire/sync-control-center.blade.php');
$lines = explode("\n", $content);
$stack = [];
foreach ($lines as $i => $line) {
    $num = $i + 1;
    // Count opens in this line
    preg_match_all('/<div\b/', $line, $opens);
    foreach ($opens[0] as $o) {
        $stack[] = $num;
    }
    // Count closes in this line
    preg_match_all('/<\/div>/', $line, $closes);
    foreach ($closes[0] as $c) {
        if (empty($stack)) {
            echo "EXTRA CLOSE at line $num\n";
        } else {
            $openLine = array_pop($stack);
            if ($openLine == 1) {
                echo "ROOT CLOSED AT LINE $num\n";
            }
        }
    }
}
if (!empty($stack)) {
    echo "UNCLOSED DIVS STARTED AT: " . implode(', ', $stack) . "\n";
}
