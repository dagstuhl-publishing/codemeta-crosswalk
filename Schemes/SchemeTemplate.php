<?php

namespace Schemes;

use Conversions\CodemetaConversion;

class SchemeTemplate extends CodemetaConversion
{
    /*
     * Please consult: https://github.com/codemeta/codemeta/blob/master/crosswalk.csv
     * and the scheme documentation to properly construct this class
     */

    public const SCHEME = 'schemeName';       // name the scheme

    public const HEADING = [ 'customKey' => 'constants' ];             // add here any scheme constants in array format

    protected static array $codeMetaInitialTransformations = [      // define initial mappings name associated to the name of the codeMeta keys w.r.t scheme's documentation
        'codemetaKey' => [self::class, 'codemetaKey'],              // define maps in this class having the same name of the codemetaKey
        /*
        ⋮
        ⋮
        */
        ];

    protected static array $codeMetaDuplicatedTransformations = [               // define initial mappings name associated to the name of the codeMeta keys w.r.t scheme's documentation
        'customName' => ['codemetaKey1', 'codemetaKey2', 'codemetaKey3'],       // gather in this array the codemeta keys whose initial mappings can be the same
        /*                                                                      // define maps in this class having the same name of this 'customName'
        ⋮
        ⋮
        */
    ];

    protected static array $mappingsDescription = [
        'simpleKeys' => [
            'schemeKey' => 'codemetaKey',           // place in here the name of the scheme keys w.r.t codemeta keys in which their initial mapping above sufficed in their conversion
            /*
            ⋮
            ⋮
            */
        ],

        'complexKeys' => [          // place in this complex array the final transformation on the following set of codemeta keys name
            [
                'key' => 'schemeKey',       // schemeKeyName
                'schemeKey' => ['codemetaKey1', 'codemetaKey2', 'codemetaKey3'],    // set of codeMeta keys that require further mapping to reach the schemeKey final transformation
                'mapping' => [self::class, 'mapSchemeKey']      // the name of the final mapping prefixed with the word 'map'
            ],
            /*
            ⋮
            ⋮
            */
        ]
    ];

    protected static function codemetaKey(string $codeMetaValue)
    {
        // define the initial map on codemetaKey (key by key) as in the $codeMetaInitialTransformations array
        // $codeMetaValue here is the value of the codemetaKey (map name)
    }

    protected static function customName(string $codeMetaValue, string $codeMetaKey)
    {
        // define the duplicated map on codemeta keys grouping as defined above in $codeMetaDuplicatedTransformations
        // $codeMetaValue is the corresponding value for the codeMetaKey as defined in 'customName' => ['codemetaKey1', 'codemetaKey2', 'codemetaKey3'] of $codeMetaDuplicatedTransformations
    }

    protected static function mapSchemeKey(...$codeMetaValues)
    {
        // define the final mapping on the set of codemeta keys defined above in the 'schemeKey' => ['codemetaKey1', 'codemetaKey2', 'codemetaKey3']
        // these keys are accessible here as: codemetaValues['codemetaKey1'] ... codemetaValues['codemetaKey2'] ... codemetaValues['codemetaKey3']
    }


}