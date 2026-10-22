<?php
declare(strict_types=1);

namespace YM\Core;

use DOMDocument;
use DOMElement;
use DOMNode;

/** Allow-list HTML sanitizer for rich-text fields (editor output is never trusted). */
final class Sanitizer
{
    private const TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'blockquote',
        'h2', 'h3', 'h4', 'a', 'img', 'hr', 'span', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'figure', 'figcaption',
    ];
    private const ATTRS = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height'],
        'th' => ['colspan', 'rowspan'],
        'td' => ['colspan', 'rowspan'],
    ];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }
        $doc = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="ym-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $doc->getElementById('ym-root');
        if (!$root) {
            return e($html);
        }
        self::walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return trim($out);
    }

    private static function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (!$child instanceof DOMElement) {
                if ($child->nodeType === XML_COMMENT_NODE) {
                    $node->removeChild($child);
                }
                continue;
            }
            $tag = strtolower($child->tagName);
            if (!in_array($tag, self::TAGS, true)) {
                // Drop dangerous containers entirely, unwrap everything else.
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'svg', 'math', 'link', 'meta'], true)) {
                    $node->removeChild($child);
                    continue;
                }
                self::walk($child);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }
            foreach (iterator_to_array($child->attributes) as $attr) {
                $name = strtolower($attr->nodeName);
                if (!in_array($name, self::ATTRS[$tag] ?? [], true)) {
                    $child->removeAttribute($attr->nodeName);
                    continue;
                }
                if (in_array($name, ['href', 'src'], true) && !self::safeUrl($attr->nodeValue, $name === 'src')) {
                    $child->removeAttribute($attr->nodeName);
                }
            }
            if ($tag === 'a') {
                $child->setAttribute('rel', 'noopener noreferrer');
            }
            self::walk($child);
        }
    }

    private static function safeUrl(string $url, bool $isImage): bool
    {
        $url = trim($url);
        if ($isImage && str_starts_with($url, 'data:image/') && preg_match('#^data:image/(png|jpe?g|webp|gif);base64,#i', $url)) {
            return false; // keep the DB lean: no inline base64 images
        }
        return (bool) preg_match('#^(https?://|mailto:|tel:|/|\#|[A-Za-z0-9_./-]+$)#i', $url);
    }
}
