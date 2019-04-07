<?php

/**
 * Http Multistream Downloader example
 */

use Royalcms\Component\Downloader\Downloader;

include __DIR__ . '/../vendor/autoload.php';

$url = 'http://uni.mirrors.163.com/ubuntu-releases/19.04/ubuntu-19.04-beta-desktop-amd64.iso';
$outputFile = pathinfo($url, PATHINFO_BASENAME);

$downloader = new Downloader($url);
$downloader->setOutputFile($outputFile);
$downloader->setMinCallbackPeriod(3);
$downloader->setMaxParallelChunks(30);
$downloader->setChunkSize(1024 * 1024);

$downloader->setProgressCallback(function($position, $totalBytes) use ($downloader)
  {
    static $prevPosition = 0;
    static $prevTime = 0;

    $now = microtime(true);
    $speed = ($position - $prevPosition) / ($now - $prevTime);
    $speed = round($speed / 1024 / 1024, 2) . 'MB/s';
    $positionFormatted = round($position / 1024 / 1024, 2) . 'MB';
    $totalBytesFormatted = round($totalBytes / 1024 / 1024, 2) . 'MB';
    $streams = $downloader->getRunningChunks();

    if ($now - $prevTime < 1e3) {
        echo "speed: $speed; done: $positionFormatted / $totalBytesFormatted; streams: $streams\n";
        flush();
    }


    $prevPosition = $position;
    $prevTime = $now;

    return true;
  });

ignore_user_abort(true);    //By closing the browser, the PHP script can continue to execute.
set_time_limit(0);          // Let the program execute without limit by set_time_limit(0)
ini_set('output_buffering', 0);

echo "<pre>";
echo "Downloading $url ...\n"; flush();
echo "Content length: " . $downloader->getTotalBytes() . " bytes\n"; flush();

$downloader->download();

echo "Done!\n"; flush();
echo "Output file size: " . filesize($outputFile) . " bytes\n"; flush();
echo "Output file MD5: " . md5_file($outputFile) . "\n"; flush();
echo "<pre>";