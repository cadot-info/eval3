<?php

namespace App\DataFixtures;

use App\Entity\Crypto;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $s = ['BTC', 'ETC', 'XRP', 'ETH', 'XLM', 'NEO', 'BCH', 'EOS'];
        $p = [45970, 88, 1, 3290, 0.5, 90, 1200, 10];
        foreach ($s as $k => $v) {
            $crypto = new Crypto();
            $crypto->setSymbol($v)->setPrixAchat($p[$k])->setQuantite(3);
            $manager->persist($crypto);
        }

        $manager->flush();
    }
}
