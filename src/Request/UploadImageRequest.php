<?php

namespace WechatOfficialAccountMaterialBundle\Request;

use WechatOfficialAccountBundle\Request\WithAccountRequest;
use WechatOfficialAccountMaterialBundle\Exception\InvalidMaterialParameterException;
use WechatOfficialAccountMaterialBundle\Exception\MaterialUploadException;

/**
 * 本接口所上传的图片不占用公众号的素材库中图片数量的100000个的限制。图片仅支持jpg/png格式，大小必须在1MB以下。
 * 这个接口返回的是图片URL，开发者可以在腾讯系域名内使用该URL。
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Adding_Permanent_Assets.html
 */
class UploadImageRequest extends WithAccountRequest
{
    private ?string $path = null;

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/cgi-bin/media/uploadimg';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        if (null === $this->path) {
            throw new InvalidMaterialParameterException('Path must be set before getting request options');
        }

        $resource = fopen($this->path, 'r');
        if (false === $resource) {
            throw new MaterialUploadException('Cannot open file: ' . $this->path);
        }

        return [
            'headers' => [
                'Content-Type' => 'image/png',
            ],
            'multipart' => [
                [
                    'name' => 'media',
                    'contents' => $resource,
                ],
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getPath(): string
    {
        if (null === $this->path) {
            throw new InvalidMaterialParameterException('Path must be set before getting');
        }

        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
