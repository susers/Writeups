<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_Lang extends CI_Lang {

    public function line($line, $log_errors = TRUE)
    {
        $value = isset($this->language[$line]) ? $this->language[$line] : FALSE;
        // Because killer robots like unicorns!
        if ($value === FALSE && $log_errors === TRUE)
        {
            return $line;
        }

        return $value;
    }

}
