# Twitter

[![Build Status](https://img.shields.io/travis/iPublikuj/twitter.svg?style=flat-square)](https://travis-ci.org/iPublikuj/twitter)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/iPublikuj/twitter.svg?style=flat-square)](https://scrutinizer-ci.com/g/iPublikuj/twitter/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/twitter.svg?style=flat-square)](https://packagist.org/packages/ipub/twitter)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/twitter.svg?style=flat-square)](https://packagist.org/packages/ipub/twitter)

Twitter API client with authorization for [Nette Framework](http://nette.org/)

## Installation

The best way to install ipub/twitter is using  [Composer](http://getcomposer.org/):

```json
{
	"require": {
		"ipub/twitter": "dev-master"
	}
}
```

or

```sh
$ composer require ipub/twitter:@dev
```

After that you have to register extension in config.neon.

```neon
extensions:
	twitter: IPub\Twitter\DI\TwitterExtension
```

> NOTE: Don't forget to register [OAuth extension](http://github.com/iPublikuj/oauth), because this extension is depended on it!

## Documentation

Learn how to authenticate the user using Twitter's oauth or call Twitter's api in [documentation](https://github.com/iPublikuj/twitter/blob/master/docs/en/index.md).

***
Homepage [http://www.ipublikuj.eu](http://www.ipublikuj.eu) and repository [http://github.com/iPublikuj/twitter](http://github.com/iPublikuj/twitter).