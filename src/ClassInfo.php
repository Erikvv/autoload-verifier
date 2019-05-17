<?php

namespace Vcn\AutoloadVerifier;

class ClassInfo
{
    private $file;

    private $class;

    public function __construct(
        string $file,
        string $class
    ) {
        $this->file = $file;
        $this->class = $class;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}