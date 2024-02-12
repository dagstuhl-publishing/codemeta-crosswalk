# beta-codemeta-crosswalk

This repository has been developed as part of the FAIR4CoreEOSC project to address metadata conversions for some of Dagstuhl's
s use cases, namely,

    1. CodeMeta --> DataCite
    2. CodeMeta --> BibLatex
    3. CodeMeta --> BibTex

The pattern used in codemeta.json conversion is extendable to other metadata schemes. 

## Installation Steps:

    1) Clone this project.
    
    2) Open a console session and navigate to the cloned directory:
    
        Run "composer install"

        This should involve installing the PHP REPL, PsySH

    3) (optional) Add psysh to PATH
            
            Example, Bash: 
                    echo 'export PATH="$PATH:/The_Cloned_Directory/vendor/bin"' >> ~/.bashrc
                    source ~/.bashrc

    4) (Optional) Create your local branch.

## Usage:

- In a console session inside the cloned directory, start the php REPL:    

```php
$ psysh     // if not added to PATH replace with: vendor/bin/psysh

Psy Shell v0.12.0 (PHP 8.2.0 — cli) by Justin Hileman
```

- Define namespace:

```php
> namespace Conversions; 
> use Conversions;
```

- Specify codemeta.json path:

```php
> $codeMetaPath = 'CodeMeta/codeMeta.json'
```

> [!Note]
> By default, codemeta.json is located under 'CodeMeta' directory where an example already exists.
> $codeMetaPath can directly take codemeta.json as an array

- Specify target scheme

```php
> $dataCite = \Schemes\DataCite::class
> $bibLatex = \Schemes\BibLatex::class
> $bibTex   = \Schemes\BibTex::class
```

> [!Note]
> By default, scheme classes are located under 'Schemes' directory.

- Get the conversion from the specified Codemeta.json:

```php
> $errors = NULL;
 
> $dataCiteFromCodeMeta = CodeMetaConversion::To($dataCite, $codeMetaPath, $e)      // array-formatted

> $bibLatexFromCodeMeta = CodeMetaConversion::To($bibLatex, $codeMetaPath, $e)      // string-formatted

> $bibTexFromCodeMeta = CodeMetaConversion::To($bibTex, $codeMetaPath, $e)          // string-formatted
```

- Retrieve errors (if occurred) from the `Illuminate\Support\MessageBag()` instance:

```php
> $errors->messages()      // gets error messages as specified in CodeMeta/conversionsValidations.php

> $errors->keys()          // gets codemeta keys where errors occurred

> $errors->first()         // gets the first occurred error message

> $errors->has('identifier')    // checks whether an error has occurred in the codemeta `identifier` key
```

> [!Note]
> Validations use the `Illuminate\Validation\Validator` package.
> Error messages and rules can be customised in `CodeMeta/conversionsValidations.php` as per the package syntax.

        