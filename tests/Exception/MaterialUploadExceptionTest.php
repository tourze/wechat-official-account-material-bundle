<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatOfficialAccountMaterialBundle\Exception\MaterialException;
use WechatOfficialAccountMaterialBundle\Exception\MaterialUploadException;

/**
 * @internal
 */
#[CoversClass(MaterialUploadException::class)]
final class MaterialUploadExceptionTest extends AbstractExceptionTestCase
{
    protected function createException(?string $message = null): \Throwable
    {
        return new MaterialUploadException($message ?? 'test message');
    }

    public function testExceptionInheritance(): void
    {
        $exception = new MaterialUploadException('test message');

        $this->assertInstanceOf(MaterialException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('test message', $exception->getMessage());
    }
}
