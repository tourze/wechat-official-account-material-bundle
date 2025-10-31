<?php

namespace WechatOfficialAccountMaterialBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Exception\InvalidMaterialParameterException;
use WechatOfficialAccountMaterialBundle\Exception\MaterialUploadException;
use WechatOfficialAccountMaterialBundle\Request\AddMaterialRequest;
use WechatOfficialAccountMaterialBundle\Request\DeleteMaterialRequest;

/**
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Adding_Permanent_Assets.html 新增永久素材
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Getting_Permanent_Assets.html 获取永久素材
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Get_materials_list.html 获取素材列表
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Deleting_Permanent_Assets.html 删除永久素材
 */
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Material::class)]
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Material::class)]
class MaterialListener
{
    public function __construct(
        private readonly OfficialAccountClient $client,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 本地保存素材时，自动上传到微信服务器
     */
    public function prePersist(Material $material): void
    {
        if ($material->isSyncing()) {
            return;
        }
        if (null !== $material->getMediaId() || null === $material->getLocalFile()) {
            return;
        }

        $uploadFile = $this->generateUploadFileFromUrl($material->getLocalFile());

        $request = new AddMaterialRequest();
        $request->setAccount($material->getAccount());
        $request->setFile($uploadFile);
        $type = $material->getType();
        if (null === $type) {
            throw new InvalidMaterialParameterException('Material type must be set');
        }
        $request->setType($type);

        $response = $this->client->request($request);

        if (is_array($response) && isset($response['media_id']) && is_string($response['media_id'])) {
            $material->setMediaId($response['media_id']);
        }

        if (is_array($response) && isset($response['url']) && is_string($response['url'])) {
            $material->setUrl($response['url']);
        }

        $this->entityManager->persist($material);
        $this->entityManager->flush();
    }

    /**
     * 读取远程URL的内容，并生成一个上传文件对象
     */
    private function generateUploadFileFromUrl(string $url): UploadedFile
    {
        $content = @file_get_contents($url);
        if (false === $content) {
            throw new MaterialUploadException('Cannot fetch content from URL: ' . $url);
        }

        $file = tempnam(sys_get_temp_dir(), 'upload_file');
        if (false === $file) {
            throw new MaterialUploadException('Cannot create temporary file');
        }

        file_put_contents($file, $content);
        $name = basename($url);

        return $this->generateUploadFileFromPath($file, $name);
    }

    private function generateUploadFileFromPath(string $path, ?string $name = null): UploadedFile
    {
        if (null === $name) {
            $name = basename($path);
        }

        return new UploadedFile($path, $name);
    }

    /**
     * 本地删除素材后，远程也同步删除
     */
    public function preRemove(Material $material): void
    {
        if ($material->isSyncing()) {
            return;
        }
        if (null === $material->getMediaId()) {
            return;
        }

        $request = new DeleteMaterialRequest();
        $request->setAccount($material->getAccount());
        $request->setMediaId($material->getMediaId());
        $this->client->asyncRequest($request);
    }
}
