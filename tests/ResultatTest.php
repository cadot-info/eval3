<?php

namespace App\Tests\Entity;

use App\Entity\Resultat;
use DateTime;
use PHPUnit\Framework\TestCase;

class resultatTest extends TestCase
{
    public function testSettingdate()
    {
        $resultat = new Resultat();
        $var = new DateTime();

        $resultat->setDate($var);

        $this->assertEquals($var, $resultat->getDate());
    }
    public function testSettingvalue()
    {
        $resultat = new Resultat();
        $var = 12;

        $resultat->setValeur($var);

        $this->assertEquals($var, $resultat->getValeur());
    }
}
