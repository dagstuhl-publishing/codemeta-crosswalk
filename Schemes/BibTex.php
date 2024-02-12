<?php

/**
 * @Author: Ramy-Badr-Ahmed
 * @Desc: Codemeta conversions
 * @Repo: https://github.com/dagstuhl-publishing/beta-codemeta-crosswalk
 */

namespace Schemes;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BibTex extends Bib
{
    public const SCHEME = 'bibTex';           // validations/LW-View

    protected static array $codeMetaInitialTransformations = [      // codeMeta w.r.t scheme
        'author' => [self::class, 'author'],
    ];

    protected static array $codeMetaDuplicatedTransformations = [         // codeMeta w.r.t scheme
        'identity' => ['name', 'description', 'releaseNotes', 'readme', 'buildInstructions', 'identifier'],
        'dates'    => ['datePublished', 'dateCreated'],
        'URLs'     => ['downloadUrl', 'codeRepository'],
    ];

    protected static array $mappingsDescription = [             // scheme w.r.t codeMeta
        'simpleKeys' => [
            'author' => 'author',
        ],

        'complexKeys' => [
            [
                'key' => 'title',
                'title' => ['name'],
                'mapping' => [self::class, 'mapTitle']
            ],
            [
                'key' => 'bibKey',
                'bibKey' => ['name', 'identifier'],
                'mapping' => [self::class, 'mapBibKey']
            ],
            [
                'key' => 'howpublished',
                'howpublished' => ['downloadUrl', 'codeRepository'],
                'mapping' => [self::class, 'mapHowPublished']
            ],
            [
                'key' => 'year',
                'year' => ['datePublished', 'dateCreated'],
                'mapping' => [self::class, 'mapYear']
            ],
            [
                'key' => 'month',
                'month' => ['datePublished', 'dateCreated'],
                'mapping' => [self::class, 'mapMonth']
            ],
            [
                'key' => 'note',
                'note' => ['description', 'releaseNotes', 'readme', 'buildInstructions'],
                'mapping' => [self::class, 'mapNote']
            ],
        ]

    ];

    protected static function author(array $codeMetaValue): string
    {
        return implode(' and ', Arr::map($codeMetaValue,
            fn ($authorArray) => $authorArray['familyName'].", ".$authorArray['givenName'])
        );
    }

    protected static function identity(string $codeMetaValue): string
    {
        return $codeMetaValue;
    }

    protected static function dates(string $codeMetaValue): array
    {
        return date_parse($codeMetaValue);
    }

    protected static function URLs(string $codeMetaValue): string
    {
        return "\url{{$codeMetaValue}}";
    }

    protected static function mapTitle(...$codeMetaValue): string
    {
        return $codeMetaValue['name'];
    }

    protected static function mapBibKey(...$codeMetaValues): string
    {
        return Str::slug($codeMetaValues['name'], "-").  "__".$codeMetaValues['identifier'];
    }

    protected static function mapHowPublished(...$codeMetaValues): string|NULL
    {
        return $codeMetaValues['codeRepository'] ?? ($codeMetaValues['downloadUrl'] ?? NULL);
    }

    protected static function mapYear(...$codeMetaValues): string|NULL
    {
        $date = $codeMetaValues['datePublished'] ?? ($codeMetaValues['dateCreated'] ?? NULL);

        return isset($date) ? $date['year'] : NULL;
    }

    protected static function mapMonth(...$codeMetaValues): string|NULL
    {
        $date = $codeMetaValues['datePublished'] ?? ($codeMetaValues['dateCreated'] ?? NULL);

        return isset($date) ? $date['month'] : NULL;
    }

    protected static function mapNote(...$codeMetaValues): string|NULL
    {
        return collect([ $codeMetaValues['description'] ?? NULL, $codeMetaValues['releaseNotes'] ?? NULL, $codeMetaValues['readme'] ?? NULL, $codeMetaValues['buildInstructions'] ?? NULL ])
            ->whereNotNull()
            ->pipe(fn($val) => $val->isEmpty() ? NULL : $val->implode("\n\t"));
    }
}
