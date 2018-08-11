# TwitterCrawler

Build a network of related users on Twitter by crawling who follows who. An instance would be @F1 will follow F1-related users, and they most likely the same. This is designed to maximise the crawl speed without breaking Twitter API throttling.

## Requirements
 - PHP 7.0
 - Nginx
 - MySQL 5.6

## Installation
Fastest way to install and get requirements up and running is using [Laravel Homestead](https://laravel.com/docs/homestead).

Clone into Homestead directory.
```
git clone https://github.com/johanleroux/twitter-crawler.git
```

Set working directory to twitter-crawler

Copy over environment file and update details to your environment.
```
cp .env.example .env
```

Update dependencies
```
composer update
```

Set Application Key
```
php artisan key:generate
```

Set Twitter API Tokens in .env file
```
TWITTER_CONSUMER_KEY=
TWITTER_CONSUMER_SECRET=
TWITTER_ACCESS_TOKEN=
TWITTER_ACCESS_TOKEN_SECRET=
```

Migrate DB
```
php artisan migrate:fresh
```

## Usage
Visit the crawler through your browser and add the first Twitter account to crawl and run console command `php artisan twitter:fetchFollowing` or rather setup a cron job to run `php artisan schedule:run` every minute.

## License
The MIT License (MIT). Please see [License File](license.md) for more information.