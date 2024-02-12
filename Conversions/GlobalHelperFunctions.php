<?php

/**
 * @Author: Ramy-Badr-Ahmed
 * @Desc: Codemeta conversions
 * @Repo: https://github.com/dagstuhl-publishing/beta-codemeta-crosswalk
 */

namespace Conversions;

use Composer\Spdx\SpdxLicenses;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait GlobalHelperFunctions
{

    protected static function isDOI(string $url): bool
    {
        return preg_match('/https?:\/\/(dx\.)?doi\.org\/[a-zA-Z0-9.\/-]+/i', $url);
    }

    protected static function isSwhResolver(string $url): bool
    {
        $isMatching = preg_match('/(?<=https:\/\/archive\.softwareheritage\.org\/).*$/', $url, $m);
        if($isMatching){
            return Str::contains($m[0], 'swh:1:');
        }
        return false;
    }

    protected static function getLicenseByURL($url) : string
    {
        $license = Arr::flatten(Arr::where((new SpdxLicenses())->getLicenses(), function($licenseArray) use($url) {
            return Str::of($url)->match('/\/'.$licenseArray[0].'.html/i')->value();
        }));
        return empty($license) ? 'NULL' : $license[0].": ".$license[1];
    }

}
