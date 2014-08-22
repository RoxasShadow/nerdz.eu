<?php
namespace NERDZ\Core;

class Security
{
    public static function refererControl()
    {
        return isset($_SERVER['HTTP_REFERER']) && in_array(parse_url($_SERVER['HTTP_REFERER'])['host'],[ Config\SITE_HOST,Config\MOBILE_HOST ] );
    }

    public static function getCsrfToken($n = '')
    {
        $_SESSION['tok_'.$n] = isset($_SESSION['tok_'.$n]) ? $_SESSION['tok_'.$n] : md5(uniqid(rand(7,21)));
        return $_SESSION['tok_'.$n];
    }

    public static function csrfControl($tok,$n = '')
    {
        if(empty($_SESSION['tok_'.$n]))
            return false;
        return $_SESSION['tok_'.$n] === $tok;
    }

    public static function limitControl($limit,$n)
    {
        if(is_numeric($limit) && $limit < $n && $limit > 0)
            return $limit;

        if(!is_string($limit))
            return $n;

        $r = sscanf($limit,'%d,%d',$a,$b);

        if($r != 2)
            $r = sscanf($limit,'%d OFFSET %d',$a,$b);

        if($r != 2 || ($r == 2 && $b > $n))
            return $n;

        return "{$b} OFFSET {$a}";
    }

    public static function fieldControl($field, array $fields, $default = '')
    {
        if(Utils::in_arrayi($field, $fields))
            return $field;

        return $default;
    }

    public static function setNextAndPrevURLs(array &$vals, $limit, array $options)
    {

        extract($options);
        $order = !empty($order) ? $order : false;
        $query = !empty($query) ? $query : false;
        $field = !empty($field) ? $field : false;
        $validFields = !empty($validFields) && is_array($validFields) ? $validFields : [];

        $limit = static::limitControl($limit, 20);

        $queryParams = [];
        $queryParams['order'] = $order ? 'desc='.(trim(strtolower($order)) == 'desc' ? '1' : '0') : '';
        $queryParams['query'] = $query ? 'q='.(trim(htmlspecialchars($query, ENT_QUOTES, 'UTF-8', false))) : '';

        if(static::fieldControl($field, $validFields))
            $queryParams['field'] = 'orderby='.$field;

        $url = '?'.implode('&amp;',$queryParams);

        if(is_numeric($limit)) {
            $vals['prev_url_n'] = '';
            $vals['next_url_n'] = count($vals['list_a']) == 20 ? $url.'&amp;lim=20,20' : '';
        } else {
            $limitnext = $limitprev = '20';

            if(2 == sscanf($_GET['lim'],"%d,%d",$a,$b)) {
                $next = $a+20;
                $prev = $a-20;
                $limitnext = "{$next},20";
                $limitprev = $prev >0 ? "{$prev},20" : '20';
            }

            $vals['next_url_n'] = count($vals['list_a']) == 20 ? $url."&amp;lim={$limitnext}" : '';
            $vals['prev_url_n'] = $url."&amp;lim={$limitprev}";
        }
    }
}
?>
