<?php

/**
 * Http Multistream Downloader simplest example
 */

$downloader = new Royalcms\Component\Downloader\Downloader(
  'http://fastdl.mongodb.org/osx/mongodb-osx-x86_64-1.6.5.tgz');
$downloader->download();

