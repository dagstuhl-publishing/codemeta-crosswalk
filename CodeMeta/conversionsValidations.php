<?php

/**
 * @Author: Ramy-Badr-Ahmed
 * @Desc: Codemeta conversions
 * @Repo: https://github.com/dagstuhl-publishing/beta-codemeta-crosswalk
 */

return [
    "rules" => [
        "bibTex" => [
            'name' => 'required',
            'identifier' => 'required',
            'author.*.givenName' => 'required_with:author.*.familyName',
            'author.*.familyName' => 'required_with:author.*.givenName',
        ],
        "bibLaTex" => [
            'name' => 'required',
            'dateCreated' => 'required',
            'dateModified' => 'required_with:version',
            'publisher.url' => 'required',
            'identifier' => 'required',
            'author.*.givenName' => 'required',
            'author.*.familyName' => 'required',
        ],
        "dataCite" => [
            'name' => 'required',
            'datePublished' => 'required',
            'publisher.name' => 'required',
            'identifier' => 'required',
            'funder.name' => 'required_with:funder.@id',
            'funder.*.name' => 'required_with:funder.*.@id',
            'funder.@id' => 'required_with:funder.name',
            'funder.*.@id' => 'required_with:funder.*.name',
            'author.*.givenName' => 'required',
            'author.*.familyName' => 'required',
            'contributor.*.givenName' => 'required_with:contributor.*.familyName',
            'contributor.*.familyName' => 'required_with:contributor.*.givenName',
            'contributor.givenName' => 'required_with:contributor.familyName',
            'contributor.familyName' => 'required_with:contributor.givenName',
        ],
        "codeMeta" => [
            'publisher' => 'string|required_with:url',
            'url' => 'url|nullable',
            'downloadUrl' => 'url|nullable',
            'installUrl' => 'url|nullable',
            'buildInstructions' => 'url|nullable',
            'isPartOf' => 'url|nullable',
            'hasPart' => 'url|nullable',
            'readme' => 'url|nullable',
            'codeRepository' => 'url|nullable',
            'contIntegration' => 'url|nullable',
            'issueTracker' => 'url|nullable',
            'referencePublication' => 'url|nullable',
            'funder.name' => 'string|required_with:funder.@id,funder.funding',
            'funder.*.name' => 'string|required_with:funder.*.@id,funder.*.funding',
            'funder.@id' => 'url|nullable',
            'funder.*.@id' => 'url|nullable',
            'author.*.givenName' => 'string|required',
            'author.*.email' => 'email|nullable',
            'author.*.@id' => 'url|nullable',
            'contributor.*.email' => 'email|nullable',
            'contributor.email' => 'email|nullable',
            'contributor.*.@id' => 'url|nullable',
            'contributor.@id' => 'url|nullable',
            'maintainer.*.email' => 'email|nullable',
            'maintainer.email' => 'email|nullable',
            'maintainer.*.@id' => 'url|nullable',
            'maintainer.@id' => 'url|nullable',
        ]
    ],
    "messages" => [
        "bibTex" => [
            'name.required' => 'bibTex: The :attribute cannot be empty',
            'identifier.required' => 'bibTex: The :attribute cannot be empty',
            'author.*.givenName.required_with' => 'bibTex: @:position firstName is required with lastName',
            'author.*.familyName.required_with' => 'bibTex: @:position lastName is required with firstName'
        ],
        "bibLaTex" => [
            'name.required' => 'bibLaTex: The :attribute cannot be empty',
            'dateCreated.required' => 'bibLaTex: Creation date cannot be empty',
            'dateModified.required_with' => 'bibLaTex: :attribute cannot be empty with non-empty version',
            'publisher.url.required' => 'bibLaTex: Publication URL cannot be empty',
            'identifier.required' => 'bibLaTex: The :attribute cannot be empty',
            'author.*.givenName.required' => 'bibLaTex: Author :position firstName cannot be empty',
            'author.*.familyName.required' => 'bibLaTex: Author :position lastName cannot be empty'
        ],
        "dataCite" => [
            'name.required' => 'dataCite: The :attribute cannot be empty',
            'datePublished.required' => 'dataCite: Publication date cannot be empty',
            'publisher.name.required' => 'dataCite: Publisher cannot be empty',
            'identifier.required' => 'dataCite: The :attribute cannot be empty',
            'funder.*.name.required_with' => "dataCite :attribute cannot be empty with non-empty URI",
            'funder.name.required_with' => "dataCite :attribute cannot be empty with non-empty URI",
            'funder.*.@id.required_with' => "dataCite: :attribute cannot be empty with non-empty Funder",
            'funder.@id.required_with' => "dataCite: :attribute cannot be empty with non-empty Funder",
            'author.*.givenName.required' => 'dataCite: Author :position firstName cannot be empty',
            'author.*.familyName.required' => 'dataCite: Author :position lastName cannot be empty',
            'contributor.*.givenName.required_with' => 'dataCite: Contributor :position firstName cannot be empty with non-empty lastName',
            'contributor.*.familyName.required_with' => 'dataCite: Contributor :position lastName cannot be empty with non-empty firstName',
            'contributor.givenName.required_with' => 'dataCite: Contributor firstName cannot be empty with non-empty lastName',
            'contributor.familyName.required_with' => 'dataCite: Contributor lastName cannot be empty with non-empty firstName',
        ],
        "codeMeta" => [
            'publisher.required_with' => 'codeMeta: :attribute must be provided if its URL is',
            'url.url' => 'codeMeta: Please provide a valid URL',
            'installUrl.url' => 'codeMeta: Please provide a valid URL',
            'downloadUrl.url' => 'codeMeta: Please provide a valid URL',
            'buildInstructions.url' => 'codeMeta: Please provide a valid URL',
            'isPartOf.url' => 'codeMeta: Please provide a valid URL',
            'hasPart.url' => 'codeMeta: Please provide a valid URL',
            'readme.url' => 'codeMeta: Please provide a valid URL',
            'codeRepository.url' => 'codeMeta: Please provide a valid URL',
            'contIntegration.url' => 'codeMeta: Please provide a valid URL',
            'issueTracker.url' => 'codeMeta: Please provide a valid URL',
            'referencePublication.url' => 'codeMeta: Please provide a valid URL',
            'funder.name.required_with' => "codeMeta: :attribute cannot be empty with non-empty URI/Funding",
            'funder.*.name.required_with' => "codeMeta: :attribute cannot be empty with non-empty URI/Funding",
            'funder.*.@id.url' => "codeMeta: Funder :position URL is invalid",
            'funder.@id.url' => "codeMeta: Funder URL is invalid",
            'author.*.givenName.required' => "codeMeta: Author :position name cannot be empty",
            'author.*.email.email' => "codeMeta: Author :position email is invalid",
            'author.*.@id.url' => "codeMeta: Author :position URL is invalid",
            'contributor.*.email.email' => "codeMeta: Contributor :position email is invalid",
            'contributor.email.email' => "codeMeta: Contributor :position email is invalid",
            'contributor.*.@id.url' => "codeMeta: Contributor :position URL is invalid",
            'contributor.@id.url' => "codeMeta: Contributor :position URL is invalid",
            'maintainer.*.email.email' => "codeMeta: Maintainer :position email is invalid",
            'maintainer.email.email' => "codeMeta: Maintainer :position email is invalid",
            'maintainer.*.@id.url' => "codeMeta: Maintainer :position URL is invalid",
            'maintainer.@id.url' => "codeMeta: Maintainer :position URL is invalid",
        ]
    ],
    "attributes" => [
        "bibTex" => [
            'name' => 'SW Name used for bibTex key',
            'identifier' => 'identifier used for bibTex key'
        ],
        "bibLaTex" => [
            'name' => 'SW Name used for bibLaTex key',
            'identifier' => 'identifier used for bibLaTex key',
            'dateModified' => 'Release Date'
        ],
        "dataCite" => [
            'name' => 'SW Name used for dataCite titles',
            'identifier' => 'identifier used for dataCite identifiers',
            'funder.*.name' => 'Funder :position',
            'funder.name' => 'Funder Name',
            'funder.*.@id' => 'Funder :position URI',
            'funder.@id' => 'Funder URI',
        ],
        "codeMeta" => [
            'publisher' => 'Publisher name',
            'funder.*.name' => 'Funder :position',
            'funder.name' => 'Funder Name',
        ]
    ]
];

