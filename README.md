
## Flight Trip Builder

Flight Trip Builder is a simple API for building flights trip based on simple request from user.
The project is built using Laravel framework.

## Installation
Run ```composer install```

Copy .env.example into your .env file with ```cp .env.example .env```

Run ```php artisan key:generate``` to generate Laravel encryption key.

Check the .env file, change the DB config to the your local DB config.

Run ```php artisan migrate``` to migrate database. 

Run ```php artisan db:seed``` to add sample data to the tables. 

Run ```php artisan serve```, by default, ```http://127.0.0.1:8000/``` should be up and running !!!

## How to test the API. 

For simplicity purpose, the sample datas provide 3 airports from Montreal, Toronto and Vancouver respectively in the `airports` table. 2 airlines: `Air Canada` and `Air Transat` are provided in the `airlines` table. 

24 flights with routes from and to : `Montreal<->Toronto`, `Toronto<->Vancouver`, `Montreal<->Vancouver`, from both 2 airlines, with two schedule hour `07:00` and `15:00` are created.

The real physical distance fact is ignored, These airports are considered as 3 points: A, B and C. (meaning there will be flight options from Montreal to Vancouver and then to Toronto if user want to arrive from Montreal to Toronto).

Open your favorite API client to test, mine is Postman ! Add these parameters: 
```
{
    "departure_airport": "YUL",
    "arrival_airport": "YTZ",
    "departure_date": "2021-02-01",
    "return_date": "2021-02-20",
    "trip_type": "round-trip",
    "sort_by": "price",
    "sort_order": "asc",
    "preferred_airline": "Air Canada"
}

```
Optional Parameters are: `sort_by`, `sort_order` and `preferred_airline`. `return_date` is not required as long as the `trip_type` value is `one-way`.

Example of test url: ```http://127.0.0.1:8000/api/search_routes?departure_airport=YUL&arrival_airport=YTZ&departure_date=2021-02-01&return_date=2021-02-20&trip_type=round-trip```

** Remember to set your `Content-Type` Header to `application/json` !

** Feel free to fork. 
## Author
Vinh Truong
https://github.com/vinhtruong94

## License

Code released under the [MIT license](https://opensource.org/licenses/MIT).
