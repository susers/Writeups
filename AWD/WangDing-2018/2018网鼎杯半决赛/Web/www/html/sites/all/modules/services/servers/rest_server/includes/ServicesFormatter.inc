<?php

interface ServicesFormatterInterface {
  /**
   * Render data to the string.
   *
   * @param $data
   *   Data to render
   *
   * @return string
   */
  public function render($data);
}

class ServicesJSONFormatter implements ServicesFormatterInterface {
  public function render($data) {
    // json_encode doesn't give valid json with data that isn't an array/object.
    if (is_scalar($data)) {
      $data = array($data);
    }
    return str_replace('\\/', '/', json_encode($data));
  }
}

class ServicesJSONPFormatter extends ServicesJSONFormatter {
  public function render($data) {
    $json = parent::render($data);

    if (isset($_GET['callback'])) {
      $callback = preg_replace("/[^A-Za-z0-9_\-\.]/", '', $_GET['callback']);
      $callback = substr($callback, 0, 60);
      return sprintf('%s(%s);', $callback, $json);
    }
    return $json;
  }
}

class ServicesPHPFormatter implements ServicesFormatterInterface {
  public function render($data) {
    return serialize($data);
  }
}

class ServicesXMLFormatter implements ServicesFormatterInterface {
  public function render($data) {
    $doc = new DOMDocument('1.0', 'utf-8');
    $root = $doc->createElement('result');
    $doc->appendChild($root);

    $this->xml_recursive($doc, $root, $data);

    return $doc->saveXML();
  }

  private function xml_recursive(&$doc, &$parent, $data) {
    if (is_object($data)) {
      $data = get_object_vars($data);
    }

    if (is_array($data)) {
      $assoc = FALSE || empty($data);
      foreach ($data as $key => $value) {
        if (is_numeric($key)) {
          $key = 'item';
        }
        else {
          $assoc = TRUE;
          $key = preg_replace('/[^A-Za-z0-9_]/', '_', $key);
          $key = preg_replace('/^([0-9]+)/', '_$1', $key);
        }
        $element = $doc->createElement($key);
        $parent->appendChild($element);
        $this->xml_recursive($doc, $element, $value);
      }

      if (!$assoc) {
        $parent->setAttribute('is_array', 'true');
      }
    }
    elseif ($data !== NULL) {
      $parent->appendChild($doc->createTextNode($data));
    }
  }
}

class ServicesYAMLFormatter implements ServicesFormatterInterface {
  public function render($data) {
    if (($library = libraries_load('spyc')) && !empty($library['loaded'])) {
      return Spyc::YAMLDump($data, 4, 60);
    }
    else {
      watchdog('REST Server', 'Spyc library not found!', array(), WATCHDOG_ERROR);
      return '';
    }
  }
}

class ServicesBencodeFormatter implements ServicesFormatterInterface {
  public function render($data) {
    module_load_include('php', 'rest_server', 'lib/bencode');
    return bencode($data);
  }
}
