<?php
namespace paymentCms\component;

class cookie
{

    /**
     * @param $key → key name the data to save
     * @param $value → data to save
     * @param int $time → expiration time in minutes (default value is 1440 min == 1 day)
     *
     */
    public static function set($key, $value, $time = 1440, $path = "/", $domain = null, $https = false, $httpOnly = true)
    {
        setcookie($key, $value, time() + ($time * 60), $path, $domain, $https, $httpOnly);
    }

    public static function get($key=null)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $_COOKIE;
    }

    /**
     * destroys the cookies
     * @param string $key → cookie name to destroy. Not set to delete all
     */
    public static function destroy($key = "")
    {
        if (isset($_COOKIE[$key])) {
            setcookie($key, "", time() - 3600, "/");
            return;
        }
        if (count($_COOKIE) > 0) {
            foreach ($_COOKIE as $key => $value) {
                setcookie($key, "", time() - 3600, "/");
            }
        }
    }
}