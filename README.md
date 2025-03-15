## Data Aggregation and Storage
This repository aggredates data from various news API and stores it in its database, allowing for user preference, seach and each of news accessibility.

## Technical Information
This is a laravel project which requires a database, postman or similar application.

Requirements: 
- PHP 8.1 or Higher
- MySQL
- Composer
- Internet Access (to install dependencies and for sync)


## Running the application (Development)
- Follow the instructions carefully

- To get started, create an empty directory and navigate into it

```bash
mkdir news_api && cd news_api
```

- Clone the repository into the folder
```bash
git clone https://github.com/beisong7/news_api.git .
```

- Install dependencies with composer
```bash
composer install
```

- Configure your environment variables
```bash
cp .env.example .env
```
- update the provided API Keys with the keys acquired from the following sources:
    - World News API: https://worldnewsapi.com
    - News API: https://newsapi.org
    - The Guardian: https://open-platform.theguardian.com/documentation

    - you may wish to add a generated key to the `SYNC_KEY` value in the `.env` but this is optional as you can achieve sync from the CLI and is only concidered if you wish to handle sync using cloud scheduler services.

    - Update your database connection information on the `.env` file

- Initiate required environment keys
```bash
php artisan key:generate
php artisan jwt:secret
```

- Refresh Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

- Handle Database Migration
```bash
# with seed
php artisan migrate --seed

# without seed
php artisan migrate
```
This will provision all required database table and will handle the News Sources as well.

- Run SYNC with CLI
```bash
php artisan news:sync
```
This will fetch the news from the news sources in the database and aggregate the data in the database 

## Schedule SYNC
The application has a scheduled task to fetch all news every hour. To view the list, run the command
```bash
php artisan schedule:list
```
this will show all available scheduled task and their next run

## Using PostMan
- To ru the application, use the command
```bash
php artisan serve
```
This will run the application on http://127.0.0.1:8000

You can access the endpoints using postman with the url: `http://127.0.0.1:8000` or access the application from your browser to be sure it is working.
