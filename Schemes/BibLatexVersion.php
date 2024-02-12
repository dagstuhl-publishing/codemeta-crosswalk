<?php

/**
 * @Author: Ramy-Badr-Ahmed
 * @Desc: Codemeta conversions
 * @Repo: https://github.com/dagstuhl-publishing/beta-codemeta-crosswalk
 */

namespace Schemes;


class BibLatexVersion extends Bib
{
    protected static array $codeMetaInitialTransformations = [      // codeMetaBibLatex w.r.t scheme
    ];

    protected static array $codeMetaDuplicatedTransformations = [         // codeMetaBibLatex w.r.t scheme
        'identity' => [
            'bibLatexKey', 'version', 'downloadUrl',
            'isPartOf', 'dateModified', 'releaseNotes'
        ],
    ];

    protected static array $mappingsDescription = [             // scheme w.r.t codeMetaBibLatex
        'simpleKeys' => [
            'file' => 'downloadUrl',
            'introducedin' =>'isPartOf',
            'note' => 'releaseNotes'
        ],

        'complexKeys' => [
            [
                'key' => 'version',
                'version' => ['version'],
                'mapping' => [self::class, 'mapVersion']
            ],
            [
                'key' => 'bibKey',
                'bibKey' => ['bibLatexKey', 'version'],
                'mapping' => [self::class, 'mapBibKey']
            ],
            [
                'key' => 'crossref',
                'crossref' => ['bibLatexKey'],
                'mapping' => [self::class, 'mapCrossref']
            ],
            [
                'key' => 'year',
                'year' => ['dateModified'],
                'mapping' => [self::class, 'mapYear']
            ],
            [
                'key' => 'month',
                'month' => ['dateModified'],
                'mapping' => [self::class, 'mapMonth']
            ],
            [
                'key' => 'date',
                'date' => ['dateModified'],
                'mapping' => [self::class, 'mapDate']
            ],
        ]
    ];

    protected static function identity(string|array $codeMetaValue): string|array
    {
        return $codeMetaValue;
    }

    protected static function mapVersion(...$codeMetaValues): string
    {
        return $codeMetaValues['version'];
    }


    protected static function mapBibKey(...$codeMetaValues): string
    {
        return $codeMetaValues['bibLatexKey']."_version_".$codeMetaValues['version'];
    }

    protected static function mapCrossref(...$codeMetaValues): string
    {
        return $codeMetaValues['bibLatexKey'];
    }

    protected static function mapYear(...$codeMetaValues): string
    {
        return $codeMetaValues['dateModified']['year'];
    }

    protected static function mapMonth(...$codeMetaValues): string
    {
        return $codeMetaValues['dateModified']["month"];
    }

    protected static function mapDate(...$codeMetaValues): string
    {
        return $codeMetaValues['dateModified']["full"];
    }



}
