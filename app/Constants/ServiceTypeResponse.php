<?php

namespace App\Constants;

class ServiceTypeResponse {

    CONST SUCCESS                           = 'success';
    CONST SUCCESS_CREATED                   = 'Service Type created successfully.';
    CONST SUCCESS_ALL_RETRIEVED             = 'Service Type retrieved successfully.';
    CONST SUCCESS_RETRIEVED                 = 'Service Type retrieved successfully.';
    CONST SUCCESS_UPDATED                   = 'Service Type updated successfully.';
    CONST SUCCESS_DELETED                   = 'Service Type deleted successfully.';
    CONST NOT_FOUND                         = 'Service Type is not found';
    CONST ERROR                             = 'error';
    CONST EXIST                             = 'Service Type is already exist';
    CONST IN_USED                           = 'Service Type is in used. You can`t delete it';
    CONST NOT_AUTHORIZED                    = 'You are not authorized to perform this action';
}
