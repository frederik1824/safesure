<?php
// Simulate Livewire's Blade rendering (very basic)
$blade = file_get_contents('resources/views/livewire/sync-control-center.blade.php');
// Remove blade directives to get closer to final HTML for structure test
$html = preg_replace('/@[a-zA-Z]+(\([^)]*\))?/', '', $blade);

$html = preg_replace('/<script\b[^>]*>.*?<\/script>/si', '', $html);
$html = preg_replace('/<style\b[^>]*>.*?<\/style>/si', '', $html);

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

$count = 0;
foreach ($dom->childNodes as $node) {
    if ($node->nodeType === XML_ELEMENT_NODE) {
        if ($node->nodeName !== '?xml') {
            $count++;
            echo "Element: " . $node->nodeName . "\n";
        }
    } elseif ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) !== '') {
        $count++;
        echo "Text: " . trim($node->nodeValue) . "\n";
    }
}
echo "Total Roots (Elements + Non-empty Text): " . $count . "\n";
