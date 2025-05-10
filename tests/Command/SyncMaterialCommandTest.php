<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;

class SyncMaterialCommandTest extends TestCase
{
    private AccountRepository $accountRepository;

    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(AccountRepository::class);
    }

    public function testFindAccount(): void
    {
        // 准备测试数据
        $account = $this->createMock(Account::class);
        $account->method('getName')->willReturn('Test Account');
        
        // 设置模拟行为
        $this->accountRepository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn($account);
            
        // 验证结果
        $result = $this->accountRepository->find('123');
        $this->assertSame($account, $result);
        $this->assertEquals('Test Account', $result->getName());
    }
    
    public function testFindMissingAccount(): void
    {
        // 设置模拟行为
        $this->accountRepository->expects($this->once())
            ->method('find')
            ->with('999')
            ->willReturn(null);
            
        // 验证结果
        $result = $this->accountRepository->find('999');
        $this->assertNull($result);
    }
} 