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
        $config = array(
            "satis_package_conf_path" => __DIR__ . "/../../tmp/composer_satis.json",
            "cache_path" => __DIR__ . "/../../tmp/cache",
            "temp_path" => __DIR__ . "/../../temp",
            "satis_bin_path" => "",
            "satis_html_path" => "",
        );
        
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
    
    public function testShouldPostRepository()
    {
        $client = $this->createClient();
        
        $client->request('POST', '/repository', array('type' => "git", "url" => "http://172.0.0.1/laura"));
        
        $client->request('GET', '/repositories');
        $content = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertTrue(isset($content['repositories'][2]["type"]));
        $this->assertEquals("git", $content['repositories'][2]["type"]);
        $this->assertEquals("http://172.0.0.1/laura", $content['repositories'][2]["url"]);
    }
    
    public function testShouldFailAtPostRepository()
    {
        $client = $this->createClient();
        
        $client->request('POST', '/repository', array('type' => "git", "url" => "https://github.com/wtfred/AliDatatableBundle.git"));
        
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertEquals('"This repository already exist https:\/\/github.com\/wtfred\/AliDatatableBundle.git"', $client->getResponse()->getContent());
    }
    
    public function testShouldDeleteRepository()
    {
        $client = $this->createClient();
        
        $client->request('DELETE', '/repository', array('type' => "composer", "url" => "https://packagist.org"));
        
        $client->request('GET', '/repositories');
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse(isset($content[2]["type"]));
    }
    
    public function testShouldNotDeleteRepository()
    {
        $client = $this->createClient();
        
        $client->request('DELETE', '/repository', array('type' => "git", "url" => "https://dumber.org"));
        
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
