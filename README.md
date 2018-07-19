# rest-test-helper

1. Run command: ``composer require kglogowski/rest-test-helper``

2. Basic usage:
    ```php
    class TestUserController extends AbstractController
    {
       const ROUTE = 'user.get';

       /**
        * testGetSuccess
       */
       public function testGetSuccess()
       {
           $crawler = $this->createCrawler();
     
           $crawler
               ->click(
                   Request::METHOD_GET, //Request type
                   $this->getRequestHeaders(), //Headers, overwrite method
                   $this->getUrl(self::ROUTE, ['id' => 1]) //Generate url
               )
               ->checkStatus(Response::HTTP_OK) //Check status response
               ->child('id') //Go to child
                   ->assertActive(ResponseCrawlerInterface::ASSERT_EQUALS, [
                       1
                   ])
               ->end()
               ->child('email')
                   ->assertActive(ResponseCrawlerInterface::ASSERT_NOT_NULL)
               ->end()
           ;
       }
    }
    ```
    For this example, json response should look like:
    ```json
    {
       "id": 1,
       "login": "username"
    }
    ```
    
3. Test with json in request:
    
    Request json:
    ```json
    {
     "first_name": "FirstName",
     "last_name": "Surname",
     "email": "email@email.com",
     "role": "ROLE_ADMIN",
     "status": "ACTIVE"
    }
    ```
    path to mock json: /mock/test.json

    Code:
    ```php
    class TestUserController extends AbstractController
    {
       const ROUTE = 'user.post';

       /**
        * testGetSuccess
       */
       public function testPostSuccess()
       {
           $crawler = $this->createCrawler();
     
           $crawler
               ->click(
                   Request::METHOD_POST,
                   $this->getRequestHeaders(),
                   $this->getUrl(self::ROUTE),
                           $this->getJsonMockFileContent($file)
                   
                   ;
        
           /**
            * {@inheritdoc}
            */
           public function getMockDir(): string
           {
               return __DIR__ . '/mock/';
           }
       }
    }
    ```