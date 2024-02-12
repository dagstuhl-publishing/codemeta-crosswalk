<?php

/**
 * @Author: Ramy-Badr-Ahmed
 * @Desc: Codemeta conversions
 * @Repo: https://github.com/dagstuhl-publishing/beta-codemeta-crosswalk
 */

namespace Conversions;

use ErrorException;
use Illuminate\Support\Arr;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Schemes\Bib;
use Schemes\BibLatex;
use Schemes\BibTex;
use Schemes\DataCite;
use Throwable;

class CodemetaConversion extends SchemeDescriptions
{
    use GlobalHelperFunctions;

    public array $codeMeta;
    protected array $filteredCodeMeta;      // subset relevant to child scheme
    protected array $convertedCodeMeta;     // collects converted codeMeta throughout
    public const SUPPORTED_FORMATS = [
        DataCite::SCHEME,
        BibLatex::SCHEME,
        BibTex::SCHEME
    ];
    private static array $validationsConfigs;
    private static array $rules;
    private static array $messages;
    private static array $attributes;

    /**
     * @throws ErrorException|Throwable
     * @throws ValidationException
     */
    public function __construct(string|array $codeMetaPathOrArray, &$codeMetaErrors = NULL, bool $bypass = false)
    {
        $this->codeMeta = is_string($codeMetaPathOrArray)
            ? json_decode(file_get_contents($codeMetaPathOrArray), true, 10, JSON_OBJECT_AS_ARRAY | JSON_INVALID_UTF8_SUBSTITUTE)
            : $codeMetaPathOrArray;

        if(!$bypass) $this->validate($codeMetaErrors);   // errors w.r.t scheme

        parent::__construct();

        $this->setFilteredCodeMeta();
    }

    /**
     * @throws ErrorException|Throwable
     * @throws ValidationException
     */
    public function validate(&$errors = null): void
    {
        self::readValidationConfigs();

        $codeMetaData = $this->initialiseArraysValidation();

        $validator = new Validator(new Translator(new ArrayLoader(), 'en'),$codeMetaData, self::$rules, self::$messages, self::$attributes);

        if($validator->fails()){
            $errors = $validator->errors();
            throw new ValidationException($validator);
        }
    }

    /**
     * @throws ErrorException|Throwable
     */
    private static function readValidationConfigs(): void
    {
        self::$validationsConfigs = require 'CodeMeta/conversionsValidations.php';

        self::$rules = self::$validationsConfigs['rules'][static::SCHEME] ?? [];
        self::$messages = self::$validationsConfigs['messages'][static::SCHEME] ?? [];
        self::$attributes = self::$validationsConfigs['attributes'][static::SCHEME] ?? [];

        throw_if(empty(self::$rules) || empty(self::$messages) || empty(self::$attributes), new ErrorException('Missing validation parameters'));
    }

    private function initialiseArraysValidation(): array
    {
        return array_merge($this->codeMeta, ['author' => $this->codeMeta['author'] ?? array([])],
            match(static::SCHEME){
                BibLatex::SCHEME, DataCite::SCHEME => ['publisher' => $this->codeMeta['publisher'] ?? [] ],
                default => []
            });
    }

    protected function setFilteredCodeMeta(): void
    {
        $this->filteredCodeMeta = Arr::only($this->codeMeta, parent::$codeMetaKeys);
    }

    public static function To(string $targetScheme, string|array $codeMetaPathOrArray, &$errors = NULL, $bypass = false): array|string
    {
        $converter = new $targetScheme($codeMetaPathOrArray, $errors, $bypass);

        $arrayConversion = $converter->getTargetConversion();

        return $converter instanceof Bib
            ? $converter->bibStringify($arrayConversion)
            : $arrayConversion;
    }

    protected function getTargetConversion(): array
    {
        $this->convertedCodeMeta = self::codeMetaInitialTransformations();

        $schemeConversion = $this->mapToScheme();

        return array_merge(defined("static::class::HEADING") ? static::HEADING : [], $schemeConversion);
    }

    protected function codeMetaInitialTransformations(): array
    {
        return Arr::map($this->filteredCodeMeta,
            fn($codeMetaValue, $codeMetaKey) => static::$codeMetaInitialTransformations[$codeMetaKey]($codeMetaValue, $codeMetaKey));
    }

    private function mapToScheme(): array
    {
        $codeMetaKeys = array_keys($this->convertedCodeMeta);

        $schemeSimpleMapping = $this->filterSchemeSimpleKeys($codeMetaKeys);

        $schemeSimpleConversion  = Arr::mapWithKeys($schemeSimpleMapping,
            fn ($codeMetaKey, $schemeKey) => [ $schemeKey => $this->convertedCodeMeta[$codeMetaKey] ] );

        $schemeComplexConversion = $this->codeMetaFinalTransformations();

        return array_merge($schemeSimpleConversion, $schemeComplexConversion);
    }

    protected function codeMetaFinalTransformations(): array
    {
        return Arr::mapWithKeys(static::$mappingsDescription['complexKeys'], function ($complexMappingArray){
            $schemeKey = $complexMappingArray['key'];

            $mappingArguments = collect($complexMappingArray[$schemeKey])
                ->mapWithKeys(fn($codeMetaKey) => [$codeMetaKey => $this->convertedCodeMeta[$codeMetaKey] ?? NULL ] )
                ->toArray();

            $schemeValue = $complexMappingArray['mapping'](...$mappingArguments);

            return Arr::whereNotNull([$schemeKey => $schemeValue]);
        });
    }

}
