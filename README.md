# oat-assessment-api-test

How to define the file to use (CSV or JSON) for the API:
 - Open config/services.yaml file
 - Modify QuestionService first argument with one of those services:
    - CsvDataProvider
    - JsonDataProvider

For example:
    App\Service\QuestionService:
            arguments:
                - '@App\Service\Provider\CsvDataProvider'
