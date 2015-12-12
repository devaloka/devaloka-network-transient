# Devaloka Transient [![Build Status](https://travis-ci.org/devaloka/devaloka-transient.svg?branch=master)](https://travis-ci.org/devaloka/devaloka-transient) [![Packagist](https://img.shields.io/packagist/v/devaloka/devaloka-transient.svg)](https://packagist.org/packages/devaloka/devaloka-transient)

A WordPress Plugin that provides Site-contextual Transient API.

## Features

*   Per-Site Transient value with a single Transient name to manipulate  

## Requirements

*   [Devaloka](https://github.com/devaloka/devaloka)

## Installation

1.  Install via Composer.

    ```sh
    composer require devaloka/devaloka-transient
    ```

2.  Move `loader/10-devaloka-transient-loader.php` into
    `<ABSPATH>wp-content/mu-plugins/`.
