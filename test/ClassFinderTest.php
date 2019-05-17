<?php

namespace Vcn\AutoloadVerifier\Test;

use function Functional\invoke;
use function Functional\pluck;
use PHPUnit\Framework\TestCase;
use Vcn\AutoloadVerifier\ClassFinder;

class ClassFinderTest extends TestCase
{
    public function provider()
    {
        return [
            [
                'NonNamespacedClass.php', [
                    \NonNamespacedClass::class,
                ]
            ], [
                'NamespacedClass.php', [
                    \TestAssets\NamespacedClass::class
                ]
            ], [
                'BracedNamespace.php', [
                    \TestAssets\BracedNamespace\BracedClass::class,
                ]
            ], [
                'TwoClassesInOneNamespace.php', [
                    \TestAssets\TwoClassesInOneNamespace\One::class,
                    \TestAssets\TwoClassesInOneNamespace\Two::class,
                ]
            ], [
                'TwoClassesInTwoNamespaces.php', [
                    \TestAssets\NamespaceOne\One::class,
                    \TestAssets\NamespaceTwo\Two::class
                ]
            ]
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testClassFinder(string $file, array $expectedClasses)
    {
        $classFinder = new ClassFinder();
        $classInfos = $classFinder->findClassesInFile(__DIR__ . '/../test-assets/' . $file);

        self::assertEquals($expectedClasses, invoke($classInfos, 'getClass'));
    }
}
