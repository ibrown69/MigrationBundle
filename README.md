MigrationBundle
===============

MySQL schema migration bundle.

Requirements
------------

* Doctrine DBAL with configuration.

Install
-------

1. Use [Composer](http://getcomposer.org/) package manager to download bundle:

Add repository to `composer.json`:

```json
"repositories": [
    {
        "type": "package",
        "package": {
            "name": "Estina/MigrationBundle",
            "version": "master",
            "source": {
                "url": "git://github.com/Estina/MigrationBundle.git",
                "type": "git",
                "reference": "master"
            }
        }
    }
]
```

Add bundle to requirements:

```json
"require": {
    "Estina/MigrationBundle": "*"
}
```

2. Create `schema/migrations` folders on project root dir.

Usage
-----

Create migration table on database if it is not created yet.
```sh
migration:init
```

Import `schema.sql` and `data.sql` to database.
```sh
migration:setup
```

Create a new migration file.
```sh
migration:new
```

Apply migration scripts which were not applied.
```sh
migration:apply
```
