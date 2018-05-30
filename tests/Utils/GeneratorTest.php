<?php

namespace App\Tests\Utils;

use App\Utils\Generator;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class GeneratorTest extends TestCase
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Generator
     */
    private $generator;

    public function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->generator = new Generator($this->session);
    }

    public function testTooFewCombination()
    {
        $this->generator->generate(10000000, 2);

        $errors = $this->session->getFlashBag()->get('error');

        $this->assertContains('Too few combinations to generate', $errors[0]);
    }

    public function testGetEmptyFileName()
    {
        $this->assertEmpty($this->generator->getDownloadLink());
    }

    public function testValidValueOfNumberOfCodes()
    {
        $numberOfCodes = 100;
        $this->assertTrue($this->generator->checkValue($numberOfCodes));
        $this->assertEquals(0, $this->generator->errors);
    }

    public function testInvalidValueOfNumberOfCodes()
    {
        $numberOfCodes = 'notInt';
        $this->assertFalse($this->generator->checkValue($numberOfCodes));
        $this->assertEquals(1, $this->generator->errors);
    }

    public function checkFileString()
    {
        $validFileString = '/tmp/codes.txt';
        $invalidFileString = 6666;

        $this->assertTrue($this->generator->checkFileString($validFileString));
        $this->assertFalse($this->generator->checkFileString($invalidFileString));
    }

    public function testGeneratingCodes()
    {
        mt_srand(1000000000);

        $this->generator->generate(500, 8);
        $link = $this->generator->getDownloadLink();
        $fileString = file_get_contents(__DIR__ . '/../../public/' . $link);
        $this->assertStringStartsWith('AKBeGqdv', $fileString);
    }

    public function testSaveCodesToPath()
    {
        $path = __DIR__ . '/../../var/codes.txt';
        $this->generator->generate(50, 8, $path);

        $this->assertFileExists($path, "File {$path} does not exists but it should");
        $fileString = file_get_contents($path);
        $this->assertStringStartsWith('XuhXd6ue', $fileString);
    }
}