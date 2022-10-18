<?php

require_once __DIR__ . '/vendor/autoload.php';

use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

function crawl_list_by_page($pref_code, $city_code, $page)
{
    $options = new Options();
    $options->setEnforceEncoding('utf8');

    $url = sprintf('https://townpage.goo.ne.jp/result.php?pref_code=%02d&city_code=%03d&pages=%d', $pref_code, $city_code, $page);
    $dom = new Dom();
    $dom->loadFromUrl($url, $options);

    $elements = $dom->find('div.resultBox.generalprBox');

    $results = [];
    foreach ($elements as $element)
    {
        $titleTxt = $element->find('div.titleTxt a')[0];

        $detailUrl = dirname($url) . substr($titleTxt->href, 1);

        $dom2 = new Dom();
        $dom2->loadFromUrl($detailUrl, $options);

        $elements2 = $dom2->find('a[@itemprop="url"]');

        array_push($results, [
            'name' => $titleTxt->text,
            'url' => $elements2[0]->text,
            'phone_number' => $element->find('div[@class=infoBlock] td[@set_culumn="tel_no"]')[0]->text
        ]);
    }

    return $results;
}

$code = $argv[1];
$pref_code = $code / 1000;
$city_code = $code % 1000;
$page = $argv[2];

$elements = crawl_list_by_page($pref_code, $city_code, $page);

foreach ($elements as $element)
{
    echo $element['name'] . "\n";
    echo $element['url'] . "\n";
    echo $element['phone_number'] . "\n";
}

?>
