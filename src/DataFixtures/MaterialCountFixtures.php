<?php

namespace WechatOfficialAccountMaterialBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatOfficialAccountBundle\DataFixtures\AccountFixtures;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Entity\MaterialCount;

final class MaterialCountFixtures extends Fixture implements DependentFixtureInterface
{
    public const MATERIAL_COUNT_1_REFERENCE = 'material-count-1';
    public const MATERIAL_COUNT_2_REFERENCE = 'material-count-2';
    public const MATERIAL_COUNT_3_REFERENCE = 'material-count-3';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $materialCount1 = new MaterialCount();
        $materialCount1->setAccount($account);
        $materialCount1->setDate(new \DateTimeImmutable('2024-01-01'));
        $materialCount1->setVoiceCount(50);
        $materialCount1->setVideoCount(30);
        $materialCount1->setImageCount(100);
        $materialCount1->setNewsCount(25);
        $manager->persist($materialCount1);

        $materialCount2 = new MaterialCount();
        $materialCount2->setAccount($account);
        $materialCount2->setDate(new \DateTimeImmutable('2024-01-02'));
        $materialCount2->setVoiceCount(52);
        $materialCount2->setVideoCount(31);
        $materialCount2->setImageCount(105);
        $materialCount2->setNewsCount(26);
        $manager->persist($materialCount2);

        $materialCount3 = new MaterialCount();
        $materialCount3->setAccount($account);
        $materialCount3->setDate(new \DateTimeImmutable('2024-01-03'));
        $materialCount3->setVoiceCount(48);
        $materialCount3->setVideoCount(29);
        $materialCount3->setImageCount(98);
        $materialCount3->setNewsCount(24);
        $manager->persist($materialCount3);

        $manager->flush();

        $this->addReference(self::MATERIAL_COUNT_1_REFERENCE, $materialCount1);
        $this->addReference(self::MATERIAL_COUNT_2_REFERENCE, $materialCount2);
        $this->addReference(self::MATERIAL_COUNT_3_REFERENCE, $materialCount3);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
