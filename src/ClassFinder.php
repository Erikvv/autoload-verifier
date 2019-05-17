<?php

namespace Vcn\AutoloadVerifier;

use function Functional\flat_map;
use function Functional\map;
use function Functional\pluck;
use PhpParser\NameContext;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

class ClassFinder
{
    private $parser;

    private $nodeFinder;

    private $nodeTraverser;

    /**
     * Initialize some classes we can reuse
     */
    public function __construct()
    {
        $this->parser        = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $this->nodeFinder    = new NodeFinder();
        $this->nodeTraverser = new NodeTraverser();
        $this->nodeTraverser->addVisitor(new NameResolver());
    }

    /**
     * @param string[] $files
     *
     * @return ClassInfo[]
     */
    public function findClassesInFiles(iterable $files): array
    {
        return flat_map($files, [$this, 'findClassesInFile']);
    }

    /**
     * @param string $file
     *
     * @return ClassInfo[]
     */
    public function findClassesInFile(string $file): array
    {
        try {
            $nodes = $this->parser->parse(file_get_contents($file));
        } catch (\Throwable $e) {
            echo "Error parsing file " . $file . "\n";
            echo (string)$e;
        }

        $this->nodeTraverser->traverse($nodes);
        $classNodes = $this->nodeFinder->findInstanceOf($nodes, Class_::class);

        if (count($classNodes) > 1) {
            echo "Found more than 1 class in file " . $file . "\n";
        }

        $names = pluck($classNodes, 'namespacedName');

        $classInfos = map(
            $names, function (Node\Name $name) use ($file): ClassInfo {
                return new ClassInfo($file, (string) $name);
            }
        );

        return $classInfos;
    }
}
