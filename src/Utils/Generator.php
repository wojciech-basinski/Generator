<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Generator
{
    private $generatingsString = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    /**
     * @var Session
     */
    private $session;

    /**
     * @var int number of errors
     */
    public $errors = 0;

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var array
     */
    private $code;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Generate unique codes
     *
     * @param int $numberOfCodes
     * @param int $codeLength
     * @param string $path path where file will be saved (in cli)
     */
    public function generate(int $numberOfCodes, int $codeLength, string $path = ''): void
    {
        if (!$this->isPossibleToGenerateCodes($numberOfCodes, $codeLength)) {
            $this->errors++;
            $this->session->getFlashBag()->add('error', "Too few combinations to generate {$numberOfCodes} codes with length {$codeLength}");
            return;
        }
        $this->code = $this->generateCodes($numberOfCodes, $codeLength);
        if (!$path) {
            $this->saveCodes();
        } else {
            $this->saveCodesToFile($path);
        }
    }

    /**
     * @param null|string|int $value
     */
    public function checkValue($value):bool
    {
        $status = (is_numeric($value) && $value < PHP_INT_MAX && $value > 0);
        if (!$status) {
            $this->errors++;
        }
        return $status;
    }

    /**
     * @return string
     */
    public function getDownloadLink(): string
    {
        return $this->fileName ?? '';
    }

    /**
     * @param null|string|int $string
     */
    public function checkFileString($string): bool
    {
        return (is_string($string) && $string != '');
    }

    /**
     * @param int $numberOfCodes
     * @param int $codeLength
     *
     * Check if there is at least combination as wanted number of codes
     * @return bool
     */
    private function isPossibleToGenerateCodes(int $numberOfCodes, int $codeLength): bool
    {
        return $numberOfCodes <= (strlen($this->generatingsString) ** $codeLength);
    }

    /**
     * Generating codes
     *
     * @param int $numberOfCodes
     * @param int $codeLength
     *
     * @return array
     */
    private function generateCodes(int $numberOfCodes, int $codeLength): array
    {
        $this->generatingsString = str_shuffle($this->generatingsString);
        $table = [];
        for ($i = 0 ; $i < $numberOfCodes ; $i++) {
            $code = '';
            for ($j = 0 ; $j < $codeLength ; $j++) {
                $code[$j] = $this->generateOneChar();
            }
            //adding code as array key and checks if exists with isset() is faster than adding code to array and checking with in_array()
            if (isset($table[$code])) {
                --$i;
                continue;
            } else {
                $table[$code] = true;
            }
        }
        return array_keys($table);
    }

    /**
     * Generating one char of code
     *
     * @return string
     */
    private function generateOneChar():string
    {
        $random = mt_rand(0, strlen($this->generatingsString) - 1);

        return $this->generatingsString[$random];
    }

    /**
     * Save codes to file
     */
    private function saveCodes()
    {
        $fileName = __DIR__ . '/../../public/files/codes'.time().rand().'.txt';
        $file = fopen($fileName, 'w');
        foreach($this->code as $value) {
            fwrite($file, $value."\r\n");
        }
        fclose($file);
        $pathParts = pathinfo($fileName);
        $this->fileName = '/files/'.$pathParts['basename'];
    }

    private function saveCodesToFile(string $path)
    {
        $file = fopen($path, 'w');
        foreach($this->code as $value) {
            fwrite($file, $value."\r\n");
        }
        fclose($file);
    }
}