<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$component = app(\App\Livewire\SyncControlCenter::class);
// Run lifecycle
$component->mount();

$view = $component->render();
$html = $view->render();

$html_stripped = preg_replace('/<script\b[^>]*>.*?<\/script>/si', '', $html);
$html_stripped = preg_replace('/<style\b[^>]*>.*?<\/style>/si', '', $html_stripped);

$dom = new \DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html_stripped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
libxml_clear_errors();

$count = 0;
echo "Roots found:\n";
foreach ($dom->childNodes as $node) {
    if ($node->nodeType === XML_ELEMENT_NODE) {
        if ($node->nodeName !== '?xml') {
            $count++;
            echo "- Element: " . $node->nodeName;
            if ($node->hasAttributes()) {
                $class = $node->getAttribute('class');
                if ($class) echo " (class: $class)";
            }
            echo "\n";
        }
    } elseif ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) !== '') {
        $count++;
        echo "- Text: [" . trim($node->nodeValue) . "]\n";
    }
}
echo "TOTAL ROOTS: " . $count . "\n";
