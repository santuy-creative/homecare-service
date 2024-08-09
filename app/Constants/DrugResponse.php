<?php

namespace App\Constants;

class DrugResponse {

    CONST SUCCESS                           = 'success';
    CONST SUCCESS_CREATED                   = 'Drug created successfully.';
    CONST SUCCESS_ALL_RETRIEVED             = 'Drug retrieved successfully.';
    CONST SUCCESS_RETRIEVED                 = 'Drug retrieved successfully.';
    CONST SUCCESS_UPDATED                   = 'Drug updated successfully.';
    CONST SUCCESS_DELETED                   = 'Drug deleted successfully.';
    CONST NOT_FOUND                         = 'Drug is not found';
    CONST ERROR                             = 'error';
    CONST EXIST                             = 'Drug is already exist';
    CONST IN_USED                           = 'Drug is in used. You can`t delete it';
    CONST NOT_AUTHORIZED                    = 'You are not authorized to perform this action';
}
