## Usage 

- Install composer
- Run `composer install` 
- open `config/metric_reports.php` > edit `app_url` to your domain *to be used in testing* for example : `http://super-metrics.local/` don't forget to add the last slash `/`
- **Serve the project** and navigate to `/` the main page
- Main page lists all required/exists reports
- Click on any report to get the JSON response


## Description 
- The target was developing small core basic framework (requests,response,routing,testing) 
to be generic, extendable, easy to maintain by other staff.                                                                                                                              members

- Also, have a list of links that represent each report task, to have easily access on it 

## Configuration 
- `app_url` : domain for example : `http://super-metrics.local/` don't forget to add the last slash `/`
- `reports` : contains the allowed reports to be generated by the code
- `base_uri` : which the api link that will be calling for the data
- `client_id` : (String) required for `register` token api
- `email` : (String) required for `register` token api
- `name` : (String) required for `register` token api
- `max_page_number` : (int) max post pages 

## Available Reports 
- Average character length of posts per month

*Each Object Represent a Month, and the target value with index `average`* 
```
[
  { 
    "month": 5,
    "month_title": "May",
    "sum_message_length": 83964,
    "total_posts": 218,
    "average": 385
  },

  {
    "month": 6,
    "month_title": "June",
    "sum_message_length": 65767,
    "total_posts": 164,
    "average": 401
  }
]
```
- Longest post by character length per month

*Each Object Represent a Month, and the target value with index `post` and it's good to know the max post lenght `max_post_length`* 
```
[
  "month": 5,
  "month_title": "May",
  "max_post_length": 768,
  "post": {
    "id": "post5fadf4463923d_0991feb8",
    "from_name": "Rosann Eide",
    "from_id": "user_9",
    "message": "message here !!!",
    "message_length": 768,
    "type": "status",
    "created_time": "2020-05-26",
    "year": 2020,
    "month": 5,
    "week": 22,
    "day": 26
  }
},
]
```
- Total posts split by week number
*Each Object Represent a Week Number, and the target value with index `total_posts`*
```
[
  {
    "week_number": 46,
    "total_posts": 27
  },
  {
    "week_number": 32,
    "total_posts": 38
  },
  
]
```

- Average number of posts per user per month

*Each Object Represent a User , and in each user array of Months* 
```
[
  {
    "5": {
      "from_id": "user_19",
      "month": 5,
      "month_title": "May",
      "sum_message_length": 4719,
      "total_posts": 11,
      "average": 429
    },
    
  },
  {
    "5": {
      "from_id": "user_18",
      "month": 5,
      "month_title": "May",
      "sum_message_length": 3600,
      "total_posts": 13,
      "average": 277
    },
    
  }
]
```

## Testing 

- Run Report Testing by `./vendor/bin/phpunit tests/MetricReportTest.php`
- This will assure that all reports is working and return the data.

```
Testing Average character length of posts per month Report ... Working...
Testing Longest post by character length per month Report ... Working...
Testing Total posts split by week number Report ... Working...
Testing Average number of posts per user per month Report ... Working...
```

## Used Packages 

- **symfony/http-foundation** acts as a top-level layer for dealing with the HTTP flow. (Request and Response).

- **symfony/http-kernel** It is intended to work with HttpFoundation to convert the Request instance to a Response one.

- **symfony/routing** allows us to load Route objects into a UrlMatcher that will map the requested URI to a matching route.

- **symfony/phpunit-bridge** to report legacy tests and usage of deprecated code and helpers for mocking native functions

- **nesbot/carbon**  A simple PHP API extension for DateTime.

- **tightenco/collect** Collect - Illuminate Collections : provides a fluent, convenient wrapper for working with arrays of data.

- **twig/twig** templating environment
