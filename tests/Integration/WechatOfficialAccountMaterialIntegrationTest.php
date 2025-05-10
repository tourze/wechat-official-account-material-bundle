<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * 微信公众号素材管理集成测试基类
 */
class WechatOfficialAccountMaterialIntegrationTest extends TestCase
{
    /**
     * 验证基础测试环境是否正常
     */
    public function testEnvironment(): void
    {
        // 仅验证测试环境是否可运行
        $this->assertTrue(true);
    }
} 