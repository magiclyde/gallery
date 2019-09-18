<?php

// src/Twig/MarkdownExtension.php
// https://symfony.com/doc/current/templating/twig_extension.html
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Parsedown;

class MarkdownExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('markdown', [$this, 'renderMarkdownToHtml']),
        ];
    }

    public function renderMarkdownToHtml($markdown)
    {
        $parsedown = new Parsedown();

        return $parsedown->text($markdown);
    }

}