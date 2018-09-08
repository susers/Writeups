<?php

interface ServicesContentTypeNegotiatorInterface {
  public function getResponseFormatFromURL($canonical_path);
  public function getResponseFormatContentTypeNegotiations(&$mime_type, $canonical_path, $formats, $context);
}

/**
 * Class used to do Content Type negotiation.
 */

class ServicesContentTypeNegotiator implements ServicesContentTypeNegotiatorInterface {

  /**
   * Retrieve formatter from URL. If format is in the path, we remove it from $canonical_path.
   *
   * For example <endpoint>/<path>.<format>
   *
   * @param $canonical_path
   *
   * @return string
   */
  public function getResponseFormatFromURL($canonical_path) {
    $matches = $this->getCanonicalPathMatches($canonical_path);
    return $matches[2];
  }

  /**
   * Parse canonical path. It may have extension in the end (example node/1.json).
   * This function returns canonical path without extension.
   *
   * @param string $canonical_path
   *
   * @return string
   *   Canonical path without extension.
   */
  public function getParsedCanonicalPath($canonical_path) {
    $matches = $this->getCanonicalPathMatches($canonical_path);
    return $matches[1];
  }

  /**
   * Preg match canonical path to split it to clean path and extension.
   *
   * @param string $canonical_path
   *   Canonical path with extension.
   * @return array
   *   Array of matches.
   */
  public function getCanonicalPathMatches($canonical_path) {
    $matches = array();
    if (preg_match('/^(.+)\.([^\.^\/]+)$/', $canonical_path, $matches)) {
      return $matches;
    }
    return array('', $canonical_path, '');
  }

  /**
   * Determine response format and mime type using headers to negotiate content types.
   *
   * @param string $mime_type
   *   Mime type. This variable to be overriden.
   * @param string $canonical_path
   *   Canonical path of the request.
   * @param array $formats
   *   Enabled formats by endpoint.
   *
   * @return string
   *   Negotiated response format. For example 'json'.
   */
  public function getResponseFormatContentTypeNegotiations(&$mime_type, $canonical_path, $formats, $context) {
    drupal_add_http_header('Vary', 'Accept');

    // Negotiate response format based on accept-headers if we
    // don't have a response format.
    $mime_candidates = array();
    $mime_map = array();

    foreach ($formats as $format => $formatter) {
      foreach ($formatter['mime types'] as $m) {
        $mime_candidates[] = $m;
        $mime_map[$m] = $format;
      }
    }

    // Get the best matching format, default to json
    $response_format = variable_get('rest_server_default_response_format', 'json');
    $http_accept = $context->getServerVariable('HTTP_ACCEPT');
    if (!empty($http_accept)) {
      $mime = $this->mimeParse();
      $mime_type = $mime->best_match($mime_candidates, $http_accept);
      $response_format = isset($mime_map[$mime_type]) ? $mime_map[$mime_type] : '';
    }

    return $response_format;
  }

  /**
   * Create a instance of the Mimeparse utility class.
   *
   * @return Mimeparse
   */
  public function mimeParse() {
    static $mimeparse;
    if (!$mimeparse) {
      module_load_include('php', 'rest_server', 'lib/mimeparse');
      $mimeparse = new Mimeparse();
    }
    return $mimeparse;
  }
}
