# oat-assessment-api-test
How to test the API:
    
    Requirements:
        - php 7.1+ 
        - composer
        
    Installation:
        - clone the Github repository (oat-assessment-api-test)
        - run composer install
        - download CA certificate (https://curl.haxx.se/ca/cacert.pem) into extra/ssl directory of php
        - update php.ini file with the certificate
            > curl.cainfo = "Path\to\php\php<version>\extras\ssl\cacert.pem"
        - start local server
            > php -S 127.0.0.1:1988 -t public 
        
    Execution:
    (use Postman for more facilities, but can be done with curl)
        - Show all questions
            > call http://127.0.0.1:1988/questions?lang=fr with GET method
        
        - Create a new question
            > call http://127.0.0.1:1988/questions with POST method
                Mandatory parameters:
                    text:<The question text>
                    choice1:<First choice>
                    choice2:<Second choice>
                    choice3:<Third choice>
                Optional parameter:
                    createdAt:<Date of creation> (can be formatted like <Y-m-d> or <Y-m-d H:i:s>)
                    
How to define the file to use (CSV or JSON) for the API:

 - Open config/services.yaml file
 - Modify QuestionService first argument with one of those services:
    - CsvDataProvider
    - JsonDataProvider

For example:

    App\Service\QuestionService:
            arguments:
                - '@App\Service\Provider\CsvDataProvider'
