<?php

use Vcn\AutoloadVerifier\AutoloadVerifier;
use Symfony\Component\Finder\Finder as SymfonyFinder;

require_once 'vendor/autoload.php';

$projectDir = '.';
$projectAutoloader = 'vendor/autoload.php';

$fileFinder = new SymfonyFinder();
$fileFinder->files()->name('*.php')->notPath('vendor')->in('.');

$report = AutoloadVerifier::run($fileFinder, $projectDir, $projectAutoloader);
$report->print();
