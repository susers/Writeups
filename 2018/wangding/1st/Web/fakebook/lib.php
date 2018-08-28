<?php

function sha512($data)
{
    return hash('sha512', $data);
}

function xss($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function anti_object_injection($unserializedData)
{
    if (preg_match("/O:/i", $unserializedData))
    {
        return 0;
    }

    else
        return 1;
}