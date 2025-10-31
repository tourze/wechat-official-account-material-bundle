<?php

declare(strict_types=1);

namespace WechatOfficialAccountMaterialBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatOfficialAccountMaterialBundle\WechatOfficialAccountMaterialBundle;

/**
 * @internal
 */
#[CoversClass(WechatOfficialAccountMaterialBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatOfficialAccountMaterialBundleTest extends AbstractBundleTestCase
{
}
