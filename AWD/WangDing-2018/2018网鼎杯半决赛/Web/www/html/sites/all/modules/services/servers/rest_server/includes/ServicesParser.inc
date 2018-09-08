<?php

interface ServicesParserInterface {
  public function parse(ServicesContextInterface $context);
}

class ServicesParserURLEncoded implements ServicesParserInterface {
  public function parse(ServicesContextInterface $context) {
    parse_str($context->getRequestBody(), $data);
    return $data;
  }
}

class ServicesParserPHP implements ServicesParserInterface {
  public function parse(ServicesContextInterface $context) {
    return unserialize($context->getRequestBody());
  }
}

class ServicesParserXML implements ServicesParserInterface {
  public function parse(ServicesContextInterface $context) {
    // get/hold the old error state
    $old_error_state = libxml_use_internal_errors(1);

    // clear all libxml errors
    libxml_clear_errors();

    // get a now SimpleXmlElement object from the XML string
    $xml_data = simplexml_load_string($context->getRequestBody());

    // if $xml_data is Null then we expect errors
    if (!$xml_data) {
      // build an error message string
      $message = '';
      $errors = libxml_get_errors();
      foreach ($errors as $error) {
        $message .= t('Line @line, Col @column: @message', array('@line' => $error->line, '@column' => $error->column, '@message' => $error->message)) . "\n\n";
      }

      // clear all libxml errors and restore the old error state
      libxml_clear_errors();
      libxml_use_internal_errors($old_error_state);

      // throw an error
      services_error($message, 406);
    }
    // whew, no errors, restore the old error state
    libxml_use_internal_errors($old_error_state);

    // unmarshal the SimpleXmlElement, and return the resulting array
    $php_array = $this->unmarshalXML($xml_data, NULL);
    return (array) $php_array;
  }

  /**
   * A recusive function that unmarshals an XML string, into a php array.
   */
  protected function unmarshalXML($node, $array) {
    // For all child XML elements
    foreach ($node->children() as $child) {
      if (count($child->children()) > 0) {
        // if the child has children
        $att = 'is_array';
        if ($child->attributes()->$att) {
          $new_array = array();
          // recursive through <item>
          foreach($child->children() as $item) {
            // Make sure that elements with no children gets a value assigned.
            $item_keys = array_keys((array) $item);
            if (count($item_keys) == 1 && current($item_keys) === 0) {
              $new_array[] = (string) $item[0];
            }
            elseif (is_object($item)&& ($item->count() == 0)) {
              $new_array[] = (string) $item;
            }
            else {
              $new_array[] = self::unmarshalXML($item, $array[$item->getName()]);
            }
          }
        }
        else {
          // else, simply create an array where the key is name of the element
		  $array[$child->getName()] = array();
          $new_array = $this->unmarshalXML($child, $array[$child->getName()]);
        }
        // add $new_array to $array
        $array[$child->getName()] = $new_array;
      }
      else {
        // Use the is_raw attribute on value elements for select type fields to
        // pass form validation. Example:
        // <field_terms_select>
        //    <und is_array="true">
        //      <item>
        //        <tid is_raw="true">10513</tid>
        //      </item>
        //      <item>
        //        <tid is_raw="true">10523</tid>
        //      </item>
        //    </und>
        //  </field_terms_select>
        if ($child->attributes()->is_raw) {
          return (string) $child;
        }
        $array[$child->getName()] = (string) $child;
      }
    }
    // return the resulting array
    return $array;
  }
}

class ServicesParserJSON implements ServicesParserInterface {
  public function parse(ServicesContextInterface $context) {
    $requestBody = $context->getRequestBody();
    if ($requestBody) {
      $data = json_decode($requestBody, TRUE);
      if ($data === NULL) {
        return services_error(t('Invalid JSON.'), 400);
      }
      return $data;
    }
    return $requestBody;
  }
}

class ServicesParserFile implements ServicesParserInterface {
  public function parse(ServicesContextInterface $context) {
    return $context->getRequestBody();
  }
}

class ServicesParserYAML implements ServicesParserInterface {
  public function parse(ServicesContextInterface $context) {
    if (($library = libraries_load('spyc')) && !empty($library['loaded'])) {
      return Spyc::YAMLLoadString($context->getPostData());
    }
    else {
      watchdog('REST Server', 'Spyc library not found!', array(), WATCHDOG_ERROR);
      return array();
    }
  }
}

class ServicesParserMultipart implements ServicesParserInterface {
  public function parse(ServicesContextInterface $context) {
    // php://input is not available with enctype="multipart/form-data".
    // see http://php.net/manual/en/wrappers.php.php
    return $context->getPostData();
  }
}
