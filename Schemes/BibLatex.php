<?php

/**
 * @Author: Ramy-Badr-Ahmed
 * @Desc: Codemeta conversions
 * @Repo: https://github.com/dagstuhl-publishing/beta-codemeta-crosswalk
 */

namespace Schemes;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BibLatex extends Bib
{
    public const SCHEME = 'bibLaTex';           // validations/LW-View

    protected static array $codeMetaInitialTransformations = [      // codeMeta w.r.t scheme
        'author'     => [self::class, 'author'],
        'funder'     => [self::class, 'funder'],
        'publisher'  => [self::class, 'publisher'],
        'identifier' => [self::class, 'identifier'],
    ];

    protected static array $codeMetaDuplicatedTransformations = [         // codeMeta w.r.t scheme
        'identity' => ['name', 'description', 'softwareVersion', 'version', 'releaseNotes', 'isPartOf'],
        'dates'    => ['dateCreated', 'datePublished', 'dateModified'],
        'URLs'     => ['downloadUrl', 'url', 'codeRepository', 'readme', 'buildInstructions', 'license'],
    ];

    protected static array $mappingsDescription = [             // scheme w.r.t codeMeta
        'simpleKeys' => [
            'abstract' => 'description',
            'file' => 'downloadUrl',
            'version' => 'softwareVersion',
            'license' => 'license',
            'repository' => 'codeRepository',
            'organization' => 'funder'
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
                'key' => 'author',
                'author' => ['author'],
                'mapping' => [self::class, 'mapAuthor']
            ],
            [
                'key' => 'institution',
                'institution' => ['author'],
                'mapping' => [self::class, 'mapInstitution']
            ],
            [
                'key' => 'year',
                'year' => ['dateCreated'],
                'mapping' => [self::class, 'mapYear']
            ],
            [
                'key' => 'month',
                'month' => ['dateCreated'],
                'mapping' => [self::class, 'mapMonth']
            ],
            [
                'key' => 'url',
                'url' => ['publisher'],
                'mapping' => [self::class, 'mapUrl']
            ],
            [
                'key' => 'publisher',
                'publisher' => ['publisher'],
                'mapping' => [self::class, 'mapPublisher']
            ],
            [
                'key' => 'doi',
                'doi' => ['identifier'],
                'mapping' => [self::class, 'mapDoi']
            ],
            [
                'key' => 'swhid',
                'swhid' => ['identifier'],
                'mapping' => [self::class, 'mapSwhid']
            ],
            [
                'key' => 'note',
                'note' => ['releaseNotes', 'readme', 'buildInstructions'],
                'mapping' => [self::class, 'mapNote']
            ],
            [
                'key' => 'date',
                'date' => ['dateCreated'],
                'mapping' => [self::class, 'mapDate']
            ],
            [
                'key' => 'urldate',
                'urldate' => ['datePublished'],
                'mapping' => [self::class, 'mapUrldate']
            ],
        ]

    ];

    protected static function author(array $codeMetaValue): array
    {
        return [
            'names'        => Arr::map($codeMetaValue, fn ($authorArray) => $authorArray['familyName'].", ".$authorArray['givenName']),
            'affiliations' => Arr::pluck($codeMetaValue, 'affiliation.name')
        ];
    }

    protected static function funder(array $codeMetaValue): string
    {
        return implode(parent::COMMA_SEPARATOR,
            Arr::pluck(Arr::isAssoc($codeMetaValue) ? [$codeMetaValue] : $codeMetaValue, 'name'));
    }

    protected static function publisher(array $codeMetaValue): array
    {
        return Arr::except($codeMetaValue, ["@type"]);
    }

    protected static function identifier(string $codeMetaValue): array
    {
        return Arr::mapWithKeys([$codeMetaValue], function ($identifier){
            $isDoi = parent::isDOI($identifier);

            return [
                'idType' => $isDoi ? 'doi' : (parent::isSwhResolver($identifier) ? 'swhid' : Null),
                'identifier' => $isDoi
                    ? preg_replace('/^\/|\/$/', '', parse_url($identifier)['path'])
                    : Str::of($identifier)->match('/(?<=https:\/\/archive\.softwareheritage\.org\/).*$/i')->value()
            ];
        });
    }

    protected static function identity(string $codeMetaValue): string
    {
        return $codeMetaValue;
    }

    protected static function dates(string $codeMetaValue): array
    {
        return array_merge(date_parse($codeMetaValue), ["full" => $codeMetaValue]);
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
        return Str::slug($codeMetaValues['name'], "-").  "__".$codeMetaValues['identifier']['identifier'];
    }

    protected static function mapAuthor(...$codeMetaValues): string
    {
        return implode(parent::AND_SEPARATOR, $codeMetaValues['author']['names']);
    }

    protected static function mapInstitution(...$codeMetaValues): string|NULL
    {
        $affiliations = collect($codeMetaValues['author']['affiliations'])->whereNotNull();

        return $affiliations->isEmpty()
            ? Null
            : $affiliations->map(fn($val) => $val ?? 'Unknown')->implode(' and ');
    }

    protected static function mapYear(...$codeMetaValues): string
    {
        return $codeMetaValues['dateCreated']["year"];
    }

    protected static function mapMonth(...$codeMetaValues): string
    {
        return $codeMetaValues['dateCreated']["month"];
    }

    protected static function mapUrl(...$codeMetaValues): string
    {
        return $codeMetaValues['publisher']['url'];
    }

    protected static function mapPublisher(...$codeMetaValues): string|NULL
    {
        return $codeMetaValues['publisher']['name'] ?? NULL;
    }

    protected static function mapDoi(...$codeMetaValues): string|NULL
    {
        return $codeMetaValues['identifier']['idType'] === 'doi'
            ? "\url{{$codeMetaValues['identifier']['identifier']}}"
            : NULL;
    }

    protected static function mapSwhid(...$codeMetaValues): string|NULL
    {
        return $codeMetaValues['identifier']['idType'] === 'swhid'
            ? $codeMetaValues['identifier']['identifier']
            : NULL;
    }

    protected static function mapNote(...$codeMetaValues): string|NULL
    {
        $arr = [ $codeMetaValues['readme'] ?? NULL, $codeMetaValues['releaseNotes'] ?? NULL, $codeMetaValues['buildInstructions'] ?? NULL ];

        $note = implode("\n\t", Arr::whereNotNull($arr));

        return Str::of($note)->isNotEmpty() ? $note : NULL;
    }

    protected static function mapDate(...$codeMetaValues): string|NULL
    {
        return isset($codeMetaValues['dateCreated'])
            ? $codeMetaValues['dateCreated']["full"]
            : NULL;
    }

    protected static function mapUrldate(...$codeMetaValues): string|NULL
    {
        return isset($codeMetaValues['datePublished'])
            ? $codeMetaValues['datePublished']["full"]
            : NULL;
    }


























}
