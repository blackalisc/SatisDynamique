<?php

use Silex\WebTestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class SatisManagerTest extends WebTestCase
{
   
    public function setUp()
    {
        parent::setUp();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->copy(__DIR__ . "/../../Needs/composer_satis.json", __DIR__ . "/../../tmp/composer_satis.json");
        
        parent::setUp();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->remove( __DIR__ . "/../../tmp/");
    }
    
    public function createApplication()
    {
        require   __DIR__ .  "/../../Needs/config.dist.php";
        Cnerta\Utils\Utils::setUpPorxyConfig($config);
        
        // Silex
        $app = new Application();
                
        require __DIR__ . '/../../../../src/Cnerta/app.php';
        
        return $this->app = $app;
    }
    
    public function test404()
    {
        $client = $this->createClient();

        $client->request('GET', '/give-me-a-404');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testShouldGetAllPackage()
    {
        $client = $this->createClient();
        
        $client->request('GET', '/pakages');

        $this->assertEquals('application/json', $client->getResponse()->headers->get("content-type"));
        
        $content = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertTrue(isset($content['packages'][0]["name"]));
        $this->assertEquals("symfony/symfony", $content['packages'][0]["name"]);
    }
    
    public function testShouldPostPackage()
    {
        $client = $this->createClient();
                
        $json = array(
            "package" => array(
                'name' => "cnerta/breadcrumb-bundle", "version" => "~1.0"
                )
            );

        $client->request('POST', '/pakage', $json); 

        $client->request('GET', '/pakages');
        
        $content = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertTrue(isset($content['packages'][11]["name"]));
        $this->assertEquals("cnerta/breadcrumb-bundle", $content['packages'][11]["name"]);
        $this->assertEquals("~1.0", $content['packages'][11]["version"]);
    }
    
    public function testShouldFailAtPostPackage()
    {
        $client = $this->createClient();
        
        $json = array(
            "package" => array(
                'name' => "doctrine/doctrine-bundle", "version" => "~1.2"
                )
            );
        
        $client->request('POST', '/pakage', $json);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertEquals('"This package already exist doctrine\/doctrine-bundle"', $client->getResponse()->getContent());
    }
        
    public function testShouldDeletePackage()
    {
        $client = $this->createClient();
        
        $client->request('DELETE', '/pakage?package=' .  json_encode(array('name' => "sensio/framework-extra-bundle")));
        
        $client->request('GET', '/pakages');
        $content = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertFalse(isset($content["sensio/framework-extra-bundle"]));
    }
    
    public function testShouldNotDeletePackage()
    {
        $client = $this->createClient();

        $client->request('DELETE', '/pakage?package=' .  json_encode(array('name' => "dummy/package")));
        
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
    
    public function testShouldGetAllRepository()
    {
        $client = $this->createClient();
        
        $client->request('GET', '/repositories');

        $this->assertEquals('application/json', $client->getResponse()->headers->get("content-type"));
        
        $content = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertTrue(isset($content['repositories'][0]["type"]));
        $this->assertEquals("composer", $content['repositories'][0]["type"]);
    }
    
    public function testShouldAddRepositorySimple()
    {
        $client = $this->createClient();

        $repository = array (
            "repository" => array (
                    "type" => "git",
                    "url" => "git@github.com:waldo2188/SatisDynamique.git"
                )
            );
        
        $client->request('POST', '/repository', $repository);
        
        $client->request('GET', '/repositories');
        $content = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertEquals($repository['repository'], $content['repositories'][5]);
    }
    public function testShouldFailAtAddingRepositorySimple()
    {
        $client = $this->createClient();

        $repository = array (
            "repository" => array (
                    "type" => "git",
                    "url" => ".com:waldo2188/SatisDynamique.git"
                )
            );
        
        $client->request('POST', '/repository', $repository);
                
        $this->assertEquals('"The URL :.com:waldo2188\/SatisDynamique.git is not a valid URL"', 
                $client->getResponse()->getContent());
    }
    
    public function testShouldAddRepositoryFactory()
    {
        $client = $this->createClient();

        $repository = array (
            "repository" => array (
                    "type" => "package",
                    "package" => array (
                            "name" => "waldo2188/SatisDynamique",
                            "version" => "~0.1",
                            "source" => array (
                                    "url" => "git@github.com:waldo2188/SatisDynamique.git",
                                    "type" => "git",
                                    "reference" => "master"
                                ),
                            "dist" => array (
                                "url" => "git@github.com:waldo2188/SatisDynamique.git",
                                "type" => "git"
                                )
                    )
                )
            );
        
        $client->request('POST', '/repository', $repository);
        
        $client->request('GET', '/repositories');
        $content = json_decode($client->getResponse()->getContent(), true);
        
        
        $this->assertEquals($repository['repository'], $content['repositories'][5]);
    }
    
    public function testShouldUpdateRepository()
    {
        $client = $this->createClient();

        $repository = array (
            "repository" => array (
                    "type" => " package ",
                    "package" => array (
                            "name" => " jquery/jquery",
                            "version" => "5.5.5   ",
                            "source" => array (
                                    "url" => " https://github.com/jquery/jqueryxxx.git",
                                    "type" => " git",
                                    "reference" => "master "
                                ),
                            "dist" => array (
                                "url" => " ",
                                "type" => ""
                                )
                    )
                )
            );
        
        $repositoryExpected = array (
            "repository" => array (
                    "type" => "package",
                    "package" => array (
                            "name" => "jquery/jquery",
                            "version" => "5.5.5",
                            "source" => array (
                                    "url" => "https://github.com/jquery/jqueryxxx.git",
                                    "type" => "git",
                                    "reference" => "master"
                                ),
                            "dist" => array (
                                "url" => "",
                                "type" => ""
                                )
                    )
                )
            );
        
        $client->request('POST', '/repository', $repository);

        $client->request('GET', '/repositories');
        $content = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertEquals($repositoryExpected['repository'], $content['repositories'][2]);
    }
    
    public function testShouldFailAtPostRepositoryPackage()
    {
        $client = $this->createClient();
        
        $client->request('POST', '/repository', array("repository" => array ('type' => "package", "url" => "https://github.com/wtfred/AliDatatableBundle.git")));
        
        $errorExpected = '"JSON does not validate. Violations:\n[package] is missing and it is required\n[] The property url is not defined and the definition does not allow additional properties\n"';
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertEquals($errorExpected, $client->getResponse()->getContent());
    }
    
    public function testShouldDeleteRepositorySimple()
    {
        $client = $this->createClient();
        
        $repositoryExpected = array (
            "repository" => array (
                    "type" => "git",
                    "url" => "https://github.com/wtfred/AliDatatableBundle.git"
                )
            );
        
        $client->request('DELETE', '/repository?repository=' . json_encode($repositoryExpected));
        
        $client->request('GET', '/repositories');
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse(isset($content[1]["type"]));
    }
    
    public function testShouldDeleteRepositoryPackage()
    {
        $client = $this->createClient();
        
        $repositoryExpected = array (
            "repository" => array (
                    "type" => "package",
                    "package" => array (
                            "name" => "jquery/jquery",
                            "version" => "2.0.3",
                            "source" => array (
                                    "url" => "https://github.com/jquery/jquery.git",
                                    "type" => "git",
                                    "reference" => "master"
                                )
                    )
                )
            );
        
        $client->request('DELETE', '/repository?repository=' . json_encode($repositoryExpected));
        
        $client->request('GET', '/repositories');
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse(isset($content[2]["type"]));
    }
    
    public function testShouldNotDeleteRepositoryPackage()
    {
        $client = $this->createClient();
        
        $repositoryExpected = array (
            "repository" => array (
                    "type" => "package",
                    "package" => array (
                            "name" => "jquery/jquery",
                            "version" => "2.0.3",
                            "source" => array (
                                    "url" => "https://github.qsdqsdcom/jquery/jquery.git",
                                    "type" => "git",
                                    "reference" => "master"
                                )
                    )
                )
            );
        
        $client->request('DELETE', '/repository?repository=' . json_encode($repositoryExpected));
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
    
    public function testShouldNotDeleteRepositorySimple()
    {
        $client = $this->createClient();
        
        $repositoryExpected = array (
            "repository" => array (
                    "type" => "git",
                    "url" => "https://github.com/wtfrqsded/AliDatatableBundle.git"
                )
            );
        
        $client->request('DELETE', '/repository?repository=' . json_encode($repositoryExpected));
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
