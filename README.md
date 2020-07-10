<p align="center">
    <a href="http://www.serendipityhq.com" target="_blank">
        <img style="max-width: 350px" src="http://www.serendipityhq.com/assets/open-source-projects/Logo-SerendipityHQ-Icon-Text-Purple.png">
    </a>
</p>

<h1 align="center">Serendipity HQ Array Writer</h1>
<p align="center">A class to write and read arrays.</p>
<p align="center">
    <a href="https://github.com/Aerendir/component-array-writer/releases"><img src="https://img.shields.io/packagist/v/serendipity_hq/component-array-writer.svg?style=flat-square"></a>
    <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square"></a>
    <a href="https://github.com/Aerendir/component-array-writer/releases"><img src="https://img.shields.io/packagist/php-v/serendipity_hq/component-array-writer?color=%238892BF&style=flat-square&logo=php" /></a>
    <a title="Tested with Symfony ^3.4" href="https://github.com/Aerendir/component-array-writer/actions"><img title="Tested with Symfony ^3.4" src="https://img.shields.io/badge/Symfony-%5E3.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Tested with Symfony ^4.4" href="https://github.com/Aerendir/component-array-writer/actions"><img title="Tested with Symfony ^4.4" src="https://img.shields.io/badge/Symfony-%5E4.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Tested with Symfony ^5.0" href="https://github.com/Aerendir/component-array-writer/actions"><img title="Tested with Symfony ^5.0" src="https://img.shields.io/badge/Symfony-%5E5.0-333?style=flat-square&logo=symfony" /></a>
</p>

## Current Status

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=coverage)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=alert_status)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=security_rating)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=sqale_index)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)

[![Phan](https://github.com/Aerendir/PHPArrayWriter/workflows/Phan/badge.svg)](https://github.com/Aerendir/PHPArrayWriter/actions)
[![PHPStan](https://github.com/Aerendir/PHPArrayWriter/workflows/PHPStan/badge.svg)](https://github.com/Aerendir/PHPArrayWriter/actions)
[![PSalm](https://github.com/Aerendir/PHPArrayWriter/workflows/PSalm/badge.svg)](https://github.com/Aerendir/PHPArrayWriter/actions)
[![PHPUnit](https://github.com/Aerendir/PHPArrayWriter/workflows/PHPunit/badge.svg)](https://github.com/Aerendir/PHPArrayWriter/actions)
[![Composer](https://github.com/Aerendir/PHPArrayWriter/workflows/Composer/badge.svg)](https://github.com/Aerendir/PHPArrayWriter/actions)
[![PHP CS Fixer](https://github.com/Aerendir/PHPArrayWriter/workflows/PHP%20CS%20Fixer/badge.svg)](https://github.com/Aerendir/PHPArrayWriter/actions)
[![Rector](https://github.com/Aerendir/PHPArrayWriter/workflows/Rector/badge.svg)](https://github.com/Aerendir/PHPArrayWriter/actions)

## Available methods

- `getValue()`
- `getValueByPartialKey()`
- `isNode()`
- `isReadable()`
- `isRoot()`
- `isWritable()`
- `keyExistsNested()`
- `add()`
- `cp()`
- `cpSafe()`
- `edit()`
- `merge()`
- `mv()`
- `mvSafe()`
- `mvUp()`
- `rm()`
- `wrap()`
- `pathize()`
- `unpathize()`
- `forceArray()`

For details about each method, please, read the comments in the code: they are really simple to be read, trust me!

## Install Serendipity HQ Text Matrix via Composer

    $ composer require serendipity_hq/component-array-writer

This library follows the http://semver.org/ versioning conventions.
