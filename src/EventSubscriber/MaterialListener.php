<?php

namespace WechatOfficialAccountMaterialBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Request\AddMaterialRequest;
use WechatOfficialAccountMaterialBundle\Request\DeleteMaterialRequest;

/**
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Adding_Permanent_Assets.html 新增永久素材
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Getting_Permanent_Assets.html 获取永久素材
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Get_materials_list.html 获取素材列表
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Deleting_Permanent_Assets.html 删除永久素材
 */
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Material::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Material::class)]
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
        if ($material->getMediaId() || !$material->getLocalFile()) {
            return;
        }

        $uploadFile = $this->generateUploadFileFromUrl($material->getLocalFile());

        $request = new AddMaterialRequest();
        $request->setAccount($material->getAccount());
        $request->setFile($uploadFile);
        $request->setType($material->getType());

        $response = $this->client->request($request);
        $material->setMediaId($response['media_id']);
        $material->setUrl($response['url']);

        $this->entityManager->persist($material);
        $this->entityManager->flush();
    }

    /**
     * 读取远程URL的内容，并生成一个上传文件对象
     */
    private function generateUploadFileFromUrl(string $url): UploadedFile
    {
        $content = file_get_contents($url);
        $file = tempnam(sys_get_temp_dir(), 'upload_file');
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
        if (!$material->getMediaId()) {
            return;
        }

        $request = new DeleteMaterialRequest();
        $request->setAccount($material->getAccount());
        $request->setMediaId($material->getMediaId());
        $this->client->asyncRequest($request);
    }
}
