<?php

namespace app\model\payload;

use app\model\enums\DownloaderMarkerEnums;
use Iyuu\BittorrentClient\Utils;

/**
 * 自动辅种表payload有效载荷对象
 */
class ReseedPayload
{
    /**
     * 下载器标记规则
     * @var string
     */
    public string $marker = DownloaderMarkerEnums::Empty->value;

    /**
     * 自动校验
     * @var string
     */
    public string $auto_check = '';

    /**
     * @param string|null $payload
     */
    public function __construct(string $payload = null)
    {
        if ($payload) {
            $properties = json_decode($payload, true);
            foreach ($properties as $property => $value) {
                if (property_exists($this, $property)) {
                    $this->{$property} = $value;
                }
            }
        }
    }

    /**
     * 判断是否自动校验
     * @return bool
     */
    public function isAutoCheck(): bool
    {
        return Utils::booleanParse($this->auto_check);
    }

    /**
     * 获取下载器标记规则枚举对象
     * @return DownloaderMarkerEnums
     */
    public function getMarkerEnum(): DownloaderMarkerEnums
    {
        return DownloaderMarkerEnums::from($this->marker);
    }

    /**
     * 转数组
     * @return array
     */
    final public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * 转字符串
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
