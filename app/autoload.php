<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

//return $loader;

set_include_path(__DIR__.'/../vendor'.PATH_SEPARATOR.get_include_path());
require_once __DIR__.'/../vendor/Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
Zend_Search_Lucene_Analysis_Analyzer::setDefault(
new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());

   ini_set("iconv.internal_encoding", 'UTF-8');
   Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
return $loader;

