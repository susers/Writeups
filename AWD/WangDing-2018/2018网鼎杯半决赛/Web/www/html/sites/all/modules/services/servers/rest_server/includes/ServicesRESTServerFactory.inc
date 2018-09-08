<?php

/**
 * Factory class to build RESTServer object.
 *
 * If you want to change it
 */

class ServicesRESTServerFactory {
  protected $data = array();
  static $class_name = 'RESTServer';

  /**
   * We need data property to pass additional arguments to methods.
   *
   * Required property is 'endpoint_path' -- base path of endpoint.
   * Example: GET rest/node/1.php -- "rest" is endpoint path.
   */
  public function __construct($data = array()) {
    if (!isset($data['endpoint_path'])) {
      throw new Exception('ServicesRESTServerFactory constructor requires "endpoint_data" property.');
    }
    $this->data = $data;
  }

  public function getRESTServer() {
    $content_type_negotiator = $this->getContentTypeNegotiator();
    $context = $this->getContext();
    $resources = $this->getResources();
    $parsers = $this->getParsers();
    $formatters = $this->getFormatters();

    $class_name = static::$class_name;
    return new $class_name($context, $content_type_negotiator, $resources, $parsers, $formatters);
  }

  protected function getContentTypeNegotiator() {
    return new ServicesContentTypeNegotiator();
  }

  protected function getContext() {
    $context = new ServicesContext($this->data['endpoint_path']);
    $context->buildFromGlobals();
    return $context;
  }

  protected function getResources() {
    $endpoint_name = services_get_server_info('endpoint', '');
    $endpoint = services_endpoint_load($endpoint_name);
    $resources = services_get_resources($endpoint->name);
    module_load_include('inc', 'services', 'includes/services.resource_build');
    _services_apply_endpoint($resources, $endpoint, TRUE);

    return $resources;
  }

  protected function getEndpoint() {
    $endpoint_name = services_get_server_info('endpoint', '');
    return services_endpoint_load($endpoint_name);
  }

  protected function getEndpointSettings() {
    static $settings;

    if (empty($settings)) {
      $endpoint = $this->getEndpoint();

      // Get the server settings from the endpoint.
      $settings = !empty($endpoint->server_settings) ? $endpoint->server_settings : array();
      // Normalize the settings so that we get the expected structure
      // and sensible defaults.
      $settings = rest_server_setup_settings($settings);
    }

    return $settings;
  }

  protected function getParsers() {
    $settings = $this->getEndpointSettings();

    $parsers = rest_server_request_parsers();
    // Remove parsers that have been disabled for this endpoint.
    foreach (array_keys($parsers) as $key) {
      if (!$settings['parsers'][$key]) {
        unset($parsers[$key]);
      }
    }

    return $parsers;
  }

  protected function getFormatters() {
    $settings = $this->getEndpointSettings();

    $formatters = rest_server_response_formatters();
    // Remove formatters that have been disabled for this endpoint.
    foreach (array_keys($formatters) as $key) {
      if (!$settings['formatters'][$key]) {
        unset($formatters[$key]);
      }
    }

    return $formatters;
  }
}
