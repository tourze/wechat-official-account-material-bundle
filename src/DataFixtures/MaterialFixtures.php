<?php

namespace WechatOfficialAccountMaterialBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatOfficialAccountBundle\DataFixtures\AccountFixtures;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;

final class MaterialFixtures extends Fixture implements DependentFixtureInterface
{
    public const MATERIAL_IMAGE_REFERENCE = 'material-image';
    public const MATERIAL_VIDEO_REFERENCE = 'material-video';
    public const MATERIAL_VOICE_REFERENCE = 'material-voice';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $material1 = new Material();
        $material1->setAccount($account);
        $material1->setType(MaterialType::IMAGE);
        $material1->setMediaId('test_media_id_001');
        $material1->setName('测试图片素材');
        $material1->setUrl('https://mdn.alipayobjects.com/huamei_1dthxr/afts/img/A*VCFPRKBrKGcAAAAAAAAAAAAADg_PAQ/original');
        $material1->setLocalFile('/tmp/test_image1.jpg');
        $manager->persist($material1);

        $material2 = new Material();
        $material2->setAccount($account);
        $material2->setType(MaterialType::VOICE);
        $material2->setMediaId('test_media_id_002');
        $material2->setName('测试语音素材');
        $material2->setLocalFile('/tmp/test_voice.mp3');
        $manager->persist($material2);

        $material3 = new Material();
        $material3->setAccount($account);
        $material3->setType(MaterialType::VIDEO);
        $material3->setMediaId('test_media_id_003');
        $material3->setName('测试视频素材');
        $material3->setLocalFile('/tmp/test_video.mp4');
        $manager->persist($material3);

        $manager->flush();

        $this->addReference(self::MATERIAL_IMAGE_REFERENCE, $material1);
        $this->addReference(self::MATERIAL_VOICE_REFERENCE, $material2);
        $this->addReference(self::MATERIAL_VIDEO_REFERENCE, $material3);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
