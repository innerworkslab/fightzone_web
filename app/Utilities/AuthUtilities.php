<?php

if (!function_exists('ApiUser')) {
    function ApiUser()
    {
        return auth('sanctum')->user();
    }
}

if (!function_exists('WebUser')) {
    function WebUser()
    {
        return auth('web')->user();
    }
}
