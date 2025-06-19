<?php

namespace WechatOfficialAccountMaterialBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountMaterialBundle\Entity\MaterialCount;
use WechatOfficialAccountMaterialBundle\Repository\MaterialCountRepository;
use WechatOfficialAccountMaterialBundle\Request\GetMaterialCountRequest;

/**
 * 获取素材总数
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Get_the_total_of_all_materials.html
 */
#[AsCronTask('0 */2 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取素材总数')]
class SyncMaterialCountCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-material-count';
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly MaterialCountRepository $countRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => 1]) as $account) {
            $now = CarbonImmutable::today();
            $request = new GetMaterialCountRequest();
            $request->setAccount($account);
            $response = $this->client->request($request);
            if (!isset($response['voice_count'])) {
                continue;
            }

            $count = $this->countRepository->findOneBy([
                'account' => $account,
                'date' => $now,
            ]);
            if ($count === null) {
                $count = new MaterialCount();
                $count->setAccount($account);
                $count->setDate($now);
            }
            $count->setVoiceCount($response['voice_count']);
            $count->setVideoCount($response['video_count']);
            $count->setImageCount($response['image_count']);
            $count->setNewsCount($response['news_count']);
            $this->entityManager->persist($count);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
