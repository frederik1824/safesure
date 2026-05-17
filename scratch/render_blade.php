<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$blade = file_get_contents(__DIR__.'/../resources/views/livewire/sync-control-center.blade.php');
$html = Illuminate\Support\Facades\Blade::render($blade, [
    'polling' => false,
    'systemStatus' => ['firebase' => 'online', 'workers' => 'active', 'safe_node' => 'online', 'cmd_node' => 'online'],
    'pendingJobs' => 0,
    'recentLog' => null,
    'activeTab' => 'dashboard',
    'checkpoints' => [],
    'isCircuitOpen' => false,
    'webhooks' => [],
    'logs' => [],
    'search' => '',
    'filterStatus' => 'all',
    'records' => collect([]), // mocking pagination might be tricky, let's just use empty collection
    'auditLogs' => [],
    'totalConflicts' => 0,
    'isStalled' => false,
    'showModal' => false,
    'selectedRecordDetail' => null
]);

file_put_contents(__DIR__.'/rendered_raw.html', $html);

// Now apply Livewire's logic
$html = preg_replace('/<script\b[^>]*>.*?<\/script>/si', '', $html);
$html = preg_replace('/<style\b[^>]*>.*?<\/style>/si', '', $html);

$dom = new \DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
libxml_clear_errors();

$count = 0;
echo "Detected Roots:\n";
foreach ($dom->childNodes as $node) {
    if ($node->nodeType === XML_ELEMENT_NODE) {
        if ($node->nodeName !== '?xml') {
            $count++;
            echo "- Element: " . $node->nodeName . "\n";
        }
    } elseif ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) !== '') {
        $count++;
        echo "- Text: [" . trim($node->nodeValue) . "]\n";
    }
}
echo "TOTAL: " . $count . "\n";
