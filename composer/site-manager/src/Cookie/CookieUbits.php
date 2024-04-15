<?php

namespace Iyuu\SiteManager\Cookie;

use Iyuu\SiteManager\BaseCookie;
use Iyuu\SiteManager\Frameworks\NexusPhp\HasCookie;
use Iyuu\SiteManager\Spider\Pagination;

/**
 * ubits
 * - 凭cookie解析HTML列表页
 */
class CookieUbits extends BaseCookie
{
    use HasCookie, Pagination;

    /**
     * 站点名称
     */
    public const SITE_NAME = 'ubits';
}
