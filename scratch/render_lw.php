<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $html = Livewire\Livewire::mount('sync-control-center');
    echo "SUCCESS\n";
} catch (\Livewire\Features\SupportMultipleRootElementDetection\MultipleRootElementsDetectedException $e) {
    echo "EXCEPTION CAUGHT!\n";
    // We want the HTML. Let's get it by hooking into the exception or using reflection?
    // Let's just catch the generic exception.
} catch (\Exception $e) {
    echo "OTHER EXCEPTION: " . $e->getMessage() . "\n";
}

// Better way: intercept Livewire's HTML BEFORE the exception is thrown.
// Livewire renders the view. Let's just use the Livewire manager to get the view.
$component = app(Livewire\LivewireManager::class)->new('sync-control-center');
$view = $component->render();
$html = $view->render();
echo "HTML LENGTH: " . strlen($html) . "\n";

// Run Livewire's detector manually
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
            echo "- Element: " . $node->nodeName . "\n";
        }
    } elseif ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) !== '') {
        $count++;
        echo "- Text: [" . trim($node->nodeValue) . "]\n";
    }
}
echo "TOTAL ROOTS: " . $count . "\n";
