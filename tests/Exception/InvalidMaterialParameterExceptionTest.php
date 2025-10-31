<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatOfficialAccountMaterialBundle\Exception\InvalidMaterialParameterException;
use WechatOfficialAccountMaterialBundle\Exception\MaterialException;

/**
 * @internal
 */
#[CoversClass(InvalidMaterialParameterException::class)]
final class InvalidMaterialParameterExceptionTest extends AbstractExceptionTestCase
{
    protected function createException(?string $message = null): \Throwable
    {
        return new InvalidMaterialParameterException($message ?? 'test message');
    }

    public function testExceptionInheritance(): void
    {
        $exception = new InvalidMaterialParameterException('test message');

        $this->assertInstanceOf(MaterialException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('test message', $exception->getMessage());
    }
}
