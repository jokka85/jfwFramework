<?php

use PHPUnit\Framework\TestCase;

require("../config/required.php");

// from vendor dir
// "bin/phpunit" --bootstrap autoload.php --testdox ../tests
// "bin/phpunit" --bootstrap autoload.php ../tests

final class ControllerTest extends TestCase {
    
    private $getController;
    
    protected function setUp(){
        parent::setUp();
        $this->getController = new \Controller\getController();
        
        $_SESSION['start'] = microtime(true);
    }
    
    protected function tearDown(){
        $this->getController = null;
        parent::tearDown();
    }
    
    public function testBadGetController(){
        // SHOULD RETURN BAD
        $this->getController->controller('Fake');
        $this->assertContains("Error", get_class($this->getController->get_controller()));
    }
    
    public function testGoodGetController(){
        // SHOULD RETURN GOOD
        $this->getController->controller('Default');
        $this->assertContains("Default", get_class($this->getController->get_controller()));
    }
    
    
    
}