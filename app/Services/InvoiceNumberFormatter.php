<?php

namespace App\Services;

use Illuminate\Support\Str;

class InvoiceNumberFormatter
{
    public const DEFAULT_PATTERN = '{year}/{seq:3}';

    /**
     * Validate a pattern allowing placeholders {year} and {seq} or {seq:N}.
     */
    public function validatePattern(string $pattern): bool
    {
        if (! Str::contains($pattern, '{seq')) {
            return false;
        }

        if (preg_match('/\{(?!year\}|seq(?::[2-6])?\})[^}]*\}/', $pattern)) {
            return false;
        }

        if (! preg_match('/^[A-Za-z0-9\-_.\/{\}: ]+$/', $pattern)) {
            return false;
        }

        return true;
    }

    /**
     * Format the pattern replacing placeholders with the given values.
     */
    public function format(string $pattern, int $year, int $sequence): string
    {
        $pad = $this->extractPadding($pattern);
        $seqValue = str_pad((string) $sequence, $pad, '0', STR_PAD_LEFT);

        $result = $pattern;
        $result = str_replace('{year}', (string) $year, $result);
        $result = preg_replace('/\{seq(?::[2-6])?\}/', $seqValue, $result) ?? $result;

        return $result;
    }

    /**
     * Extract the sequence number from an invoice number using the pattern.
     */
    public function extractSequence(string $pattern, int $year, string $invoiceNumber): ?int
    {
        $regex = $this->buildRegex($pattern, $year);

        if (! preg_match($regex, $invoiceNumber, $matches)) {
            return null;
        }

        return isset($matches['seq']) ? (int) $matches['seq'] : null;
    }

    private function buildRegex(string $pattern, int $year): string
    {
        $regex = '';
        $offset = 0;

        while (($start = strpos($pattern, '{', $offset)) !== false) {
            $literal = substr($pattern, $offset, $start - $offset);
            $regex .= preg_quote($literal, '/');

            $end = strpos($pattern, '}', $start);
            if ($end === false) {
                break;
            }

            $placeholder = substr($pattern, $start, $end - $start + 1);
            if ($placeholder === '{year}') {
                $regex .= preg_quote((string) $year, '/');
            } elseif (preg_match('/^\{seq(?::(?P<padding>[2-6]))?\}$/', $placeholder, $seqMatch)) {
                $padding = $seqMatch['padding'] ?? '';
                $regex .= $padding !== '' ? '(?P<seq>\d{'.$padding.'})' : '(?P<seq>\d+)';
            } else {
                $regex .= preg_quote($placeholder, '/');
            }

            $offset = $end + 1;
        }

        if ($offset < strlen($pattern)) {
            $regex .= preg_quote(substr($pattern, $offset), '/');
        }

        return '/^'.$regex.'$/';
    }

    private function extractPadding(string $pattern): int
    {
        if (preg_match('/\{seq:(?P<padding>[2-6])\}/', $pattern, $matches)) {
            return (int) $matches['padding'];
        }

        return 3;
    }
}
