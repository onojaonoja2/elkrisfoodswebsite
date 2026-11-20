<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

final class SafeHtmlSanitizer implements HtmlSanitizerInterface
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}

    public function sanitize(string $input): string
    {
        return $this->sanitizeFor('body', $input);
    }

    public function sanitizeFor(string $element, string $input): string
    {
        $html = $this->sanitizer->sanitizeFor($element, $input);

        if (stripos($html, '<iframe') === false) {
            return $html;
        }

        $document = new DOMDocument;

        $previousUseInternalErrors = libxml_use_internal_errors(true);

        $document->loadHTML(
            '<?xml encoding="UTF-8"><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previousUseInternalErrors);

        $root = $document->documentElement;

        if (! $root instanceof DOMElement) {
            return $html;
        }

        foreach (iterator_to_array($root->getElementsByTagName('iframe')) as $iframe) {
            $host = strtolower((string) parse_url(trim($iframe->getAttribute('src')), PHP_URL_HOST));

            if (! in_array($host, ['www.youtube-nocookie.com', 'player.vimeo.com'], true)) {
                $iframe->parentNode?->removeChild($iframe);
            }
        }

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= (string) $document->saveHTML($child);
        }

        return $output;
    }
}
