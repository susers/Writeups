<?php

/**
 * Factory to build RESTServer object for testing.
 */
class MockServicesRESTServerFactory extends ServicesRESTServerFactory {
  static $class_name = 'MockRESTServer';

  public function __construct($data = array()) {
    parent::__construct($data);
    drupal_static_reset();
  }

  protected function getContext() {
    $context = new MockServicesContext($this->data['endpoint_path']);
    $context->setData($this->data['context_data']);
    return $context;
  }

  protected function getResources() {
    if (isset($this->data['resources'])) {
      return $this->data['resources'];
    }
    return array();
  }

  protected function getFormatters() {
    if (isset($this->data['formatters'])) {
      return $this->data['formatters'];
    }
    return array();
  }

  protected function getParsers() {
    if (isset($this->data['parsers'])) {
      return $this->data['parsers'];
    }
    return array();
  }
}

/**
 * Mock ServicesContext object.
 */
class MockServicesContext extends ServicesContext {
  public function setData($data) {
    $this->data = array_merge($this->data, $data);
  }
}

/**
 * Extend RESTServer to test protected methods.
 */
class MockRESTServer extends RESTServer {
  public function protectedGetControllerArgumentsFromSources($controller, $sources) {
    return $this->getControllerArgumentsFromSources($controller, $sources);
  }

  public function protectedRender($formatter, $result) {
    return $this->render($formatter, $result);
  }

  public function protectedGetResourceName() {
    return $this->getResourceName();
  }

  public function protectedGetResponseFormatter() {
    return $this->getResponseFormatter();
  }

  public function protectedResolveController($resource, &$operation) {
    return $this->resolveController($resource, $operation);
  }

  public function protectedParseRequestBody() {
    return $this->parseRequestBody();
  }
}