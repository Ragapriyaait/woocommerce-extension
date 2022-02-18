<?php
/**
 * This file is part of the WPMKTGENGINE plugin.
 *
 * Copyright 2016 Genoo, LLC. All rights reserved worldwide.  (web: http://www.wpmktgengine.com/)
 * GPL Version 2 Licensing:
 *  PHP code is licensed under the GNU General public static License Ver. 2 (GPL)
 *  Licensed "As-Is"; all warranties are disclaimed.
 *  HTML: http://www.gnu.org/copyleft/gpl.html
 *  Text: http://www.gnu.org/copyleft/gpl.txt
 *
 * Proprietary Licensing:
 *  Remaining code elements, including without limitation:
 *  images, cascading style sheets, and JavaScript elements
 *  are licensed under restricted license.
 *  http://www.wpmktgengine.com/terms-of-service
 *  Copyright 2016 Genoo LLC. All rights reserved worldwide.
 */

namespace WPME;

/**
 * Class Helper
 *
 * @package WPME
 */
class Helper
{

    /**
     * @param $user
     */
    public static function setLeadCookieFromUser($user)
    {
        $user_id = $user instanceof \WP_User ? $user->ID : $user;
        $lead_id = \get_user_meta($user_id, WPMKTENGINE_LEAD_COOKIE, TRUE);
        if(is_numeric($lead_id)){
            setcookie(WPMKTENGINE_LEAD_COOKIE, $lead_id, time() + 31556926, COOKIE_PATH, COOKIE_DOMAIN);
        }
    }

    /**
     * @param $user
     *
     * @return mixed
     */ 
    public static function getUserLeadIdFromUser($user)
    {
        $user_id = $user instanceof \WP_User ? $user->ID : $user;
        return $lead_id = \get_user_meta($user->ID, WPMKTENGINE_LEAD_COOKIE, TRUE);
    }

    /**
     * @return null
     */
    public static function getUserLeadIdFromCookie()
    {
        if(self::hasLeadCookie()){
            return $_COOKIE[WPMKTENGINE_LEAD_COOKIE];
        }
        return NULL;
    }

    /**
     * @return bool
     */
    public static function hasLeadCookie()
    {
        if(isset($_COOKIE) && is_array($_COOKIE) && array_key_exists(WPMKTENGINE_LEAD_COOKIE, $_COOKIE)){
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @param $user
     * @param $cookie
     */
    public static function setUserCookieToUser($user, $cookie)
    {
        $user_id = $user instanceof \WP_User ? $user->ID : $user;
        \add_user_meta((int)$user_id, WPMKTENGINE_LEAD_COOKIE, $cookie);
        \update_user_meta((int)$user_id, WPMKTENGINE_LEAD_COOKIE, $cookie);
    }

    /**
     * @param $lead_id
     */
    public static function setUserCookie($lead_id)
    {
        if(!headers_sent()){
            setcookie(WPMKTENGINE_LEAD_COOKIE, $lead_id, time() + 31556926);
        }
    }

    /**
     * @return bool|mixed|null
     */
    public static function loggedInOrCookie()
    {
        $inCookie = self::getUserLeadIdFromCookie();
        $inUser = FALSE;
        if(is_user_logged_in()){
            $user = wp_get_current_user();
            $inUser = self::getUserLeadIdFromUser($user);
            $inUser = !empty($inUser) ? $inUser : FALSE;
        }
        if($inCookie !== NULL || $inCookie !== FALSE){
            return $inCookie;
        }
        return $inUser;
    }

    /**
     * @return bool
     */
    public static function canContinue()
    {
        $can = self::loggedInOrCookie();
        if($can !== NULL || $can !== FALSE && is_numeric($can)){
            return TRUE;
        }
        return FALSE;
    }
}