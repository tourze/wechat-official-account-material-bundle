<?php

namespace WechatOfficialAccountMaterialBundle\Tests\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 请求测试辅助Trait
 */
trait RequestTestTrait
{
    /**
     * 创建临时文件并返回文件路径
     */
    protected function createTempFile(string $content = 'test file content', string $extension = 'txt'): string
    {
        $tmpDir = sys_get_temp_dir();
        $filename = tempnam($tmpDir, 'test_');
        $filepath = $filename . '.' . $extension;
        rename($filename, $filepath);
        
        file_put_contents($filepath, $content);
        
        return $filepath;
    }
    
    /**
     * 创建临时图片文件
     */
    protected function createTempImageFile(): string
    {
        // 创建一个1x1像素的黑色PNG图片
        $img = imagecreatetruecolor(1, 1);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefilledrectangle($img, 0, 0, 1, 1, $black);
        
        $filepath = $this->createTempFile('', 'png');
        imagepng($img, $filepath);
        imagedestroy($img);
        
        return $filepath;
    }
    
    /**
     * 创建模拟的上传文件
     */
    protected function createMockUploadedFile(?string $filepath = null): UploadedFile
    {
        if ($filepath === null) {
            $filepath = $this->createTempFile();
        }
        
        return new UploadedFile(
            $filepath,
            basename($filepath),
            null,
            null,
            true // 测试模式
        );
    }
    
    /**
     * 清理测试结束后创建的临时文件
     */
    protected function cleanupTempFiles(array $files): void
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
} 