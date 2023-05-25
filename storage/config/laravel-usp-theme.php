<?php

/*$admin = [
    [
        'text' => '<i class="fas fa-atom"></i>  SubItem 1',
        'url' => 'subitem1',
    ],
    [
        'text' => 'SubItem 2',
        'url' => '/subitem2',
        'can' => 'admin',
    ],
    [
        'type' => 'divider',
    ],
    [
        'type' => 'header',
        'text' => 'Cabeçalho',
    ],
    [
        'text' => 'SubItem 3',
        'url' => 'subitem3',
    ],
];*/

$alunos = [
    [
        'text' => 'Cadastro Monografia',
        'url' => '/alunos/cadastroMonografia/',
        'can' => 'userAluno'
    ],
    /*[
        'text' => 'SubItem 2',
        'url' => 'subitem2',
        'can' => 'admin',
    ],*/
];

$orientadores = [
    [
        'text' => 'Listar Monografias',
        'url' => '/orientador/lista_monografia',
        'can' => 'userOrientador'
    ],
];

$graduacao = [
    [
        'text' => 'Listar Monografias',
        'url' => '/orientador/lista_monografia',
        'can' => 'userGraduacao'
    ],
    [
        'text' => 'Cadastro de Orientadores',
        'url' => '/orientador/',
        'can' => 'userGraduacao'
    ],
    [
        'text' => 'Cadastro de Unitermos',
        'url' => '/graduacao/unitermos',
        'can' => 'userGraduacao'
    ],
    [
        'text' => 'Cadastro - Áreas Temáticas',
        'url' => '/graduacao/area_tematica/',
        'can' => 'userGraduacao'
    ],
    [
        'text' => 'Cadastro de Comissão',
        'url' => 'graduacao/comissao',
        'can' => 'userGraduacao'
    ],
    [
        'text' => 'Cadastro de Banca',
        'url' => 'graduacao/banca',
        'can' => 'userGraduacao'
    ],
    [
        'text' => 'Emissão de Declarações/ Relatórios',
        'url' => 'graduacao/declaracao',
        'can' => 'userGraduacao'
    ],
    [
        'text' => 'Área Administrativa',
        'url' => 'graduacao/administracao',
        'can' => 'userGraduacao'
    ],
];

$menu = [
    [
        'text' => '<i class="fas fa-home"></i> Home',
        'url' => 'home',
    ],
    /*[
        # este item de menu será substituido no momento da renderização
        'key' => 'menu_dinamico',
    ],*/
    [
        'text' => 'Alunos',
        'submenu' => $alunos,
        'can' => 'userAluno',
    ],
    [
        'text' => 'Orientadores',
        'submenu' => $orientadores,
        //'url' => 'gerente',
        'can' => 'userOrientador',
    ],
    [
        'text' => 'Graduação',
        'submenu' => $graduacao,
        'can' => 'userGraduacao',
    ],
    /*[
        'text' => 'Está logado',
        'url' => config('app.url') . '/logado', // com caminho absoluto
        'can' => 'user',
    ],
    
    [
        'text' => 'Menu admin',
        'submenu' => $admin,
        'can' => 'admin',
    ],*/
];

$right_menu = [
    [
        // menu utilizado para views da biblioteca senhaunica-socialite.
        'key' => 'senhaunica-socialite',
    ],
    [
        'key' => 'laravel-tools',
    ],
    /*[
        'text' => '<i class="fas fa-cog"></i>',
        'title' => 'Configurações',
        'target' => '_blank',
        'url' => config('app.url') . '/item1',
        'align' => 'right',
    ],*/
];

return [
    # valor default para a tag title, dentro da section title.
    # valor pode ser substituido pela aplicação.
    'title' => config('app.name'),

    # USP_THEME_SKIN deve ser colocado no .env da aplicação
    'skin' => env('USP_THEME_SKIN', 'eeusp'),

    # chave da sessão. Troque em caso de colisão com outra variável de sessão.
    'session_key' => 'laravel-usp-theme',

    # usado na tag base, permite usar caminhos relativos nos menus e demais elementos html
    # na versão 1 era dashboard_url
    'app_url' => config('app.url'),

    # login e logout
    'logout_method' => 'POST',
    'logout_url' => 'logout',
    'login_url' => 'login',

    # menus
    'menu' => $menu,
    'right_menu' => $right_menu,

    # mensagens flash - https://uspdev.github.io/laravel#31-mensagens-flash
    'mensagensFlash' => false,

    # container ou container-fluid
    'container' => 'container-fluid',

];
