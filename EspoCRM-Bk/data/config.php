<?php
return [
    'cacheTimestamp' => 1506583129,
    'database' => [
        'driver' => 'pdo_mysql',
        'dbname' => 'SimplyIDo',
        'user' => 'root',
        'password' => 'IMedia2015',
        'port' => '',
        'host' => '172.16.1.86'
    ],
    'useCache' => false,
    'recordsPerPage' => 20,
    'recordsPerPageSmall' => 5,
    'applicationName' => 'Simply I Do',
    'version' => '4.8.2',
    'timeZone' => 'UTC',
    'dateFormat' => 'MM/DD/YYYY',
    'timeFormat' => 'hh:mm a',
    'weekStart' => 0,
    'thousandSeparator' => ',',
    'decimalMark' => '.',
    'exportDelimiter' => ';',
    'currencyList' => [
        0 => 'USD'
    ],
    'defaultCurrency' => 'USD',
    'baseCurrency' => 'USD',
    'currencyRates' => [
        
    ],
    'outboundEmailIsShared' => true,
    'outboundEmailFromName' => 'SiDo',
    'outboundEmailFromAddress' => 'no-reply@intellimedianetworks.com',
    'smtpServer' => 'smtp.gmail.com',
    'smtpPort' => '465',
    'smtpAuth' => true,
    'smtpSecurity' => 'SSL',
    'smtpUsername' => 'no-reply@intellimedianetworks.com',
    'smtpPassword' => 'no-reply@2017',
    'languageList' => [
        0 => 'en_GB',
        1 => 'en_US',
        2 => 'es_MX',
        3 => 'cs_CZ',
        4 => 'da_DK',
        5 => 'de_DE',
        6 => 'es_ES',
        7 => 'fr_FR',
        8 => 'id_ID',
        9 => 'it_IT',
        10 => 'nb_NO',
        11 => 'nl_NL',
        12 => 'tr_TR',
        13 => 'sr_RS',
        14 => 'ro_RO',
        15 => 'ru_RU',
        16 => 'pl_PL',
        17 => 'pt_BR',
        18 => 'uk_UA',
        19 => 'vi_VN',
        20 => 'zh_CN'
    ],
    'language' => 'en_US',
    'logger' => [
        'path' => 'data/logs/espo.log',
        'level' => 'WARNING',
        'rotation' => true,
        'maxFileNumber' => 30
    ],
    'authenticationMethod' => 'Espo',
    'globalSearchEntityList' => [
        0 => 'Account',
        1 => 'Contact',
        2 => 'Lead',
        3 => 'Opportunity'
    ],
    'tabList' => [
        0 => 'Account',
        1 => 'Contact',
        2 => 'Lead',
        3 => 'Email',
        4 => 'Calendar',
        5 => 'Meeting',
        6 => 'Task',
        7 => 'Document',
        8 => 'User',
        9 => 'Package'
    ],
    'quickCreateList' => [
        0 => 'Email',
        1 => 'Tax',
        2 => 'Tag'
    ],
    'exportDisabled' => false,
    'assignmentEmailNotifications' => false,
    'assignmentEmailNotificationsEntityList' => [
        0 => 'Lead',
        1 => 'Opportunity',
        2 => 'Task',
        3 => 'Case'
    ],
    'assignmentNotificationsEntityList' => [
        0 => 'Meeting',
        1 => 'Call',
        2 => 'Task',
        3 => 'Email'
    ],
    'portalStreamEmailNotifications' => true,
    'streamEmailNotificationsEntityList' => [
        0 => 'Case'
    ],
    'emailMessageMaxSize' => 10,
    'notificationsCheckInterval' => 10,
    'disabledCountQueryEntityList' => [
        0 => 'Email'
    ],
    'maxEmailAccountCount' => 2,
    'followCreatedEntities' => false,
    'b2cMode' => false,
    'restrictedMode' => false,
    'theme' => 'Espo',
    'massEmailMaxPerHourCount' => 100,
    'personalEmailMaxPortionSize' => 10,
    'inboundEmailMaxPortionSize' => 20,
    'authTokenLifetime' => 0,
    'authTokenMaxIdleTime' => 120,
    'userNameRegularExpression' => '[^a-z0-9\\-@_\\.\\s]',
    'addressFormat' => 1,
    'displayListViewRecordCount' => true,
    'dashboardLayout' => [
        0 => (object) [
            'name' => 'My Espo',
            'layout' => [
                0 => (object) [
                    'id' => 'default-tasks',
                    'name' => 'Tasks',
                    'x' => 0,
                    'y' => 0,
                    'width' => 4,
                    'height' => 2
                ],
                1 => (object) [
                    'id' => 'default-activities',
                    'name' => 'Activities',
                    'x' => 0,
                    'y' => 2,
                    'width' => 4,
                    'height' => 2
                ]
            ]
        ]
    ],
    'calendarEntityList' => [
        0 => 'Meeting',
        1 => 'Call',
        2 => 'Task'
    ],
    'activitiesEntityList' => [
        0 => 'Meeting',
        1 => 'Call'
    ],
    'historyEntityList' => [
        0 => 'Meeting',
        1 => 'Call',
        2 => 'Email'
    ],
    'lastViewedCount' => 20,
    'cleanupJobPeriod' => '1 month',
    'cleanupActionHistoryPeriod' => '15 days',
    'cleanupAuthTokenPeriod' => '1 month',
    'currencyFormat' => 1,
    'currencyDecimalPlaces' => NULL,
    'aclStrictMode' => false,
    'isInstalled' => true,
    'siteUrl' => 'http://localhost/SimplyIDo/Development',
    'passwordSalt' => '2b543e3c4ad51c55',
    'cryptKey' => '46eef58a84bc24103d50fc1075ed21d3',
    'userThemesDisabled' => false,
    'avatarsDisabled' => false,
    'dashletsOptions' => (object) [
        
    ]
];
?>