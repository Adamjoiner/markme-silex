<?php

namespace App\Controller;

use Silex\WebTestCase;

/**
 * Test class for ImageController.
 * Generated by PHPUnit on 2012-12-22 at 11:23:33.
 */
class ImageControllerTest extends WebTestCase{

    /**
     * @var ImageController
     */
    protected $object;

    /**
     * @covers App\Controller\ImageController::get
     * @todo Implement testGet().
     */
    // public function testGet(){
    //     // Remove the following lines when you implement this test.
    //     $this->markTestIncomplete(
    //         'This test has not been implemented yet.'
    //     );
    // }

    /**
     * @covers App\Controller\ImageController::getByUrl
     * @todo Implement testGetByUrl().
     */
    public function testGetByUrl(){
        $client = $this->createClient();
        $client->request("POST", "/json/register", array(), array(),
            array("HTTP_Content-Type"=>"application/json"),
            json_encode(array("username"=>"supergirl","password"=>"password"
                ,"email"=>"supergirl@yahoo.com"))
        );
        $client->request("GET", "/image?url=http://stackoverflow.com/");
        $response = $client->getResponse();
        $this->assertNotNull($response);
    }

    public function createApplication(){
        return createApplication();
    }

}

?>
