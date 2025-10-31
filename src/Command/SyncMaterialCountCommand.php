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
#[AsCronTask(expression: '0 */2 * * *')]
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

            /** @var array<string, mixed> $response */
            if (!isset($response['voice_count'])) {
                continue;
            }

            $count = $this->countRepository->findOneBy([
                'account' => $account,
                'date' => $now,
            ]);
            if (null === $count) {
                $count = new MaterialCount();
                $count->setAccount($account);
                $count->setDate($now);
            }
            $voiceCount = $response['voice_count'] ?? 0;
            $count->setVoiceCount(is_numeric($voiceCount) ? (int) $voiceCount : 0);
            $videoCount = $response['video_count'] ?? 0;
            $count->setVideoCount(is_numeric($videoCount) ? (int) $videoCount : 0);
            $imageCount = $response['image_count'] ?? 0;
            $count->setImageCount(is_numeric($imageCount) ? (int) $imageCount : 0);
            $newsCount = $response['news_count'] ?? 0;
            $count->setNewsCount(is_numeric($newsCount) ? (int) $newsCount : 0);
            $this->entityManager->persist($count);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
