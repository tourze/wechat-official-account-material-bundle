<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountMaterialBundle\Command\SyncMaterialCountCommand;
use WechatOfficialAccountMaterialBundle\Entity\MaterialCount;
use WechatOfficialAccountMaterialBundle\Repository\MaterialCountRepository;

class SyncMaterialCountCommandTest extends TestCase
{
    private AccountRepository $accountRepository;
    private OfficialAccountClient $client;
    private MaterialCountRepository $countRepository;
    private EntityManagerInterface $entityManager;
    private SyncMaterialCountCommand $command;
    
    protected function setUp(): void
    {
        // 模拟依赖
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->client = $this->createMock(OfficialAccountClient::class);
        $this->countRepository = $this->createMock(MaterialCountRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        // 创建命令类
        $this->command = new SyncMaterialCountCommand(
            $this->accountRepository,
            $this->client,
            $this->countRepository,
            $this->entityManager
        );
    }
    
    public function testExecuteWithValidAccounts(): void
    {
        // 模拟当前日期
        CarbonImmutable::setTestNow(CarbonImmutable::create(2023, 6, 15));
        
        // 准备测试数据
        $account = $this->createMock(Account::class);
        
        // 设置模拟行为
        $this->accountRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$account]);
            
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn([
                'voice_count' => 5,
                'video_count' => 10,
                'image_count' => 15,
                'news_count' => 20
            ]);
            
        $this->countRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($arg) {
                return $arg instanceof MaterialCount && 
                    $arg->getVoiceCount() === 5 &&
                    $arg->getVideoCount() === 10 &&
                    $arg->getImageCount() === 15 &&
                    $arg->getNewsCount() === 20;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
            
        // The actual command execution
        $application = new Application();
        $application->add($this->command);
        
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        
        $result = $this->command->run($input, $output);
        
        // 验证结果
        $this->assertEquals(Command::SUCCESS, $result);
        
        // 重置测试时间
        CarbonImmutable::setTestNow();
    }
    
    public function testExecuteWithInvalidResponseFormat(): void
    {
        // 准备测试数据
        $account = $this->createMock(Account::class);
        
        // 设置模拟行为
        $this->accountRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$account]);
            
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn([
                'error' => 'Invalid response'
            ]);
            
        // 不应该调用persist和flush
        $this->entityManager->expects($this->never())
            ->method('persist');
        
        $this->entityManager->expects($this->never())
            ->method('flush');
            
        // 执行命令
        $application = new Application();
        $application->add($this->command);
        
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        
        $result = $this->command->run($input, $output);
        
        // 验证结果
        $this->assertEquals(Command::SUCCESS, $result);
    }
} 