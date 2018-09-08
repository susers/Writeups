<?php

/**
 * @file
 * Class for handling REST calls.
 */

class RESTServer {
  /* @var $negotiator ServicesContentTypeNegotiatorInterface */
  protected $negotiator;
  /* @var $context ServicesContextInterface */
  protected $context;

  protected $resources;
  protected $parsers;
  protected $formatters;

  protected $controller;

  /**
   * Constructor. Initialize properties.
   */
  function __construct(ServicesContextInterface $context, ServicesContentTypeNegotiatorInterface $negotiator, $resources, $parsers, $formatters) {
    $this->context = $context;
    $this->negotiator = $negotiator;

    $this->resources = $resources;
    $this->parsers = $parsers;
    $this->formatters = $formatters;
  }

  /**
   * Handles the call to the REST server
   */
  public function handle() {
    $controller = $this->getController();

    $formatter = $this->getResponseFormatter();

    services_set_server_info('resource_uri_formatter', array(&$this, 'uri_formatter'));
    // Initializing $arguments because an exception can be thrown for required
    // arguments and before $arguments is initialized for the exception handler.
    $arguments = array();
    try {
      // Parse the request data
      $arguments = $this->getControllerArguments($controller);
      $result = services_controller_execute($controller, $arguments);
    }
    catch (ServicesException $e) {
      $result = $this->handleException($e, $controller, $arguments);

      $no_body = variable_get('services_no_body_responses', array(204, 304));
      $generate_body = variable_get('services_generate_error_body', TRUE);

      if (in_array($e->getCode(), $no_body) && !$generate_body) {
        return '';
      }
    }

    return $this->render($formatter, $result);
  }

  /**
   * Controller is part of the resource like
   *
   * array(
   *  'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/node_resource'),
   *  'callback' => '_node_resource_create',
   *  'args' => array(
   *    array(
   *      'name' => 'node',
   *      'optional' => FALSE,
   *      'source' => 'data',
   *      'description' => 'The node data to create',
   *      'type' => 'array',
   *    ),
   *  ),
   *  'access callback' => '_node_resource_access',
   *  'access arguments' => array('create'),
   *  'access arguments append' => TRUE,
   * ),
   *
   * This method determines what is the controller responsible for processing of the request.
   *
   * @return array
   */
  protected function getController() {
    if (empty($this->controller)) {
      $resource_name = $this->getResourceName();

      if (empty($resource_name) || !isset($this->resources[$resource_name])) {
        return services_error(t('Could not find resource @name.', array('@name' => $resource_name)), 404);
      }

      $resource = $this->resources[$resource_name];
      $this->controller = $this->resolveControllerApplyVersion($resource, $resource_name);

      if (empty($this->controller)) {
        return services_error(t('Could not find the controller.'), 404);
      }
    }

    return $this->controller;
  }

  /**
   * Wrapper around resolveController() to apply version.
   *
   * @param array $resource
   *   Resource definition
   * @param string $resource_name
   *   Name of the resource. Needed for applying version.
   *
   * @return array $controller
   *   Controller definition
   */
  protected function resolveControllerApplyVersion($resource, $resource_name) {
    $apply_version_method = '';
    $controller = $this->resolveController($resource, $apply_version_method);
    services_request_apply_version($controller, array('method' => $apply_version_method, 'resource' => $resource_name));

    return $controller;
  }

  /**
   * Canonical path is the url of the request without path of endpoint.
   *
   * For example endpoint has path 'rest'. Canonical of request to url
   * 'rest/node/1.php' will be 'node/1.php'.
   *
   * @return string
   */
  public function getCanonicalPath() {
    // Use drupal_static so we can clear this static cache during unit testing.
    // @see MockServicesRESTServerFactory constructor.
    $canonical_path = &drupal_static('RESTServerGetCanonicalPath');
    if (empty($canonical_path)) {
      $canonical_path = $this->context->getCanonicalPath();
      $canonical_path = $this->negotiator->getParsedCanonicalPath($canonical_path);
    }
    return $canonical_path;
  }

  /**
   * Explode canonical path to parts by '/'.
   *
   * @return array
   */
  protected function getCanonicalPathArray() {
    $canonical_path = $this->getCanonicalPath();
    $canonical_path_array = explode('/', $canonical_path);

    return $canonical_path_array;
  }

