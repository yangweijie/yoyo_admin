<?php

use think\facade\Env;

return [

    /*
    |--------------------------------------------------------------------------
    | Host
    |--------------------------------------------------------------------------
    |
    | Dumps App Host address. By default: '127.0.0.1',
    | Uncomment the line below according to your environment.
    |
    */

    'host' => Env::get('laradumps.DS_APP_HOST', '127.0.0.1'),

    //'host' => 'host.docker.internal',    //Docker on Mac or Windows
    //'host' => '127.0.0.1',               //Homestead with the VirtualBox provider,
    //'host' => '10.211.55.2',             //Homestead with the Parallels provider,

    /*
    |--------------------------------------------------------------------------
    | Port
    |--------------------------------------------------------------------------
    |
    | Dumps App port. By default: 9191
    |
    */

    'port' => Env::get('laradumps.DS_APP_PORT', 9191),

    /*
    |--------------------------------------------------------------------------
    | Auto Invoke Desktop App
    |--------------------------------------------------------------------------
    |
    | Invoke LaraDumps Desktop App to gain focus when a new dump arrives.
    |
    */

    'auto_invoke_app' => Env::get('laradumps.DS_AUTO_INVOKE_APP', true),

    /*
    |--------------------------------------------------------------------------
    | SQL Query dump
    |--------------------------------------------------------------------------
    |
    | When `true`, it allows to dump database and send them to Desktop App.
    | Required for: ds()->queriesOn() method.
    |
    */

    'send_queries' => Env::get('laradumps.DS_SEND_QUERIES', false),

    /*
    |--------------------------------------------------------------------------
    | Log dump
    |--------------------------------------------------------------------------
    |
    | When `true`, it allows to dump Laravel logs and send them to Desktop App.
    | Required for logs dumping.
    |
    */

    'send_log_applications' => Env::get('laradumps.DS_SEND_LOGS', false),

    'auto_clear_on_page_reload' => Env::get('laradumps.DS_AUTO_CLEAR_ON_PAGE_RELOAD', false),

    /*
    |--------------------------------------------------------------------------
    | Preferred IDE
    |--------------------------------------------------------------------------
    |
    | Configure your preferred IDE to be used in Dumps App file links.
    |
    */

    'preferred_ide' => Env::get('laradumps.DS_PREFERRED_IDE', 'sublime'),

    /*
    |--------------------------------------------------------------------------
    | IDE Handlers
    |--------------------------------------------------------------------------
    |
    | Dumps already ships with pre-configured IDE protocol handlers.
    | You may adjust the handler or include custom ones, if needed.
    |
    */

    'ide_handlers' => [
        'atom' => [
            'handler'        => 'atom://core/open/file?filename=',
            'line_separator' => '&line=',
        ],
        'phpstorm' => [
            'handler'        => 'phpstorm://open?file=',
            'line_separator' => '&line=',
        ],
        'sublime' => [
            'handler'        => 'subl://open?url=file://',
            'line_separator' => '&line=',
        ],
        'vscode' => [
            'handler'        => 'vscode://file/',
            'line_separator' => ':',
        ],
        'vscode_remote' => [
            'handler'        => 'vscode://vscode-remote/',
            'line_separator' => ':',
            'local_path'     => 'wsl+' . Env::get('laradumps.DS_PREFERRED_WSL_DISTRO', 'Ubuntu20.04LTS'),
            'remote_path'    => Env::get('laradumps.DS_REMOTE_PATH', null),
            'work_dir'       => Env::get('laradumps.DS_WORKDIR', '/var/www/html'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore Routes
    |--------------------------------------------------------------------------
    |
    | Routes containing the words listed below will NOT be dumped with
    | ds()->routes() command.
    |
    */

    'ignore_route_contains' => [
        'debugbar',
        'ignition',
        'horizon',
        'livewire',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sleep
    |--------------------------------------------------------------------------
    |
    | You can specify an interval in 'seconds' between sending dumps
    | to the Desktop App.
    |
    */

    'sleep' => Env::get('laradumps.DS_SLEEP'),

    /*
    |--------------------------------------------------------------------------
    | Color in Screen
    |--------------------------------------------------------------------------
    |
    | If true, LaraDumps will separate colors into screens with the name of the
    | submitted color.
    |
    */

    'send_color_in_screen' => Env::get('laradumps.DS_SEND_COLOR_IN_SCREEN', false),

    /*
    |--------------------------------------------------------------------------
    | Color in Screen - Color Map
    |--------------------------------------------------------------------------
    |
    | Color map for "Color in Screen" feature.
    |
    */

    'screen_btn_colors_map' => [
        'default' => [
            'default' => 'btn-white',
        ],
        'danger' => [
            'default' => 'btn-danger',
        ],
        'info' => [
            'default' => 'btn-info',
        ],
        'success' => [
            'default' => 'btn-success',
        ],
        'warning' => [
            'default' => 'btn-warning',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Level Log Colors Map
    |--------------------------------------------------------------------------
    |
    | Definition of Tailwind CSS class for LaraDumps color tag.
    |
    */

    'level_log_colors_map' => [
        'error'     => Env::get('laradumps.DS_LOG_COLOR_ERROR', 'bg-red-600'),
        'critical'  => Env::get('laradumps.DS_LOG_COLOR_CRITICAL', 'bg-red-600'),
        'alert'     => Env::get('laradumps.DS_LOG_COLOR_ALERT', 'bg-red-600'),
        'emergency' => Env::get('laradumps.DS_LOG_COLOR_EMERGENCY', 'bg-red-600'),
        'warning'   => Env::get('laradumps.DS_LOG_COLOR_WARNING', 'bg-orange-300'),
        'notice'    => Env::get('laradumps.DS_LOG_COLOR_NOTICE', 'bg-blue-300'),
        'info'      => Env::get('laradumps.DS_LOG_COLOR_INFO', 'bg-gray-300'),
    ],

];