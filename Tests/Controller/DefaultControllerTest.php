<?php
    /**
     * Created by groovili
     * https://github.com/groovili
     */
    
    namespace Groovili\RestUploaderBundle;
    
    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
    
    /**
     * Class DefaultControllerTest
     *
     * @package Groovili\RestUploaderBundle
     */
    class DefaultControllerTest extends WebTestCase
    {
        public function testIndex()
        {
            $client = static::createClient();
            $crawler = $client->request('GET', '/');
            
            $this->assertContains(
              'Hello World!',
              $client->getResponse()->getContent()
            );
        }
    }