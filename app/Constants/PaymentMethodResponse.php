<?php

namespace App\Constants;

class PaymentMethodResponse {

    CONST SUCCESS                           = 'success';
    CONST SUCCESS_CREATED                   = 'Payment Method created successfully.';
    CONST SUCCESS_ALL_RETRIEVED             = 'Payment Method retrieved successfully.';
    CONST SUCCESS_RETRIEVED                 = 'Payment Method retrieved successfully.';
    CONST SUCCESS_UPDATED                   = 'Payment Method updated successfully.';
    CONST SUCCESS_DELETED                   = 'Payment Method deleted successfully.';
    CONST NOT_FOUND                         = 'Payment Method is not found';
    CONST UNABLE_CHANGE_ADMIN_ROLE          = 'Unable to Change Payment Method. Super Admin always Super Admin';
    CONST ERROR                             = 'error';
    CONST EXIST                             = 'Payment Method is already exist';
    CONST IN_USED                           = 'Payment Method is in used. You can`t delete it';
    CONST NOT_AUTHORIZED                    = 'You are not authorized to perform this action';
    CONST ALREADY_ASSIGNED                  = 'User is already assigned to this Role';
}
