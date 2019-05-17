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
            $class = $classInfo->getClass();
            try {
                return !class_exists($class);
            } catch (\Throwable $error) {
                echo "Error loading class {$class}\n";
                echo (string) $error;
                return true;
            }
        });

        return new Report(
            $fileFinder, $classInfos, $notFoundClasses
        );
    }
}