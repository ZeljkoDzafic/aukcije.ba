<?php

declare(strict_types=1);

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

class HtmlSanitizer
{
    /**
     * @var array<string, list<string>>
     */
    private array $allowedTags = [
        'p' => [],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'blockquote' => [],
        'h2' => [],
        'h3' => [],
        'a' => ['href', 'target', 'rel'],
    ];

    public function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><body>'.$html.'</body>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $body = $document->getElementsByTagName('body')->item(0);

        if (! $body instanceof DOMElement) {
            return '';
        }

        $sanitized = '';

        foreach (iterator_to_array($body->childNodes) as $child) {
            $sanitized .= $this->sanitizeNode($child);
        }

        return trim($sanitized);
    }

    private function sanitizeNode(DOMNode $node): string
    {
        if ($node instanceof DOMText) {
            return e($node->wholeText);
        }

        if (! $node instanceof DOMElement) {
            return '';
        }

        $tag = strtolower($node->tagName);

        if (! array_key_exists($tag, $this->allowedTags)) {
            $content = '';

            foreach (iterator_to_array($node->childNodes) as $child) {
                $content .= $this->sanitizeNode($child);
            }

            return $content;
        }

        $attributes = $this->sanitizeAttributes($tag, $node);
        $content = '';

        foreach (iterator_to_array($node->childNodes) as $child) {
            $content .= $this->sanitizeNode($child);
        }

        if (in_array($tag, ['br'], true)) {
            return "<{$tag}{$attributes}>";
        }

        return "<{$tag}{$attributes}>{$content}</{$tag}>";
    }

    private function sanitizeAttributes(string $tag, DOMElement $node): string
    {
        $allowedAttributes = $this->allowedTags[$tag];
        $sanitized = [];

        foreach ($allowedAttributes as $attribute) {
            if (! $node->hasAttribute($attribute)) {
                continue;
            }

            $value = trim($node->getAttribute($attribute));

            if ($value === '') {
                continue;
            }

            if ($tag === 'a' && $attribute === 'href' && ! $this->isSafeHref($value)) {
                continue;
            }

            if ($tag === 'a' && $attribute === 'target') {
                $value = $value === '_blank' ? '_blank' : '_self';
            }

            if ($tag === 'a' && $attribute === 'rel') {
                $value = 'noopener noreferrer';
            }

            $sanitized[] = sprintf(' %s="%s"', $attribute, e($value));
        }

        if ($tag === 'a' && $node->getAttribute('target') === '_blank' && ! $node->hasAttribute('rel')) {
            $sanitized[] = ' rel="noopener noreferrer"';
        }

        return implode('', $sanitized);
    }

    private function isSafeHref(string $href): bool
    {
        if (str_starts_with($href, '#') || str_starts_with($href, '/')) {
            return true;
        }

        return (bool) preg_match('/^(https?:|mailto:|tel:)/i', $href);
    }
}
