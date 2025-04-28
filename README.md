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
</p>
<p>
    Supports:
    <a title="Supports Symfony ^6.4" href="https://github.com/Aerendir/component-array-writer/actions?query=branch%master"><img title="Supports Symfony ^6.4" src="https://img.shields.io/badge/Symfony-%5E6.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Supports Symfony ^7.4" href="https://github.com/Aerendir/component-array-writer/actions?query=branch%master"><img title="Supports Symfony ^7.4" src="https://img.shields.io/badge/Symfony-%5E7.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Supports Symfony ^5.4" href="https://github.com/Aerendir/component-array-writer/actions?query=branch%master"><img title="Supports Symfony ^8.0" src="https://img.shields.io/badge/Symfony-%5E8.0-333?style=flat-square&logo=symfony" /></a>
</p>
<p>
    Tested with:
    <a title="Supports Symfony ^6.4" href="https://github.com/Aerendir/component-array-writer/actions?query=branch%master"><img title="Supports Symfony ^6.4" src="https://img.shields.io/badge/Symfony-%5E6.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Supports Symfony ^7.4" href="https://github.com/Aerendir/component-array-writer/actions?query=branch%master"><img title="Supports Symfony ^7.4" src="https://img.shields.io/badge/Symfony-%5E7.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Supports Symfony ^5.4" href="https://github.com/Aerendir/component-array-writer/actions?query=branch%master"><img title="Supports Symfony ^8.0" src="https://img.shields.io/badge/Symfony-%5E8.0-333?style=flat-square&logo=symfony" /></a>
</p>

## Current Status

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=coverage)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=alert_status)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=security_rating)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=sqale_index)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-array-writer&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=Aerendir_component-array-writer)

[![Phan](https://github.com/Aerendir/component-array-writer/workflows/Phan/badge.svg)](https://github.com/Aerendir/component-array-writer/actions?query=branch%3Adev)
[![PHPStan](https://github.com/Aerendir/component-array-writer/workflows/PHPStan/badge.svg)](https://github.com/Aerendir/component-array-writer/actions?query=branch%3Adev)
[![PSalm](https://github.com/Aerendir/component-array-writer/workflows/PSalm/badge.svg)](https://github.com/Aerendir/component-array-writer/actions?query=branch%3Adev)
[![PHPUnit](https://github.com/Aerendir/component-array-writer/workflows/PHPunit/badge.svg)](https://github.com/Aerendir/component-array-writer/actions?query=branch%3Adev)
[![Composer](https://github.com/Aerendir/component-array-writer/workflows/Composer/badge.svg)](https://github.com/Aerendir/component-array-writer/actions?query=branch%3Adev)
[![PHP CS Fixer](https://github.com/Aerendir/component-array-writer/workflows/PHP%20CS%20Fixer/badge.svg)](https://github.com/Aerendir/component-array-writer/actions?query=branch%3Adev)
[![Rector](https://github.com/Aerendir/component-array-writer/workflows/Rector/badge.svg)](https://github.com/Aerendir/component-array-writer/actions?query=branch%3Adev)

<hr />
<h3 align="center">
    <b>Do you like this library?</b><br />
    <b><a href="#js-repo-pjax-container">LEAVE A &#9733;</a></b>
</h3>
<p align="center">
    or run<br />
    <code>composer global require symfony/thanks && composer thanks</code><br />
    to say thank you to all libraries you use in your current project, this included!
</p>
<hr />

## Available methods

- `getValue()`
- `getValueAndForget()`
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

## Install Serendipity HQ Array Writer via Composer

    $ composer require serendipity_hq/component-array-writer

This library follows the http://semver.org/ versioning conventions.

<hr />
<h3 align="center">
    <b>Do you like this library?</b><br />
    <b><a href="#js-repo-pjax-container">LEAVE A &#9733;</a></b>
</h3>
<p align="center">
    or run<br />
    <code>composer global require symfony/thanks && composer thanks</code><br />
    to say thank you to all libraries you use in your current project, this included!
</p>
<hr />
