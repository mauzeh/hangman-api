[![Build Status](https://api.travis-ci.org/mauzeh/hangman-api.svg?branch=master)](https://travis-ci.org/mauzeh/hangman-api)

# Hangman API #

This repository contains a minimal implementation of a hangman API. This implementation was made for demonstration purposes.
 
The intended audience of this repository is code reviewers and technical recruiters.

## Installation and testing ##

Install this Symfony app by first cloning this repository:

```bash
git clone https://github.com/mauzeh/hangman-api.git
```

Then, cd into the repository:

```bash
cd hangman-api/
```

You need [Composer](https://getcomposer.org/doc/00-intro.md) to initialize the app's dependencies:

```bash
composer update --prefer-dist
```

You need [PHPUnit](https://phpunit.de/manual/current/en/installation.html) to run the tests by executing:

```bash
phpunit -c app/
```

Note: **you do NOT need to initialize a database**, as the tests will run using a Sqlite database which is created on-the-fly.

PHPUnit is included in `composer.json` so you may also invoke the tests by executing it from the vendor folder by running `vendor/phpunit/phpunit/phpunit -c app/`.

## Notes to the reviewer ##
 
* The API documentation is available via `/api/doc`.

* This API assumes JSON data. A more elaborate API could include other data formats, which could be derived from the HTTP-Accept header or from the filename extension used in the URI.

* A simplified authentication mechanism is implemented. More elaborate APIs could use an implementation of Symfony's [`SimplePreAuthenticatorInterface`](http://api.symfony.com/2.6/Symfony/Component/Security/Core/Authentication/SimplePreAuthenticatorInterface.html).

* The test data fixture no longer contains a GameData fixture. Games are created in-memory in the [`GameProcessorTest`](src/Hangman/Bundle/ApiBundle/Tests/GameProcessorTest.php) class.

* The random word selector supplied in the original assignment contained a non-agnostic query function: `CEIL()`. To allow the app to remain database-agnostic, the random word selector has been replaced with a database-agnostic version.

The API contains the following resources:

## Resources ##

**`POST /games`: Start a new game**

A list of words can be found in the MySQL database. At the start of the game a random word should be picked from this list.

**`PUT /games/[:id]`: Guess a started game**

- Guessing a correct letter does not decrement the number of tries left.
- Only valid characters are a-z.

## Response ##

Every response contains the following fields:

*word*: representation of the word that is being guessed. Contains dots for letters that have not been guessed yet (e.g. aw.so..).

*tries_left*: the number of tries left to guess the word (starts at 11).

*status*: current status of the game (`busy` | `fail` | `success`).