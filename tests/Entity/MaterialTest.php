<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;

/**
 * @internal
 */
#[CoversClass(Material::class)]
final class MaterialTest extends AbstractEntityTestCase
{
    // private Material $material; // AbstractEntityTest 不需要这个属性

    protected function setUp(): void
    {
        // Entity 测试中允许直接实例化，因为需要测试 Entity 的基本功能
        // AbstractEntityTest 会自动测试所有 getter/setter 方法
    }

    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): Material
    {
        return new Material();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * 注意：account 属性被跳过，因为它需要复杂的 Account 对象作为参数
     * 该属性会在单独的测试方法中进行测试
     */
    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'syncing' => ['syncing', true];
        yield 'type' => ['type', MaterialType::IMAGE];
        yield 'mediaId' => ['mediaId', 'test_media_id'];
        yield 'name' => ['name', 'test_name'];
        yield 'url' => ['url', 'https://example.com/test.jpg'];
        yield 'content' => ['content', ['key' => 'value']];
        yield 'localFile' => ['localFile', 'path/to/local/file.jpg'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    /**
     * 测试 account 属性的 getter/setter 方法
     */
    public function testGetSetAccount(): void
    {
        $entity = $this->createEntity();
        $account = $this->createMock(Account::class);

        $entity->setAccount($account);
        $this->assertSame($account, $entity->getAccount());
    }
}
