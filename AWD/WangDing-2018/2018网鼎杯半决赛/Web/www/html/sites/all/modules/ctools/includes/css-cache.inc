<?php

/**
 * @file
 * Custom cache implementation for the CTools CSS cache.
 */

class CToolsCssCache implements DrupalCacheInterface {

  /**
   * {@inheritdoc}
   */
  public function clear($cid = NULL, $wildcard = FALSE) {
    // Only clear the caches if the wildcard is set, this ensures that the cache
    // is only cleared when the full caches are cleared manually (eg by invoking
    // drupal_flush_all_caches()), and not on a cron run.
    // @see drupal_flush_all_caches()
    // @see system_cron()
    if ($wildcard) {
      ctools_include('css');
      ctools_css_flush_caches();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function get($cid) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(&$cids) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function set($cid, $data, $expire = CACHE_PERMANENT) {
  }

}