  /**
   * Example. We have endpoint with path 'rest'.
   * Request is done to url /rest/node/1.php'.
   * Name of resource in this case is 'node'.
   *
   * @return string
   */
  protected function getResourceName() {
    $canonical_path_array = $this->getCanonicalPathArray();
    $resource_name = array_shift($canonical_path_array);

    return $resource_name;
  }

  /**
   * Response formatter is responsible for encoding the response.
   *
   * @return array
   * example:
   * array(
   *  'xml' => array(
   *    'mime types' => array('application/xml', 'text/xml'),
   *    'formatter class' => 'ServicesXMLFormatter',
   *  ),
   * )
   */
  protected function getResponseFormatter() {
    $mime_type = '';

    $canonical_path_not_parsed = $this->context->getCanonicalPath();
    $response_format = $this->getResponseFormatFromURL($canonical_path_not_parsed);

    if (empty($response_format)) {
      $response_format = $this->getResponseFormatContentTypeNegotiations($mime_type, $canonical_path_not_parsed, $this->formatters);
    }

    $formatter = array();

    if (isset($this->formatters[$response_format])) {
      $formatter = $this->formatters[$response_format];
    }

    // Check if we support the response format and determine the mime type
    if (empty($mime_type) && !empty($formatter)) {
      $mime_type = $formatter['mime types'][0];
    }

    if (empty($response_format) || empty($mime_type)) {
      return services_error(t('Unknown or unsupported response format.'), 406);
    }

    // Set the content type and render output.
    drupal_add_http_header('Content-type', $mime_type);

    return $formatter;
  }


