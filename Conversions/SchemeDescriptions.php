<?php

/**
 * @Author: Ramy-Badr-Ahmed
 * @Desc: Codemeta conversions
 * @Repo: https://github.com/dagstuhl-publishing/beta-codemeta-crosswalk
 */

namespace Conversions;

use Illuminate\Support\Arr;

abstract class SchemeDescriptions
{
    protected static array $codeMetaInitialTransformations = [];
    protected static array $mappingsDescription = [];
    protected static array $codeMetaDuplicatedTransformations = [];
    protected static array $codeMetaKeys;
    protected static array $schemeKeys;
    protected static array $schemeSimpleKeys;
    protected static array $schemeComplexKeys;

    public function __construct()
    {
        if(isset(static::$codeMetaDuplicatedTransformations)) {
            static::$codeMetaInitialTransformations += self::appendDuplicatedTransformations();
        }
        self::setCodeMetaKeys();
        self::setSchemeKeys();
    }

    private static function appendDuplicatedTransformations(): array
    {
        $duplicatedTransformations = [];
        foreach (static::$codeMetaDuplicatedTransformations as $mapping => $codeMetaKeys){
            $duplicatedTransformations += array_fill_keys($codeMetaKeys, [static::class, $mapping]);        // [ $codeMetaKey => [static::class, $mapping] ]
        }
        return $duplicatedTransformations;
    }

    private static function setCodeMetaKeys(): void
    {
        self::$codeMetaKeys = array_keys(static::$codeMetaInitialTransformations);
    }

    private static function setSchemeKeys(): void
    {
        self::$schemeSimpleKeys = array_keys(static::$mappingsDescription['simpleKeys']);
        self::$schemeComplexKeys = Arr::pluck(static::$mappingsDescription['complexKeys'], 'key');

        self::$schemeKeys = array_merge(self::$schemeSimpleKeys, self::$schemeComplexKeys);
    }

    protected function filterSchemeSimpleKeys(array $codeMetaFilteredKeys): array
    {
        return Arr::where(static::$mappingsDescription['simpleKeys'], function ($codeMetaKey) use ($codeMetaFilteredKeys){
            return Arr::has(array_flip($codeMetaFilteredKeys), $codeMetaKey);
        });
    }

    protected static function getCodeMetaKeys(): array
    {
        return self::$codeMetaKeys;
    }

    protected static function getSchemeKeys(): array
    {
        return self::$schemeKeys;
    }

}
