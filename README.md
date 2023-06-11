## Installation

* `git clone https://github.com/EvaldasBurlingis/nordcode-time-tracking-app.git`
* `cd nordcode-time-tracking-app`
* `cp .env.example .env`
* `docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v $(pwd):/var/www/html \
  -w /var/www/html \
  laravelsail/php81-composer:latest \
  composer install --ignore-platform-reqs`
* `sail up --build -d`
* New terminal tab `sail artisan key:generate`
* `sail artisan migrate:fresh --seed`
* `sail npm install`
* `sail npm run dev`
---

## Database seeder
To seed user and tasks to db run following command:
* `sail artisan module:seed Task`

This will create user with credentials


```
email: user1@mail.com
password: password
```
 
