<?php
return [
    //Default controller and action if none is specified in URL
    'defaultPage' => [
        'controller' => 'site',
        'action' => 'index'
    ],
    //Role access configuration
    'roleAccess' => [
        'sessionName' => 'BaseAccessManagement', //name of the session to store role access object
        'timeout' => '15' //time for session to be alive (in seconds)
    ],
    //Default layout name, stored in "views/layouts"
    'defaultLayout' => 'main'
];