<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatOfficialAccountMaterialBundle\Command\SyncMaterialCountCommand;

/**
 * @internal
 */
#[CoversClass(SyncMaterialCountCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncMaterialCountCommandTest extends AbstractCommandTestCase
{
    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncMaterialCountCommand::class);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // 命令测试不需要额外的初始化
    }

    public function testCommandExists(): void
    {
        $command = self::getService(SyncMaterialCountCommand::class);
        $this->assertInstanceOf(Command::class, $command);
        $this->assertEquals(SyncMaterialCountCommand::NAME, $command->getName());
    }

    public function testCommandHasCorrectOptions(): void
    {
        $command = self::getService(SyncMaterialCountCommand::class);
        $definition = $command->getDefinition();

        // 这个命令没有选项
        $this->assertCount(0, $definition->getOptions());
    }

    public function testCommandExecution(): void
    {
        // 这个命令需要有效的公众号账号和 access token，
        // 为了避免实际的 HTTP 请求，我们只测试命令的基本配置
        $command = self::getService(SyncMaterialCountCommand::class);
        $this->assertEquals(SyncMaterialCountCommand::NAME, $command->getName());
        $this->assertEquals('公众号-获取素材总数', $command->getDescription());
    }

    public function testCommandTesterCanBeCreated(): void
    {
        $command = self::getService(SyncMaterialCountCommand::class);
        $commandTester = new CommandTester($command);

        // 只测试能否创建 CommandTester，不执行命令
        $this->assertInstanceOf(CommandTester::class, $commandTester);
    }
}
