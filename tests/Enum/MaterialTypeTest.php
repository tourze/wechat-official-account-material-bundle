<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Enum;

use ChrisUllyott\FileSize;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;

/**
 * @internal
 */
#[CoversClass(MaterialType::class)]
final class MaterialTypeTest extends AbstractEnumTestCase
{
    public function testCases(): void
    {
        $cases = MaterialType::cases();
        $this->assertCount(4, $cases);

        $values = array_map(fn ($case) => $case->value, $cases);
        $this->assertContains('image', $values);
        $this->assertContains('voice', $values);
        $this->assertContains('video', $values);
        $this->assertContains('thumb', $values);
    }

    #[TestWith([MaterialType::IMAGE, 'image', '图片'])]
    #[TestWith([MaterialType::VOICE, 'voice', '语音'])]
    #[TestWith([MaterialType::VIDEO, 'video', '视频'])]
    #[TestWith([MaterialType::THUMB, 'thumb', '缩略图'])]
    public function testValueAndLabel(MaterialType $case, string $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $case->value);
        $this->assertSame($expectedLabel, $case->getLabel());
    }

    public function testGetAllowExtensions(): void
    {
        $this->assertSame(['bmp', 'png', 'jpeg', 'jpg', 'gif'], MaterialType::IMAGE->getAllowExtensions());
        $this->assertSame(['mp3', 'wma', 'wav', 'amr'], MaterialType::VOICE->getAllowExtensions());
        $this->assertSame(['mp4'], MaterialType::VIDEO->getAllowExtensions());
        $this->assertSame(['jpg'], MaterialType::THUMB->getAllowExtensions());
    }

    public function testGetMaxFileSize(): void
    {
        $this->assertInstanceOf(FileSize::class, MaterialType::IMAGE->getMaxFileSize());
        $this->assertInstanceOf(FileSize::class, MaterialType::VOICE->getMaxFileSize());
        $this->assertInstanceOf(FileSize::class, MaterialType::VIDEO->getMaxFileSize());
        $this->assertInstanceOf(FileSize::class, MaterialType::THUMB->getMaxFileSize());

        $this->assertMatchesRegularExpression('/10\s*M[B]?/', (string) MaterialType::IMAGE->getMaxFileSize());
        $this->assertMatchesRegularExpression('/2\s*M[B]?/', (string) MaterialType::VOICE->getMaxFileSize());
        $this->assertMatchesRegularExpression('/10\s*M[B]?/', (string) MaterialType::VIDEO->getMaxFileSize());
        $this->assertMatchesRegularExpression('/64\s*K[B]?/', (string) MaterialType::THUMB->getMaxFileSize());
    }

    public function testToArray(): void
    {
        $array = MaterialType::IMAGE->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        $this->assertSame('image', $array['value']);
        $this->assertSame('图片', $array['label']);
    }

    public function testFromValidValue(): void
    {
        $this->assertSame(MaterialType::IMAGE, MaterialType::from('image'));
        $this->assertSame(MaterialType::VOICE, MaterialType::from('voice'));
        $this->assertSame(MaterialType::VIDEO, MaterialType::from('video'));
        $this->assertSame(MaterialType::THUMB, MaterialType::from('thumb'));
    }

    public function testFromInvalidValue(): void
    {
        $this->expectException(\ValueError::class);
        MaterialType::from('invalid');
    }

    public function testTryFromValidValue(): void
    {
        $this->assertSame(MaterialType::IMAGE, MaterialType::tryFrom('image'));
        $this->assertSame(MaterialType::VOICE, MaterialType::tryFrom('voice'));
        $this->assertSame(MaterialType::VIDEO, MaterialType::tryFrom('video'));
        $this->assertSame(MaterialType::THUMB, MaterialType::tryFrom('thumb'));
    }

    public function testTryFromInvalidValue(): void
    {
        $this->assertNull(MaterialType::tryFrom('invalid'));
        $this->assertNull(MaterialType::tryFrom(''));
        $this->assertNull(MaterialType::tryFrom('unknown'));
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (MaterialType $case) => $case->value, MaterialType::cases());
        $uniqueValues = array_unique($values);
        $this->assertCount(count($values), $uniqueValues, 'All values should be unique');
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (MaterialType $case) => $case->getLabel(), MaterialType::cases());
        $uniqueLabels = array_unique($labels);
        $this->assertCount(count($labels), $uniqueLabels, 'All labels should be unique');
    }
}
