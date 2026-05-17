<?php
$content = file_get_contents('resources/views/livewire/sync-control-center.blade.php');
preg_match_all('/<div\b/', $content, $opens);
preg_match_all('/<\/div>/', $content, $closes);
preg_match_all('/@if\b/', $content, $if_opens);
preg_match_all('/@endif\b/', $content, $if_closes);
preg_match_all('/@foreach\b/', $content, $foreach_opens);
preg_match_all('/@endforeach\b/', $content, $foreach_closes);
preg_match_all('/@forelse\b/', $content, $forelse_opens);
preg_match_all('/@endforelse\b/', $content, $forelse_closes);

echo "Divs: " . count($opens[0]) . " opens, " . count($closes[0]) . " closes.\n";
echo "Ifs: " . count($if_opens[0]) . " opens, " . count($if_closes[0]) . " closes.\n";
echo "Foreach: " . count($foreach_opens[0]) . " opens, " . count($foreach_closes[0]) . " closes.\n";
echo "Forelse: " . count($forelse_opens[0]) . " opens, " . count($forelse_closes[0]) . " closes.\n";
