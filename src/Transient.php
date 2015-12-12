<?php
/**
 * Transient
 *
 * @author Whizark <devaloka@whizark.com>
 * @see http://whizark.com
 * @copyright Copyright (C) 2015 Whizark.
 * @license MIT
 * @license GPL-2.0
 * @license GPL-3.0
 */

namespace Devaloka\Transient;

/**
 * Class Transient
 *
 * @package Devaloka\Transient
 */
class Transient
{
    /**
     * @param string $name
     * @param int|null $siteId
     * @param int|null $networkId
     *
     * @return bool
     */
    public function delete($name, $siteId = null, $networkId = null)
    {
        $siteId = ($siteId !== null) ? $siteId : get_current_blog_id();

        if ($networkId === null) {
            $networkId = defined('SITE_ID_CURRENT_SITE') ? SITE_ID_CURRENT_SITE : 1;
        }

        do_action('devaloka_delete_site_transient_' . $name, $name, $siteId, $networkId);

        // Get Transient
        $defaults         = [
            'time'       => time(),
            'expiration' => 0,
            'transients' => [],
        ];
        $networkTransient = get_site_transient($name);
        $networkTransient = ($networkTransient !== false) ? array_merge($defaults, $networkTransient) : $defaults;
        $transients       = is_array($networkTransient['transients']) ? $networkTransient['transients'] : [];
        $networkExists    = (count($transients) !== 0) && array_key_exists($networkId, $transients);
        $siteExists       = $networkExists && array_key_exists($siteId, $transients[$networkId]);

        if (!$networkExists || !$siteExists) {
            return false;
        }

        // Delete Site Transient
        unset($transients[$networkId][$siteId]);

        // Delete Network Transient if empty
        if (count($transients[$networkId]) === 0) {
            unset($transients[$networkId]);
        }

        $networkTransient['transients'] = $transients;
        $isEmpty                        = (count($transients) === 0);

        // Delete or replace Transient
        if ($isEmpty) {
            // Delete Transient if empty
            $isDeleted = delete_site_transient($name);
        } else {
            // Replace Transient with new values
            $isDeleted = set_site_transient($name, $networkTransient, $networkTransient['expiration']);
        }

        if ($isDeleted) {
            do_action('devaloka_deleted_site_transient', $name, $siteId, $networkId);
        }

        return $isDeleted;
    }

    /**
     * @param string $name
     * @param int|null $siteId
     * @param int|null $networkId
     *
     * @return bool
     */
    public function get($name, $siteId = null, $networkId = null)
    {
        $siteId = ($siteId !== null) ? $siteId : get_current_blog_id();

        if ($networkId === null) {
            $networkId = defined('SITE_ID_CURRENT_SITE') ? SITE_ID_CURRENT_SITE : 1;
        }

        $value = apply_filters('devaloka_pre_site_transient_' . $name, false, $name, $siteId, $networkId);

        if ($value !== false) {
            return $value;
        }

        // Get Transient
        $defaults         = [
            'transients' => [],
        ];
        $networkTransient = get_site_transient($name);
        $networkTransient = ($networkTransient !== false) ? array_merge($defaults, $networkTransient) : $defaults;
        $transients       = is_array($networkTransient['transients']) ? $networkTransient['transients'] : [];

        // Get Site Transient
        $networkExists = (count($transients) !== 0) && array_key_exists($networkId, $transients);
        $siteExists    = $networkExists && array_key_exists($siteId, $transients[$networkId]);
        $value         = ($networkExists && $siteExists) ? $transients[$networkId][$siteId] : false;

        return apply_filters('devaloka_site_transient_' . $name, $value, $name, $siteId, $networkId);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param int $expiration
     * @param int|null $siteId
     * @param int|null $networkId
     *
     * @return bool
     */
    public function set($name, $value, $expiration = 0, $siteId = null, $networkId = null)
    {
        $siteId = ($siteId !== null) ? $siteId : get_current_blog_id();

        if ($networkId === null) {
            $networkId = defined('SITE_ID_CURRENT_SITE') ? SITE_ID_CURRENT_SITE : 1;
        }

        $value = apply_filters(
            'devaloka_pre_set_site_transient_' . $name,
            $value,
            $expiration,
            $name,
            $siteId,
            $networkId
        );

        $expiration = (int) apply_filters(
            'devaloka_expiration_of_site_transient_' . $name,
            $expiration,
            $value,
            $name,
            $siteId,
            $networkId
        );

        // Get Transient
        $defaults         = [
            'time'       => time(),
            'expiration' => $expiration,
            'transients' => [],
        ];
        $networkTransient = get_site_transient($name);
        $networkTransient = ($networkTransient !== false) ? array_merge($defaults, $networkTransient) : $defaults;
        $transients       = is_array($networkTransient['transients']) ? $networkTransient['transients'] : [];

        // Set Transient
        $transients[$networkId][$siteId] = $value;
        $networkTransient['transients']  = $transients;
        $isSet                           = set_site_transient($name, $networkTransient, $expiration);

        if ($isSet) {
            do_action('devaloka_set_site_transient_' . $name, $value, $expiration, $name, $siteId, $networkId);
            do_action('devaloka_setted_site_transient', $name, $value, $expiration, $siteId, $networkId);
        }

        return $isSet;
    }
}
