<?php

namespace App\Tests\Entity;

use App\Entity\Crypto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class cryptoTest extends KernelTestCase
{
    public function getEntity(): Crypto
    {
        return (new Crypto())
            ->setSymbol('BTC')
            ->setQuantite(100)
            ->setPrixAchat(200);
    }

    public function testSettingsymbol()
    {
        $crypto = new Crypto();
        $var = "BTC";

        $crypto->setSymbol($var);

        $this->assertEquals($var, $crypto->getsymbol());
    }

    public function testSettingquantite()
    {
        $crypto = new Crypto();
        $var = 3;

        $crypto->setQuantite($var);

        $this->assertEquals($var, $crypto->getQuantite());
    }
    public function testSettingprix()
    {
        $crypto = new Crypto();
        $var = 3;

        $crypto->setPrixAchat($var);

        $this->assertEquals($var, $crypto->getPrixAchat());
    }
    public function assertHasErrors(crypto $crypto, int $number = 0)
    {
        self::bootKernel();
        $validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();
        $errors = $validator->validate($crypto);
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }
    public function testInvalidCodeEntity()
    {
        $this->assertHasErrors($this->getEntity()->setsymbol('aaasssssaaa'),);
    }
}
