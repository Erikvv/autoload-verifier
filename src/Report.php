<?php

namespace Vcn\AutoloadVerifier;

class Report
{
    private $files;

    private $foundClasses;

    private $classesNotKnownToAutoloader;

    /**
     * @param iterable<string> & \Countable $files
     * @param iterable<ClassInfo> & \Countable $foundClasses
     * @param iterable<ClassInfo> & \Countable $classesNotKnownToAutoloader
     */
    public function __construct(
        iterable $files,
        iterable $foundClasses,
        iterable $classesNotKnownToAutoloader
    ) {
        $this->files = $files;
        $this->foundClasses = $foundClasses;
        $this->classesNotKnownToAutoloader = $classesNotKnownToAutoloader;
    }

    public function print(): void
    {
        echo "=== Report ===\n\n";
        echo "Files scanned     : " . count($this->files) . "\n";
        echo "Classes found     : " . count($this->foundClasses) . "\n";
        echo "Classes not known to autoloader : " . count($this->classesNotKnownToAutoloader) . "\n\n";

        if (count($this->classesNotKnownToAutoloader)) {
            echo "=== Unknown classes === \n\n";
            $this->printClassesNotKnownToAutoloader();
        }
    }

    public function printClassesNotKnownToAutoloader(): void
    {
        /** @var ClassInfo $classInfo */
        foreach ($this->classesNotKnownToAutoloader as $classInfo) {
            echo "File  : " . $classInfo->getFile() . "\n";
            echo "Class : " . $classInfo->getClass() . "\n\n";
        }
    }
}