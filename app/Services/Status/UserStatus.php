<?php

namespace App\Services\Status;

final class UserStatus {

    const NEW                           = 'new';
    const DONE                          = 'done';
    const MAIN_MENU                     = 'main menu';
    const SETTINGS                      = 'in settings';

    const SETTINGS_LOCATION_TYPE_SELECT = 'selecting location type in settings';
    const SETTINGS_DISTRICT_SELECT      = 'selecting district in settings';
    const SETTINGS_LOCATION_WAITING     = 'waiting for location in settings';
    const SETTINGS_LOCATION_SELECTING   = 'selecting location in settings';
    const SETTINGS_CITY_NAME            = 'selecting city by name in settings';

    const CITY_MENU                     = 'in city menu';
    const USER_CITY_LIST                = 'selecting existing city from list';

    const DISTRICT_SELECT               = 'selecting district';
    const FEEDBACK                      = 'writing feedback';
    const FORECAST_CITY_SELECT          = 'selecting city for forecast';
    const CURRENT_CITY_SELECT           = 'selecting current city';

}
