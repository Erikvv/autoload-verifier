<?php

namespace Vcn\AutoloadVerifier;

use function Functional\filter;
use Symfony\Component\Finder\Finder;

class AutoloadVerifier
{
    public static function run(
        Finder $fileFinder,
        string $projectDir = '.',
        string $projectAutoloader = 'vendor/autoload.php'
    ): Report {
        chdir($projectDir);

        $classFinder = new ClassFinder();
        $classInfos = $classFinder->findClassesInFiles($fileFinder);

        require_once $projectAutoloader;

        $notFoundClasses = filter($classInfos, function (ClassInfo $classInfo) {
            try {
                return !class_exists($classInfo->class);
            } catch (\Throwable $error) {
                echo "Error loading class {$classInfo->class}\n";
                echo (string) $error;
            }
        });

        return new Report(
            $fileFinder, $classInfos, $notFoundClasses
        );
    }
}