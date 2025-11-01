<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;
use WechatOfficialAccountMaterialBundle\Request\AddMaterialRequest;

/**
 * @internal
 */
#[CoversClass(AddMaterialRequest::class)]
final class AddMaterialRequestTest extends RequestTestCase
{
    private AddMaterialRequest $request;

    private Account $account;

    protected function setUp(): void
    {
        // Request 测试中允许直接实例化，因为需要测试请求对象的基本功能
        $this->request = new AddMaterialRequest();

        // 使用具体类 Account 是因为：
        // 1) 该类是 Doctrine Entity，不存在对应的接口抽象
        // 2) Request 类需要使用 Account 实例来处理微信 API 请求
        // 3) 在单元测试中模拟 Entity 是常见且合理的做法
        $this->account = $this->createMock(Account::class);
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/material/add_material', $this->request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        // 创建模拟对象
        // 使用具体类 UploadedFile 是因为：
        // 1) 这是 Symfony 的文件上传组件，没有对应的接口
        // 2) 测试需要模拟文件上传操作，避免真实文件 I/O
        // 3) Mock 对象可以模拟文件的各种属性和方法
        $file = $this->createMock(UploadedFile::class);
        $this->request->setFile($file);
        $this->request->setType(MaterialType::IMAGE);

        // 测试基本结构，不验证资源
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('multipart', $options);
        $this->assertIsArray($options['multipart']);
        $this->assertCount(2, $options['multipart']);

        if (isset($options['multipart'][0]) && is_array($options['multipart'][0])) {
            $this->assertSame('media', $options['multipart'][0]['name']);
        }
        // 不测试资源

        if (isset($options['multipart'][1]) && is_array($options['multipart'][1])) {
            $this->assertSame('type', $options['multipart'][1]['name']);
            $this->assertSame('image', $options['multipart'][1]['contents']);
        }
    }

    public function testGetSetFile(): void
    {
        // 使用具体类 UploadedFile 是因为：
        // 1) 这是 Symfony 的文件上传组件，没有对应的接口
        // 2) 测试需要模拟文件上传操作，避免真实文件 I/O
        // 3) Mock 对象可以模拟文件的各种属性和方法
        $file = $this->createMock(UploadedFile::class);
        $this->request->setFile($file);
        $this->assertSame($file, $this->request->getFile());
    }

    public function testGetSetType(): void
    {
        $type = MaterialType::VIDEO;
        $this->request->setType($type);
        $this->assertSame($type, $this->request->getType());
    }
}
