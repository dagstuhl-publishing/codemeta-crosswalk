<?php

/**
 * @Author: Ramy-Badr-Ahmed
 * @Desc: Codemeta conversions
 * @Repo: https://github.com/dagstuhl-publishing/beta-codemeta-crosswalk
 */

namespace Schemes;

use Conversions\CodeMetaConversion;
use Illuminate\Support\Arr;

class Bib extends CodemetaConversion
{
    private const LATEX_OPENING = "{";
    private const TEX_SEPARATOR = ",\n";
    private const LATEX_CLOSING = "}";
    private const BIBTEX_HEADING = "@misc{";
    private const BIBLATEX_HEADING = "@software{";
    private const BIBLATEX_VERSION_HEADING = "@softwareversion{";
    protected const AND_SEPARATOR = ' and ' ;
    protected const COMMA_SEPARATOR = ', ' ;

    protected function bibStringify(array $bibArray) : string
    {
        $bibKey = $bibArray['bibKey'];

        $bibArray = Arr::except($bibArray, 'bibKey');

        $bibArrayStringified = self::formatLatexContents($bibArray);

        $bibHeading = match(true){
            $this instanceof BibTex => self::BIBTEX_HEADING,
            $this instanceof BibLatex => self::BIBLATEX_HEADING,
            $this instanceof BibLatexVersion => self::BIBLATEX_VERSION_HEADING
        };

        $bibStringified = $bibHeading.$bibKey.self::TEX_SEPARATOR.$bibArrayStringified.self::LATEX_CLOSING;

        if($this instanceof BibLatex){
            self::getBibLatexVersion($bibKey, $bibStringified);
        }

        return $bibStringified;
    }

    private function getBibLatexVersion(string $bibKeyReference, string &$bibLatex): void
    {
        if(!isset($this->codeMeta['version'])) return;

        $codeMetaBibLatexArray = array_merge($this->convertedCodeMeta, ['bibLatexKey' => $bibKeyReference]);

        unset($this->codeMeta['version']);

        $bibLatexVersion = CodeMetaConversion::To(BibLatexVersion::class, $codeMetaBibLatexArray, bypass: true);

        $bibLatex .= "\n".$bibLatexVersion;
    }


    protected static function formatLatexContents(array $latexArray) : string
    {
        $latexString = collect($latexArray)
            ->map( fn ( $bibValue, $bibKey ) => "\t".$bibKey . str_repeat(' ', 3)."=".str_repeat(' ', 3)
                                                .self::LATEX_OPENING.$bibValue.self::LATEX_CLOSING.self::TEX_SEPARATOR)
            ->values()
            ->implode('');

        return preg_replace('/,\n$/', "\n", $latexString);
    }

    protected function getAuthorData4Latex(array $codeMetaAuthors): array
    {
        return [
            'bibTexAuthors' => implode(' and ', Arr::map($codeMetaAuthors, function ($authorArray){
                return $authorArray['familyName'].", ".$authorArray['givenName'];
            })),
            'emails' => implode(' and ', Arr::pluck($codeMetaAuthors, 'email')),
            'affiliations' => implode(' and ', Arr::pluck($codeMetaAuthors, 'affiliation.name'))
        ];
    }

}
