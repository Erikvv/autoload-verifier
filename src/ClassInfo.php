<?php

namespace Vcn\AutoloadVerifier;

class ClassInfo
{
    public $file;

    public $class;

    public function __construct(
        string $file,
        string $class
    ) {
        $this->file = $file;
        $this->class = $class;
    }
}