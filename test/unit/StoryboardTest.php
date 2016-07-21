<?php

namespace test\unit;

use \Rogyar\Storyboard\Storyboard;
use \Symfony\Component\Yaml\Yaml;

class StoryboardTest extends \PHPUnit_Framework_TestCase
{
    /** @var string  */
    protected $dummyStoragePath = 'dummy/dir';
    /** @var string  */
    protected $correctToken = 'sometoken';
    /** @var string  */
    protected $configPath = 'etc/config.yml';
    /** @var Storyboard\PHPUnit_Framework_MockObject_MockObject $storyBoard */
    protected $storyBoardMock;
    /** @var  string */
    protected $originalContent;

    protected function setUp()
    {
        $this->storyBoardMock = $this->getMockBuilder(Storyboard::class)
            ->setConstructorArgs([new Yaml(), $this->configPath, 'sometoken'])
            ->setMethods(null)
            ->getMock();
    }

    public function testConfigurationReader()
    {
        $config = $this->storyBoardMock->getConfig();
        $this->assertNotCount(0, $config);
    }

    public function testWrongConfigurationPath()
    {
        $this->storyBoardMock = $this->getMockBuilder(Storyboard::class)
            ->setConstructorArgs([new Yaml(), 'wrong/config/path', 'sometoken'])
            ->setMethods(null)
            ->getMock();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The configuration path is incorrect');
        $this->storyBoardMock->getConfig();
    }

    public function testTokenValidationWithCorrectToken()
    {
        $this->assertTrue($this->storyBoardMock->validateToken());
    }

    public function testTokenValidationWithIncorrectToken()
    {
        $this->storyBoardMock = $this->getMockBuilder(Storyboard::class)
            ->setConstructorArgs([new Yaml(), $this->configPath, 'somewrongtoken'])
            ->setMethods(null)
            ->getMock();
        $this->assertFalse($this->storyBoardMock->validateToken());
    }

    public function testSetAndGetContentProcess()
    {
        $someinfo = "test";
        $this->storyBoardMock->setContent($someinfo);
        $this->assertTrue($this->storyBoardMock->getContent() == $someinfo);
    }

    public function testGetStorageReturnsCorrectValue()
    {
        $storagePath = $this->storyBoardMock->getStoragePath();
        $configValue = Yaml::parse(file_get_contents($this->configPath))['storagePath'];
        $this->assertSame($storagePath, $configValue);
    }

    public function testFileReadProcess()
    {
        $content = 'someTestContent';
        $this->storyBoardMock->writeContent($content);
        $this->assertSame($content, $this->storyBoardMock->getContent());
    }

    public function testStorageWriteOnWrongFilePermissions()
    {
        $this->storyBoardMock = $this->getMockBuilder(Storyboard::class)
            ->setConstructorArgs([new Yaml(), $this->configPath, $this->correctToken])
            ->setMethods(['getStoragePath', 'validateToken'])
            ->getMock();
        $content = 'somecontent';
        $this->storyBoardMock->expects($this->once())->method('validateToken');
        $this->storyBoardMock
            ->expects($this->any())
            ->method('getStoragePath')
            ->willReturn($this->dummyStoragePath);
        $this->expectException(\Exception::class);
        $this->storyBoardMock->writeContent($content);
    }

    public function testStorageWriteOnCorrectFilePermissions()
    {
        $content = 'somecontent';
        $this->storyBoardMock->writeContent($content);
        $this->assertSame($content, $this->storyBoardMock->getContent());
    }

    public function testWriteImpossibleWithIncorrectToken()
    {
        $this->storyBoardMock = $this->getMockBuilder(Storyboard::class)
            ->setConstructorArgs([new Yaml(), $this->configPath, 'somewrongtoken'])
            ->setMethods(null)
            ->getMock();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The token is invalid');
        $this->storyBoardMock->writeContent('somecontent');
    }

    public function testWrongTemplateThrowsException()
    {
        $this->storyBoardMock = $this->getMockBuilder(Storyboard::class)
            ->setConstructorArgs([new Yaml(), $this->configPath, $this->correctToken])
            ->setMethods(['getConfig'])
            ->getMock();
        $this->storyBoardMock->expects($this->once())
            ->method('getConfig')
            ->willReturn(['templatePath' => 'dummy/path']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The template path is wrong. Please, check your configuration');
        $this->storyBoardMock->renderTemplate();
    }

    public function testRenderTemplateProcess()
    {
        $content = $this->storyBoardMock->getContent();
        $finalContent = $this->storyBoardMock->renderTemplate();
        $this->assertContains($content, $finalContent);
    }
}