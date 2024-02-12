<?php

/**
 * @Author: Ramy-Badr-Ahmed
 * @Desc: Codemeta conversions
 * @Repo: https://github.com/dagstuhl-publishing/beta-codemeta-crosswalk
 */

namespace Schemes;

use Conversions\CodemetaConversion;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DataCite extends CodemetaConversion
{
    public const SCHEME = 'dataCite';           // validations/LW-View

    public const HEADING = [                    // any scheme constants
        "types" => [
            "resourceTypeGeneral" => "Software",
            "resourceType" => "Source Code",        // Mandatory
            "schemaOrg" => "SoftwareSourceCode",
            "bibtex" => "misc"
        ],
        "schemaVersion" => "http://datacite.org/schema/kernel-4"
    ];

    protected static array $codeMetaInitialTransformations = [      // codeMeta w.r.t scheme
        'name' => [self::class, 'name'],
        'funder' => [self::class, 'funder'],
        'license' => [self::class, 'license'],
        'keywords' => [self::class, 'keywords'],
        'publisher' => [self::class, 'publisher'],
        'identifier' => [self::class, 'identifier'],
        'relatedLink' => [self::class, 'relatedLink'],
    ];

    protected static array $codeMetaDuplicatedTransformations = [         // codeMeta w.r.t scheme
        'descriptions' => ['description', 'readme', 'releaseNotes'],
        'versions' => ['softwareVersion', 'version'],
        'fileMeta' => ['fileSize', 'fileFormat'],
        'person' => ['author', 'contributor'],
        'dates' => ['datePublished', 'dateCreated', 'dateModified'],
    ];

    protected static array $mappingsDescription = [             // scheme w.r.t codeMeta
        'simpleKeys' => [
            'relatedIdentifiers' => 'relatedLink',
            'fundingReferences' => 'funder',
            'contributors' => 'contributor',
            'identifiers' => 'identifier',
            'rightsList' => 'license',
            'publisher' => 'publisher',
            'creators' => 'author',
            'subjects' => 'keywords',
            'formats' => 'fileFormat',
            'titles' => 'name',
            'sizes' => 'fileSize',
        ],

        'complexKeys' => [
            [
                'key' => 'dates',
                'dates' => ['datePublished', 'dateCreated', 'dateModified'],
                'mapping' => [self::class, 'mapDates']
            ],
            [
                'key' => 'publicationYear',
                'publicationYear' => ['datePublished'],
                'mapping' => [self::class, 'mapPublicationYear']
            ],
            [
                'key' => 'descriptions',
                'descriptions' => ['description', 'readme', 'releaseNotes'],
                'mapping' => [self::class, 'mapDescriptions']
            ],
            [
                'key' => 'version',
                'version' => ['softwareVersion', 'version'],
                'mapping' => [self::class, 'mapVersion']
            ],
        ]

    ];

    protected static function name(string $codeMetaValue): array
    {
        return array(["title" => $codeMetaValue]);
    }

    protected static function keywords(string|array $codeMetaValue): array
    {
        return collect($codeMetaValue)
            ->map(fn($keyword) => ["subject" => $keyword])
            ->toArray();
    }

    protected static function relatedLink(string|array $codeMetaValue): array
    {
        return collect($codeMetaValue)
            ->map(fn($link) => [
                "relatedIdentifier" => $link,
                "relatedIdentifierType" => "URL",
                "relationType" => "IsSupplementTo"
            ])
            ->toArray();
    }

    protected static function identifier(string $codeMetaValue): array
    {
        return array(array_merge([
            'identifier' => $codeMetaValue,
            'identifierType' => 'URL'
        ],
            Arr::whereNotNull(['identifierType' => parent::isDOI($codeMetaValue) ? 'DOI' : NULL])
        ));
    }

    protected static function license(string $codeMetaValue): array
    {
        return array([
            "rightsUri" => $codeMetaValue,
            'rightsIdentifier' => parent::getLicenseByURL($codeMetaValue),
            "rightsIdentifierScheme" => "SPDX"
        ]);
    }

    protected static function funder(array $codeMetaValue): array
    {
        return Arr::map(Arr::isAssoc($codeMetaValue) ? [$codeMetaValue] : $codeMetaValue, function ($funderArray) {
            return Arr::whereNotNull(
                [
                    'funderName' => $funderArray['name'],
                    "funderIdentifier" => $funderArray['@id'],
                    'funderIdentifierType' => 'Crossref Funder ID',
                    'awardNumber' => $funderArray['funding'] ?? NULL
                ]);
        });
    }

    protected static function publisher(array $codeMetaValue): string
    {
        return $codeMetaValue['name'];
    }

    protected static function person(array $codeMetaValue, string $codeMetaKey): array
    {
        return Arr::map(Arr::isAssoc($codeMetaValue) ? [$codeMetaValue] : $codeMetaValue, function ($personArray) use ($codeMetaKey) {

            $names = Arr::where($personArray, fn($value, $key) => in_array($key, ['familyName', 'givenName']));

            return array_merge([
                "nameType" => "Personal",
                "name" => $personArray["familyName"] . ", " . $personArray["givenName"]
            ],
                $names,
                isset($personArray["@id"])
                    ? [
                    "nameIdentifiers" => array([
                        "nameIdentifier" => $personArray["@id"],
                        "nameIdentifierScheme" => Str::of(parse_url($personArray["@id"])['host'])->match('/ORCID|ISNI|ROR|GRID/i')->value()])
                ]
                    : [],
                $codeMetaKey === 'contributor'
                    ? ["contributorType" => "Researcher"]
                    : [],
                isset($personArray["affiliation"])
                    ? ["affiliation" => array(Arr::except($personArray["affiliation"], ["@type"]))]
                    : []
            );
        });
    }

    protected static function fileMeta(string|array $codeMetaValue): array
    {
        return collect($codeMetaValue)->toArray();
    }

    protected static function dates(string $codeMetaValue, string $codeMetaKey): array
    {
        return array(array_merge(["date" => $codeMetaValue], match ($codeMetaKey) {
            "dateCreated" => ["dateType" => "Created"],
            "datePublished" => ["dateType" => "Issued"],
            "dateModified" => ["dateType" => "Updated"]
        }));
    }

    protected static function descriptions(string $codeMetaValue): array
    {
        return array(["description" => $codeMetaValue, "descriptionType" => "TechnicalInfo"]);
    }

    protected static function versions(string $codeMetaValue): string
    {
        return $codeMetaValue;
    }

    protected static function mapDates(...$codeMetaValues): array|null
    {
        return collect(Arr::whereNotNull($codeMetaValues))
            ->whereNotNull()
            ->reduce(fn($carry, $arr) => array_merge($carry ?? [], $arr));
    }

    protected static function mapPublicationYear(...$codeMetaValue): string
    {
        $codeMetaValue = $codeMetaValue['datePublished'];
        return (string)date_parse($codeMetaValue[0]["date"])["year"];
    }

    protected static function mapDescriptions(...$codeMetaValues): array|null
    {
        return collect(Arr::whereNotNull($codeMetaValues))
            ->whereNotNull()
            ->reduce(function ($carry, $arr) {
                return array_merge($carry ?? [], $arr);
            });
    }

    protected static function mapVersion(...$codeMetaValues): string|null
    {
        $versions = collect($codeMetaValues)->whereNotNull();

        return $versions->isEmpty()
            ? NULL
            : $versions->implode('/');
    }


}
