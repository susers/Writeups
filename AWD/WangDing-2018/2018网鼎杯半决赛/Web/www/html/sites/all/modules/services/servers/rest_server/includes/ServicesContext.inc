<?php

/**
 * ServicesContext object is for storing all global variables. The idea is to prevent
 * RESTServer from accessing global variables directly and get them only from Context.
 */

interface ServicesContextInterface {
  public function buildFromGlobals();
  public function getEndpointPath();
  public function getCanonicalPath();
  public function getPostData();
  public function getRequestBody();
  public function getServerVariable($variable_name);
  public function getGetVariable($variable_name = NULL);
}

class ServicesContext implements ServicesContextInterface {
  /**
   * Main storage of all data.
   *
   * @var array
   */
  protected $data = array();

  /**
   * Context constructor.
   *
   * @param string $endpoint_path
   *   Path of endpoint. Example call is to /{endpoint path}/node/1.json.
   *   When context is built we keep endpoint path as separate variable
   *   as it is needed to determine canonical path.
   */
  function __construct($endpoint_path) {
    $this->data['endpoint_path'] = $endpoint_path;
  }

  /**
   * Build all context data from global variables.
   */
  public function buildFromGlobals() {
    $this->data['get'] = $_GET;
    $this->data['server'] = $_SERVER;
    $this->data['post'] = $_POST;
    $this->data['request_body'] = '';
    if ($this->isRequestHasPostBody()) {
      $this->data['request_body'] = $this->getRequestBodyData();
    }
  }

  /**
   * Retrieve endpoint path. It is saved in constructor.
   *
   * @return string
   */
  public function getEndpointPath() {
    return $this->data['endpoint_path'];
  }

  /**
   * Retrieve canonical path.
   *
   * @return string
   */
  public function getCanonicalPath() {
    if (!isset($this->data['canonical_path'])) {
      $endpoint_path = $this->getEndpointPath();
      $endpoint_path_len = drupal_strlen($endpoint_path . '/');
      $this->data['canonical_path'] = drupal_substr($this->data['get']['q'], $endpoint_path_len);
    }

    return $this->data['canonical_path'];
  }

  /**
   * Determine Request Method of the request.
   *
   * @return string
   *   Name of request method (i.e. GET, POST, PUT ...).
   */
  public function getRequestMethod() {
    if (!isset($this->data['request_method'])) {
      $this->data['request_method'] = $this->getRequestMethodFromGlobals();
    }

    return $this->data['request_method'];
  }

  /**
   * Retrieve request method from global variables.
   *
   * @return string
   *   For example GET, POST
   */
  protected function getRequestMethodFromGlobals() {
    $server = &$this->data['server'];
    $get = &$this->data['get'];

    $method = $server['REQUEST_METHOD'];
    if ($method == 'POST' && isset($server['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
      $method = $server['HTTP_X_HTTP_METHOD_OVERRIDE'];
    }
    if ($method == 'POST' && isset($get['_method']) && $get['_method']) {
      $method = $get['_method'];
    }
    if (isset($get['_method'])) {
      unset($get['_method']);
    }

    return $method;
  }

  /**
   * Determine whether we should expect request body to be available.
   *
   * @return bool
   */
  protected function isRequestHasPostBody() {
    $method = $this->getRequestMethod();
    return (in_array($method, array('POST', 'PUT')));
  }

  /**
   * Fetch request body using php://input handler.
   *
   * @return string
   *   Content of the request body.
   */
  protected function getRequestBodyData() {
    return file_get_contents('php://input');
  }

  /**
   * Return value of global $_POST.
   *
   * @return string
   */
  public function getPostData() {
    return $this->data['post'];
  }

  /**
   * Return value of the request body.
   *
   * @return string
   */
  public function getRequestBody() {
    return $this->data['request_body'];
  }

  /**
   * Access to $_SERVER variables.
   *
   * @param string $variable_name
   *   Key of the server variable.
   *
   * @return string
   *   Value of the server variable.
   */
  public function getServerVariable($variable_name) {
    if (isset($this->data['server'][$variable_name])) {
      return $this->data['server'][$variable_name];
    }
    else {
      if ($variable_name == 'CONTENT_TYPE' && isset($this->data['server']['HTTP_CONTENT_TYPE'])) {
        return $this->data['server']['HTTP_CONTENT_TYPE'];
      }
    }
  }

  /**
   * Access to $_GET variables.
   *
   * @param string $variable_name
   *   Name of the variable or NULL if all content of $_GET to be returned.
   *
   * @return mixed
   *   Value of variable or array of all variables.
   */
  public function getGetVariable($variable_name = NULL) {
    if (empty($variable_name)) {
      return $this->data['get'];
    }
    if (isset($this->data['get'][$variable_name])) {
      return $this->data['get'][$variable_name];
    }
  }
}