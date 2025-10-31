<?php

namespace WechatOfficialAccountMaterialBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;
use WechatOfficialAccountMaterialBundle\Repository\MaterialRepository;
use WechatOfficialAccountMaterialBundle\Request\BatchGetMaterialRequest;

#[AsCommand(
    name: self::NAME,
    description: '同步微信公众号永久素材',
)]
class SyncMaterialCommand extends Command
{
    public const NAME = 'wechat-official-account:material:sync';

    public function __construct(
        private readonly OfficialAccountClient $client,
        private readonly EntityManagerInterface $entityManager,
        private readonly AccountRepository $accountRepository,
        private readonly MaterialRepository $materialRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('account-id', null, InputOption::VALUE_OPTIONAL, '公众号ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 获取需要同步的公众号列表
        $accounts = [];
        /** @var mixed $accountId */
        $accountId = $input->getOption('account-id');
        if (null !== $accountId) {
            $account = $this->accountRepository->find($accountId);
            if (null === $account) {
                $accountIdStr = is_scalar($accountId) ? (string) $accountId : '未知';
                $io->error(sprintf('公众号 %s 不存在', $accountIdStr));

                return Command::FAILURE;
            }
            $accounts[] = $account;
        } else {
            $accounts = $this->accountRepository->findAll();
        }

        foreach ($accounts as $account) {
            $io->section(sprintf('正在同步公众号 %s 的永久素材', $account->getName()));
            $this->syncMaterials($account, $io);
        }

        return Command::SUCCESS;
    }

    private function syncMaterials(Account $account, SymfonyStyle $io): void
    {
        // 遍历所有素材类型
        foreach (MaterialType::cases() as $type) {
            $this->syncMaterialsByType($account, $type, $io);
        }

        $this->entityManager->flush();
    }

    private function syncMaterialsByType(Account $account, MaterialType $type, SymfonyStyle $io): void
    {
        $io->text(sprintf('正在同步 %s 类型的素材', $type->value));

        $totalCount = $this->getTotalMaterialCount($account, $type);

        if ($totalCount > 0) {
            $io->progressStart($totalCount);
            $this->fetchAndSyncMaterials($account, $type, $totalCount, $io);
            $io->progressFinish();
        }
    }

    private function getTotalMaterialCount(Account $account, MaterialType $type): int
    {
        $request = new BatchGetMaterialRequest();
        $request->setAccount($account);
        $request->setType($type->value);
        $request->setOffset(0);
        $request->setCount(1);

        $response = $this->client->request($request);

        /** @var array<string, mixed> $response */
        $totalCount = $response['total_count'] ?? 0;
        return is_numeric($totalCount) ? (int) $totalCount : 0;
    }

    private function fetchAndSyncMaterials(Account $account, MaterialType $type, int $totalCount, SymfonyStyle $io): void
    {
        $offset = 0;
        $count = 20; // 每次获取20条记录

        while ($offset < $totalCount) {
            $items = $this->fetchMaterialBatch($account, $type, $offset, $count);

            foreach ($items as $item) {
                if (is_array($item)) {
                    /** @var array<string, mixed> $item */
                    $this->syncMaterial($account, $type, $item);
                }
            }

            $offset += count($items);
            $io->progressAdvance(count($items));
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchMaterialBatch(Account $account, MaterialType $type, int $offset, int $count): array
    {
        $request = new BatchGetMaterialRequest();
        $request->setAccount($account);
        $request->setType($type->value);
        $request->setOffset($offset);
        $request->setCount($count);

        $response = $this->client->request($request);

        /** @var array<string, mixed> $response */
        $items = $response['item'] ?? [];
        return is_array($items) ? $items : [];
    }

    /**
     * @param array<string, mixed> $item
     */
    private function syncMaterial(Account $account, MaterialType $type, array $item): void
    {
        // 查找是否已存在
        $material = $this->materialRepository->findOneBy([
            'account' => $account,
            'mediaId' => $item['media_id'],
        ]);

        if (null === $material) {
            $material = new Material();
            $material->setAccount($account);
            $mediaId = $item['media_id'] ?? null;
            $material->setMediaId(is_string($mediaId) ? $mediaId : null);
            $material->setType($type);
        }

        $name = $item['name'] ?? null;
        $material->setName(is_string($name) ? $name : null);
        $url = $item['url'] ?? null;
        $material->setUrl(is_string($url) ? $url : null);

        if (isset($item['content']) && is_array($item['content'])) {
            $content = $item['content'];
            /** @var array<string, mixed> $content */
            $material->setContent($content);
        }

        $this->entityManager->persist($material);
    }
}
