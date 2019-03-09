<?php

namespace Vcn\AutoloadVerifier;

use function Functional\flat_map;
use function Functional\map;
use function Functional\pluck;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

class ClassFinder
{
    private $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $this->nodeFinder = new NodeFinder();
    }

    /**
     * @param string[] $files
     * @return ClassInfo[]
     */
    public function findClassesInFiles(iterable $files): array
    {
        return flat_map($files, [$this, 'findClassesInFile']);
    }

    /**
     * @param string $file
     * @return ClassInfo[]
     */
    public function findClassesInFile(string $file): array
    {
        try {
            $nodes = $this->parser->parse(file_get_contents($file));
        } catch (\Throwable $e) {
            echo "Error parsing file " . $file . "\n";
            echo (string) $e;
        }

        $namespace  = $this->getNamespace($nodes);
        $classNodes = $this->nodeFinder->findInstanceOf($nodes, Class_::class);

        if (count($classNodes) > 1) {
            echo "Found more than 1 class in file " . $file . "\n";
        }

        $nonFqClassNames = pluck($classNodes, 'name');

        $fqClassNames = map($nonFqClassNames, function (string $class) use ($namespace): string {
            return $namespace . '\\' . $class;
        });

        $classInfos = map($fqClassNames, function (string $class) use ($file): ClassInfo {
            return new ClassInfo($file, $class);
        });

        return $classInfos;
    }

    /**
     * @param Node[] $nodes
     * @return string
     */
    private function getNamespace(array $nodes): string
    {
        /** @var Namespace_ $namepaceNode */
        $namepaceNode = $this->nodeFinder->findFirstInstanceOf($nodes, Namespace_::class);
        if ($namepaceNode) {
            return $namepaceNode->name;
        } else {
            return '';
        }
    }
}
