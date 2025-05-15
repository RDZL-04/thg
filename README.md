# THG Web Master Version 1.0

THG Web for Web Master Version and Api Version
## Overview
This application is written in Laravel Framework version 8.0

### Installation / Deployment

THG Web Master Version requires php version > 7.1.0 and php composer
using npm for bootstrap and other js/css library


Install the dependencies and devDependencies and start the server.

```sh
$ cd your-project-directory
$ php composer install
$ php artisan key:generate
```

### Setup .env file

Copy .env.exam:
```sh
$ cp ./.env.exam ./.env

Find and Edit `APP_ENV` value with app stage 
e.g : local for localhost 
      dev for thg.arkamaya.net
      prod for thg production
```sh
APP_ENV=local
```
```

Find and Edit `API_KEY` value :
```sh
API_KEY=replace_with_your_api_key_value
```

## Copyright

Copyright (c) 2021, [PT. Arkamaya](http://www.arkamaya.co.id) All Rights Reserved.



