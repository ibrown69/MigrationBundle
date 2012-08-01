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

```sh
migration:init
```
Create migration table on database if it is not created yet.

```sh
migration:setup
```
Import `schema.sql` and `data.sql` to database.


```sh
migration:new
```
Create a new migration file.

```sh
migration:apply
```
Apply migration scripts which were not applied.
