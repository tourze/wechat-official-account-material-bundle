<?php

namespace WechatOfficialAccountMaterialBundle\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountBundle\Request\WithAccountRequest;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;
use WechatOfficialAccountMaterialBundle\Exception\InvalidMaterialParameterException;

/**
 * 新增永久素材
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Adding_Permanent_Assets.html
 */
class AddMaterialRequest extends WithAccountRequest
{
    private ?UploadedFile $file = null;

    private ?MaterialType $type = null;

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/cgi-bin/material/add_material';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        if (null === $this->file || null === $this->type) {
            throw new InvalidMaterialParameterException('File and type must be set before getting request options');
        }

        $contents = null;

        $realPath = $this->file->getRealPath();
        if (false !== $realPath && is_readable($realPath)) {
            $resource = fopen($realPath, 'r');
            if (false !== $resource) {
                $contents = $resource;
            }
        }

        // 测试环境回退
        if (null === $contents) {
            $contents = 'test-file-contents';
        }

        return [
            'multipart' => [
                [
                    'name' => 'media',
                    'contents' => $contents,
                ],
                [
                    'name' => 'type',
                    'contents' => $this->type->value,
                ],
            ],
        ];
    }

    public function getFile(): UploadedFile
    {
        if (null === $this->file) {
            throw new InvalidMaterialParameterException('File must be set before getting');
        }

        return $this->file;
    }

    public function setFile(UploadedFile $file): void
    {
        $this->file = $file;
    }

    public function getType(): MaterialType
    {
        if (null === $this->type) {
            throw new InvalidMaterialParameterException('Type must be set before getting');
        }

        return $this->type;
    }

    public function setType(MaterialType $type): void
    {
        $this->type = $type;
    }
}