  /**
   * Retrieve formatter from URL. If format is in the path, we remove it from $canonical_path.
   *
   * For example <endpoint>/<path>.<format>
   *
   * @param $canonical_path
   *
   * @return string
   */
  protected function getResponseFormatFromURL($canonical_path) {
    return $this->negotiator->getResponseFormatFromURL($canonical_path);
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
  protected function getResponseFormatContentTypeNegotiations(&$mime_type, $canonical_path, $formats) {
    return $this->negotiator->getResponseFormatContentTypeNegotiations($mime_type, $canonical_path, $formats, $this->context);
  }

  /**
   * Determine the request method
   */
  protected function getRequestMethod() {
    return $this->context->getRequestMethod();
  }

  /**
   * Formats a resource uri
   *
   * @param array $path
   *  An array of strings containing the component parts of the path to the resource.
   * @return string
   *  Returns the formatted resource uri
   */
  public function uri_formatter($path) {
    return url($this->context->getEndpointPath() . '/' . join($path, '/'), array(
      'absolute' => TRUE,
    ));
  }

  /**
   * Parses controller arguments from request
   *
   * @param array $controller
   *  The controller definition
   * @return void
   */
  protected function getControllerArguments($controller) {
    $path_array = $this->getCanonicalPathArray();
    array_shift($path_array);

    $data = $this->parseRequestBody();
    drupal_alter('rest_server_request_parsed', $data, $controller);

    $headers = $this->parseRequestHeaders();
    drupal_alter('rest_server_headers_parsed', $headers);

    $sources = array(
      'path' => $path_array,
      'param' => $this->context->getGetVariable(),
      'data' => $data,
      'headers' => $headers,
    );
    // Map source data to arguments.
    return $this->getControllerArgumentsFromSources($controller, $sources);
  }

  /**
   * array $controller
   *   Controller definition
   * array $sources
   *   Array of sources for arguments. Consists of following elements:
   *  'path' - path requested
   *  'params' - GET variables
   *  'data' - parsed POST data
   *  'headers' - request headers
   *
   * @return array
   */
  protected function getControllerArgumentsFromSources($controller, $sources) {
    $arguments = array();
    if (!isset($controller['args'])) {
      return array();
    }

    foreach ($controller['args'] as $argument_number => $argument_info) {
      // Fill in argument from source
      if (isset($argument_info['source'])) {
        $argument_source = $argument_info['source'];
        if (is_array($argument_source)) {
          $argument_source_keys = array_keys($argument_source);
          $source_name = reset($argument_source_keys);
          $argument_name = $argument_source[$source_name];
          // Path arguments can be only integers. i.e.'path' => 0 and not 'path' => '0'.
          if ($source_name == 'path') {
            $argument_name = (int) $argument_name;
          }
          if (isset($sources[$source_name][$argument_name])) {
            $arguments[$argument_number] = $sources[$source_name][$argument_name];
          }
        }
        else {
          if (isset($sources[$argument_source])) {
            $arguments[$argument_number] = $sources[$argument_source];
          }
        }
        // Convert to specific data type.
        if (isset($argument_info['type']) && isset($arguments[$argument_number])) {
          switch ($argument_info['type']) {
            case 'array':
              $arguments[$argument_number] = (array) $arguments[$argument_number];
              break;
          }
        }
      }
      // When argument isn't set, insert default value if provided or
      // throw a exception if the argument isn't optional.
      if (!isset($arguments[$argument_number])) {
        if (!isset($argument_info['optional']) || !$argument_info['optional']) {
          return services_error(t('Missing required argument @arg', array('@arg' => $argument_info['name'])), 401);
        }
        // Set default value or NULL if default value is not set.
        $arguments[$argument_number] = isset($argument_info['default value']) ? $argument_info['default value'] : NULL;
      }
    }
    return $arguments;
  }

  protected function parseRequestHeaders() {
    $headers = array();
    $http_if_modified_since = $this->context->getServerVariable('HTTP_IF_MODIFIED_SINCE');
    if (!empty($http_if_modified_since)) {
      $headers['IF_MODIFIED_SINCE'] = strtotime(preg_replace('/;.*$/', '', $http_if_modified_since));
    }
    return $headers;
  }

  /**
   * Parse request body based on $_SERVER['CONTENT_TYPE'].s
   *
   * @return array|mixed
   */
  protected function parseRequestBody() {
    $method = $this->getRequestMethod();
    switch ($method) {
      case 'POST':
      case 'PUT':
        $server_content_type = $this->context->getServerVariable('CONTENT_TYPE');
        if (!empty($server_content_type)) {
          $type = $this->parseContentHeader($server_content_type);
        }

        // Get the mime type for the request, default to form-urlencoded
        if (isset($type['value'])) {
          $mime = $type['value'];
        }
        else {
          $mime = 'application/x-www-form-urlencoded';
        }

        // Get the parser for the mime type
        $parser = $this->matchParser($mime, $this->parsers);
        if (!$parser) {
          return services_error(t('Unsupported request content type @mime', array('@mime' => $mime)), 406);
        }

        $data = array();
        if (class_exists($parser) && in_array('ServicesParserInterface', class_implements($parser))) {
          $parser_object = new $parser;
          $data = $parser_object->parse($this->context);
        }
        return $data;

      default:
        return array();
    }
  }

  /**
   * Extract value of the header string.
   *
   * @param string $value
   *
   * @return array $type
   *   Value that is used $type['value']
   */
  protected function parseContentHeader($value) {
    $ret_val = array();
    $value_pattern = '/^([^;]+)(;\s*(.+)\s*)?$/';
    $param_pattern = '/([a-z]+)=(([^\"][^;]+)|(\"(\\\"|[^"])+\"))/';
    $vm=array();

    if (preg_match($value_pattern, $value, $vm)) {
      $ret_val['value'] = $vm[1];
      if (count($vm)>2) {
        $pm = array();
        if (preg_match_all($param_pattern, $vm[3], $pm)) {
          $pcount = count($pm[0]);
          for ($i=0; $i<$pcount; $i++) {
            $value = $pm[2][$i];
            if (drupal_substr($value, 0, 1) == '"') {
              $value = stripcslashes(drupal_substr($value, 1, mb_strlen($value)-2));
            }
            $ret_val['param'][$pm[1][$i]] = $value;
          }
        }
      }
    }

    return $ret_val;
  }

  /**
   * Render results using formatter.
   *
   * @param array $formatter
   *   Formatter definition
   * @param $result
   *   Value to be rendered
   *
   * @return string
   *   Rendered result
   */
  protected function render($formatter, $result) {
    if ( !isset($formatter['formatter class'])
      || array_search('ServicesFormatterInterface', class_implements($formatter['formatter class'])) === FALSE) {
      return services_error('Formatter is invalid.', 500);
    }
    $formatter_object = new $formatter['formatter class'];
    return $formatter_object->render($result);
  }

  /**
   * Matches a mime-type against a set of parsers.
   *
   * @param string $mime
   *  The mime-type of the request.
   * @param array $parsers
   *  An associative array of parser callbacks keyed by mime-type.
   * @return mixed
   *  Returns a parser callback or FALSE if no match was found.
   */
  protected function matchParser($mime, $parsers) {
    $mimeparse = $this->negotiator->mimeParse();
    $mime_type = $mimeparse->best_match(array_keys($parsers), $mime);

    return ($mime_type) ? $parsers[$mime_type] : FALSE;
  }

  /**
   * Determine controller.
   *
   * @param array $resource
   *   Full definition of the resource.
   * @param string $operation
   *   Type of operation ('index', 'retrieve' etc.). We are going to override this variable.
   *   Needed for applying version.
   *
   * @return array
   *   Controller definition.
   */
  protected function resolveController($resource, &$operation) {
    $request_method = $this->getRequestMethod();

    $canonical_path_array = $this->getCanonicalPathArray();
    array_shift($canonical_path_array);

    $canon_path_count = count($canonical_path_array);
    $operation_type = NULL;
    $operation = NULL;

    // For any HEAD request return response "200 OK".
    if ($request_method == 'HEAD') {
      return services_error('OK', 200);
    }

    // For any OPTIONS request return only the headers.
    if ($request_method == 'OPTIONS') {
      exit;
    }

    // We do not group "if" conditions on purpose for better readability.

    // 'index' method.
    if (   $request_method == 'GET'
        && isset($resource['operations']['index'])
        && $this->checkNumberOfArguments($canon_path_count, $resource['operations']['index'])
      ) {
      $operation_type = 'operations';
      $operation = 'index';
    }

    // 'retrieve' method.
    // First path element should be not empty.
    if (   $request_method == 'GET'
        && $canon_path_count >= 1
        && isset($resource['operations']['retrieve'])
        && $this->checkNumberOfArguments($canon_path_count, $resource['operations']['retrieve'])
        && !empty($canonical_path_array[0])
      ) {
      $operation_type = 'operations';
      $operation = 'retrieve';
    }

    // 'relationships'
    // First path element should be not empty,
    // second should be name of targeted action.
    if (   $request_method == 'GET'
        && $canon_path_count >= 2
        && isset($resource['relationships'][$canonical_path_array[1]])
        && $this->checkNumberOfArguments($canon_path_count, $resource['relationships'][$canonical_path_array[1]], 1)
        && isset($canonical_path_array[0])
      ) {
      $operation_type = 'relationships';
      $operation = $canonical_path_array[1];
    }

    // 'update'
    // First path element should be not empty.
    if (   $request_method == 'PUT'
        && $canon_path_count >= 1
        && isset($resource['operations']['update'])
        && $this->checkNumberOfArguments($canon_path_count, $resource['operations']['update'])
        && !empty($canonical_path_array[0])
      ) {
      $operation_type = 'operations';
      $operation = 'update';
    }

    // 'delete'
    // First path element should be not empty.
    if (   $request_method == 'DELETE'
        && $canon_path_count >= 1
        && isset($resource['operations']['delete'])
        && $this->checkNumberOfArguments($canon_path_count, $resource['operations']['delete'])
        && !empty($canonical_path_array[0])
      ) {
      $operation_type = 'operations';
      $operation = 'delete';
    }

    // 'create' method.
    // First path element should be not empty.
    if (   $request_method == 'POST'
        && isset($resource['operations']['create'])
        && $this->checkNumberOfArguments($canon_path_count, $resource['operations']['create'])
      ) {
      $operation_type = 'operations';
      $operation = 'create';
    }

    // 'actions'
    // First path element should be action name
    if (   $request_method == 'POST'
        && $canon_path_count >= 1
        && isset($resource['actions'][$canonical_path_array[0]])
        && $this->checkNumberOfArguments($canon_path_count, $resource['actions'][$canonical_path_array[0]], 1)
      ) {
      $operation_type = 'actions';
      $operation = $canonical_path_array[0];
    }

    // 'targeted_actions'
    // First path element should be not empty,
    // second should be name of targeted action.
    if (   $request_method == 'POST'
        && $canon_path_count >= 2
        && isset($resource['targeted_actions'][$canonical_path_array[1]])
        && $this->checkNumberOfArguments($canon_path_count, $resource['targeted_actions'][$canonical_path_array[1]], 1)
        && !empty($canonical_path_array[0])
      ) {
      $operation_type = 'targeted_actions';
      $operation = $canonical_path_array[1];
    }

    if (empty($operation_type) || empty($operation) || empty($resource[$operation_type][$operation])) {
      return FALSE;
    }

    $controller = $resource[$operation_type][$operation];

    if (isset($resource['endpoint']['operations'][$operation]['settings'])) {
      // Add the endpoint's settings for the specified operation.
      $controller['endpoint'] = $resource['endpoint']['operations'][$operation]['settings'];
    }

    if (isset($resource['file']) && empty($controller['file'])) {
      $controller['file'] = $resource['file'];
    }

    return $controller;
  }

  /**
   * Count possible numbers of 'path' arguments of the method.
   */
  protected function checkNumberOfArguments($args_number, $resource_operation, $required_args = 0) {
    $not_required_args = 0;

    if (isset($resource_operation['args'])) {
      foreach ($resource_operation['args'] as $argument) {
        if (isset($argument['source']) && is_array($argument['source']) && isset($argument['source']['path'])) {
          if (!empty($argument['optional'])) {
            $not_required_args++;
          }
          else {
            $required_args++;
          }
        }
      }
    }

    return $args_number >= $required_args && $args_number <= $required_args + $not_required_args;
  }

  /**
   * Set proper header and message in case of exception.
   *
   * @param object $exception
   *   Exception object
   * @param array $controller
   *   Controller that was executed.
   * @param array $arguments
   *   Set of arguments.
   *
   * @return string $error_data
   *   Error message from exception.
   */
  public function handleException($exception, $controller = array(), $arguments = array()){
    $error_code = $exception->getCode();
    $error_message = $exception->getMessage();
    $error_data = method_exists($exception, 'getData') ? $exception->getData() : '';

    switch ($error_code) {
      case 204:
        $error_header_status_message = $this->formatHttpHeaderStatusMessage('204 No Content', $error_message);
        break;
      case 304:
        $error_header_status_message = $this->formatHttpHeaderStatusMessage('304 Not Modified', $error_message);
        break;
      case 401:
        $error_header_status_message = $this->formatHttpHeaderStatusMessage('401 Unauthorized', $error_message);
        break;
      case 404:
        $error_header_status_message = $this->formatHttpHeaderStatusMessage('404 Not found', $error_message);
        break;
      case 406:
        $error_header_status_message = $this->formatHttpHeaderStatusMessage('406 Not Acceptable', $error_message);
        break;
      case 200:
        $error_header_status_message = $this->formatHttpHeaderStatusMessage('200', $error_message);
        break;
      default:
        if ($error_code >= 400 && $error_code < 600) {
          $error_header_status_message = $this->formatHttpHeaderStatusMessage($error_code, $error_message);
        }
        else {
          watchdog_exception('Services', $exception);
          $error_header_status_message = $this->formatHttpHeaderStatusMessage('500 Internal Server Error', "An error occurred ({$error_code}): {$error_message}");
        }
        break;
    }

    $error_alter_array = array(
      'code' => $error_code,
      'header_message' => &$error_header_status_message,
      'body_data' => &$error_data,
    );
    drupal_alter('rest_server_execute_errors', $error_alter_array, $controller, $arguments);

    drupal_add_http_header('Status', strip_tags(str_replace(PHP_EOL, '', $error_header_status_message)));

    return $error_data;
  }

  /**
   * Formats a status code and message for use as an HTTP header message.
   *
   * @param $status_code
   *   An HTTP status code, e.g. "404 Not found" or "200".
   * @param $message
   *   A message string.
   *
   * @return string
   *   A properly formatted HTTP header status message.
   */
  protected function formatHttpHeaderStatusMessage($status_code, $message) {
    return "{$status_code} : {$message}";
  }
}
