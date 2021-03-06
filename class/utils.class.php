<?php
namespace NERDZ\Core;

class Utils
{
    public static $REGISTER_DB_MESSAGE = [ 'error', 'REGISTER' ];
    public static $ERROR_DB_MESSAGE    = [ 'error', 'ERRROR' ];

    public static function apc_getLastModified($key)
    {
        $cache = apc_cache_info('user');

        if (empty($cache['cache_list']))
            return false;

        foreach($cache['cache_list'] as $entry)
        {
            if($entry['info'] != $key)
                continue;

            return $entry['creation_time'];
        }
    }

    public static function apc_get($key)
    {
        if(apc_exists($key))
            return unserialize(apc_fetch($key));
        return null;
    }

    public static function apc_set($key, callable $setter, $ttl)
    {
        $ret = $setter();
        @apc_store ($key, serialize($ret), $ttl);
        return $ret;
    }

    public static function isValidURL($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function getValidImageURL($url)
    {
        $url        = strip_tags(trim($url));
        $domain     = System::getResourceDomain();

        if (!static::isValidURL($url))
            return $domain.'/static/images/invalidImgUrl.php';

        // Proxy every image that's not in data\trusted-host.json
        $cache = 'nerdz_trusted'.Config\SITE_HOST;
        if(!($trusted_hosts = Utils::apc_get($cache)))
            $trusted_hosts = Utils::apc_set($cache, function() {
                $txt = file_get_contents($_SERVER['DOCUMENT_ROOT'] .'/data/trusted-hosts.json');
                return json_decode (preg_replace ('#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#', '', $txt), true);
            }, 86400);

        // Avoid IP address (and other user info) spoofing
        $urlInfo = parse_url($url);
        foreach($trusted_hosts as $host) {
            if(preg_match($host['regex'], $urlInfo['host'])) {
                if($urlInfo['scheme'] !== 'https') {
                    $count = 1; // str_replace wants the count parameter passed by referece (mfw)
                    return str_replace('http', 'https', $url, $count);
                }
                return $url;
            }
        }
        // If here, host is not a trusted host
        return Config\CAMO_KEY === '' || Config\CAMO_HOST === ''
            ? 'https://i0.wp.com/' . preg_replace ('#^https?://|^ftp://#i', '', $url)
            : 'https://'.Config\CAMO_HOST.'/'.hash_hmac('sha1', $url, Config\CAMO_KEY).'?url='.urlencode($url);
    }

    private static function getLink($name) {
        return str_replace(' ','+',urlencode(html_entity_decode($name,ENT_QUOTES,'UTF-8')));
    }

    public static function userLink($user)
    {
        return Utils::getLink($user).'.';
    }

    public static function projectLink($name)
    {
        return Utils::getLink($name).':';
    }

    public static function minifyHTML($str)
    {
        return Config\MINIFICATION_ENABLED
            ? preg_replace('#>\s+<#','> <',preg_replace('#^\s+|\s+$|\n#m','',$str))
            : $str;
    }

    public static function toJsonResponse($status, $message)
    {
        $ret = is_array($status) ? $status : ['status' => $status, 'message' => $message];
        return json_encode($ret);
    }

    public static function jsonResponse($status, $message = '')
    {
        header('Content-type: application/json; charset=utf-8');
        return static::toJsonResponse($status, $message);
    }

    public static function jsonDbResponse($msg, $otherInfo = '')
    {
        $user = new User();
        $res = $user->parseDbMessage($msg, $otherInfo);
        return static::jsonResponse($res[0], $res[1]);
    }

    public static function getSiteName()
    {
        return Config\SITE_NAME.( User::isOnMobileHost() ? 'Mobile' : '' );
    }

    public static function sortByUsername($a, $b)
    {
        return (strtolower($a['username_n']) < strtolower($b['username_n'])) ? -1 : 1;
    }

    public static function actionValidator($action)
    {
        return in_array($action, [ 'friends', 'followers', 'following', 'interactions', 'members' ])
            ? $action
            : false;
    }

    public static function in_arrayi($needle, $haystack) {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }
}
