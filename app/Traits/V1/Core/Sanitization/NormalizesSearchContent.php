<?php

namespace App\Traits\V1\Core\Sanitization;

trait NormalizesSearchContent
{
    /**
     * Normalize HTML content before searching.
     *
     * @param string $column
     *
     * @return string
     */
    private function normalizedColumn(string $column): string
    {
        return "
            regexp_replace(
                regexp_replace(
                    regexp_replace(
                        {$column},
                        '<[^>]+>',
                        ' ',
                        'g'
                    ),
                    '&nbsp;|&#160;|&#xA0;',
                    ' ',
                    'gi'
                ),
                '[[:space:]]+',
                ' ',
                'g'
            )
        ";
    }
}
