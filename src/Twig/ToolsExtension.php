<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ToolsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_string', [$this, 'isString']),
            new TwigFunction('is_array', [$this, 'isArray']),
            new TwigFunction('is_bool', [$this, 'isBool']),
            new TwigFunction('is_int', [$this, 'isInt']),
        ];
    }

    public function isString($value): Bool
    {
        return is_string($value);
    }

    public function isArray($value): Bool
    {
        return is_array($value);
    }

    public function isBool($value): Bool
    {
        return is_bool($value);
    }

    public function isInt($value): Bool
    {
        return is_int($value);
    }
}
