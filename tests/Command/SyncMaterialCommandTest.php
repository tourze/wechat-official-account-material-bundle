<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Command;

use HttpClientBundle\Exception\HttpClientException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatOfficialAccountMaterialBundle\Command\SyncMaterialCommand;

/**
 * @internal
 */
#[CoversClass(SyncMaterialCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncMaterialCommandTest extends AbstractCommandTestCase
{
    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncMaterialCommand::class);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // 命令测试不需要额外的初始化
    }

    public function testCommandExists(): void
    {
        $command = self::getService(SyncMaterialCommand::class);
        $this->assertInstanceOf(Command::class, $command);
        $this->assertEquals(SyncMaterialCommand::NAME, $command->getName());
    }

    public function testOptionAccountId(): void
    {
        $command = self::getService(SyncMaterialCommand::class);
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('account-id'));
        $option = $definition->getOption('account-id');
        $this->assertTrue($option->isValueOptional());
        $this->assertEquals('公众号ID', $option->getDescription());
    }

    public function testCommandExecution(): void
    {
        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([
            '--account-id' => 'test-account-id',
        ]);

        // 由于没有找到对应的公众号，命令应该返回 FAILURE
        $this->assertEquals(Command::FAILURE, $exitCode);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('公众号 test-account-id 不存在', $output);
    }

    public function testCommandExecutionWithoutAccountId(): void
    {
        $commandTester = $this->getCommandTester();

        // 在测试环境中，没有配置有效的微信公众号凭证
        // 当命令尝试同步所有公众号时会抛出 HttpClientException
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('access_token missing');

        $commandTester->execute([]);
    }
}
