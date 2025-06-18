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
    name: 'wechat-official-account:material:sync',
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
        $accountId = $input->getOption('account-id');
        if ($accountId !== null) {
            $account = $this->accountRepository->find($accountId);
            if ($account === null) {
                $io->error(sprintf('公众号 %s 不存在', $accountId));

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
            $io->text(sprintf('正在同步 %s 类型的素材', $type->value));

            $offset = 0;
            $count = 20; // 每次获取20条记录

            do {
                $request = new BatchGetMaterialRequest();
                $request->setAccount($account);
                $request->setType($type->value);
                $request->setOffset($offset);
                $request->setCount($count);

                $response = $this->client->request($request);
                $totalCount = $response['total_count'];
                $items = $response['item'];

                foreach ($items as $item) {
                    $this->syncMaterial($account, $type, $item);
                }

                $offset += count($items);
                $io->progressAdvance(count($items));
            } while ($offset < $totalCount);

            $io->progressFinish();
        }

        $this->entityManager->flush();
    }

    private function syncMaterial(Account $account, MaterialType $type, array $item): void
    {
        // 查找是否已存在
        $material = $this->materialRepository->findOneBy([
            'account' => $account,
            'mediaId' => $item['media_id'],
        ]);

        if ($material === null) {
            $material = new Material();
            $material->setAccount($account);
            $material->setMediaId($item['media_id']);
            $material->setType($type);
        }

        $material->setName($item['name'] ?? null);
        $material->setUrl($item['url'] ?? null);

        if (isset($item['content'])) {
            $material->setContent($item['content']);
        }

        $this->entityManager->persist($material);
    }
}
