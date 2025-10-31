<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatOfficialAccountMaterialBundle\Exception\InvalidMaterialParameterException;

/**
 * @internal
 */
#[CoversClass(InvalidMaterialParameterException::class)]
final class MaterialExceptionTest extends AbstractExceptionTestCase
{
    protected function createException(?string $message = null): \Throwable
    {
        return new InvalidMaterialParameterException($message ?? 'test message');
    }

    public function testExceptionInheritance(): void
    {
        $exception = new InvalidMaterialParameterException('test message');

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('test message', $exception->getMessage());
    }
}
