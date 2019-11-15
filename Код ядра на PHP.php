<?php

/*эмулируем входящие данные*/
$_GET["x"] = rand(1,9);
$_GET["y"] = 4;

/*1 компонент проекта*/
$GLOBALS['Рефлексы'] = [
    '1' => [
        'Ориентир'   => 'Ядро',
        'Условия'    => 'false',
        'Расчёты'    => 'Не более 5 кб. памяти',
        'Места'      => ['1'],
        'Вложение'   => [
            '1.1' => [
                'Ориентир'   => 'Пример решения',
                'Условия'    => 'isset($_GET["x"]) and isset($_GET["y"])',
                'Расчёты'    => 'Использовать входящие данные "x" и "y" в любой формуле и вывести результат на экран',
                'Места'      => ['1.1','1.2'],
                'Вложение'   => false
            ]
        ]
    ]
];

/*2 компонент проекта*/
$GLOBALS['Места']    = [
    '1' => [
        'Смысл'    => 'Память ядра',
        'Роль'     => 'Способность',
        'Связи'    => ['1'],
        'Вложение' => [
            '1.1' => [
                'Смысл'    => 'Вычисление формулы x + y = z',
                'Роль'     => ['Способность'],
                'Связи'    => ['2'],
                'Вложение' => [
                    '1.1.1' => [
                        'Смысл'    => 'Значение x',
                        'Роль'     => ['Информация'],
                        'Связи'    => false,
                        'Вложение' => isset($_GET['x']) ? $_GET['x'] : null
                    ],
                    '1.1.2' => [
                        'Смысл'    => 'Значение y',
                        'Роль'     => ['Информация'],
                        'Связи'    => false,
                        'Вложение' => isset($_GET['y']) ? $_GET['y'] : null
                    ],
                    '1.1.3' => [
                        'Смысл'    => 'Значение z',
                        'Роль'     => ['Информация'],
                        'Связи'    => false,
                        'Вложение' => null
                    ],
                ]
            ],
            '1.2' => [
                'Смысл'    => 'Вывод z на экран',
                'Роль'     => ['Способность'],
                'Связи'    => ['3'],
                'Вложение' => false
            ],
        ]
    ]
];

/*3 компонент проекта*/
$GLOBALS['Связи']    = [
    '1'  => [
        'Возможности'         => null,
        'Приобретение'        => null,
        'Пример возможностей' => null,
        'Пример приобретения' => null,
        'Реакция'             => ['1'],
    ],
    '2'  => [
        'Возможности'         => ['1.1.1', '1.1.2'],
        'Приобретение'        => ['1.1.3'],
        'Пример возможностей' => [
            '1.1.1' => 1,
            '1.1.2' => 3,
        ],
        'Пример приобретения' => [
            '1.1.3' => 4,
        ],
        'Реакция'             => ['2'],
    ],
    '3'  => [
        'Возможности'         => ['1.1.3'],
        'Приобретение'        => null,
        'Пример возможностей' => [
            '1.1.3' => 4,
        ],
        'Пример приобретения' => null,
        'Реакция'             => ['3'],
    ],
];

/*4 компонент проекта*/
$GLOBALS['Реакции']  = [
    '1'  => [
        'Способность'  => function(){

            /*алгоритм выдачи ссылки на место по id*/
            $function_link_place_by_id = function &($place_id){

                /*создаём ссылку на корень места*/
                $link = &$GLOBALS['Места'];

                /*создаём уровень вложенности*/
                $place_id_explode = explode('.', $place_id);

                /*переходим по вложенностям*/
                $last_place_id = '';
                foreach($place_id_explode as $key=>$place_id){
                    if($key == 0){
                        /*определяем название уровня*/
                        $last_place_id = $place_id;
                    }
                    else{
                        /*определяем название уровня*/
                        $last_place_id = $last_place_id . '.' . $place_id;
                        /*переходим во вложенность*/
                        $link = &$link['Вложение'];
                    }
                    /*переходим во вложенность*/
                    $link = &$link[$last_place_id];
                }

                /*результат*/
                return ($link);
            };

            /*алгоритм активации допустимых рефлексов во вложении заданного рефлекса*/
            $function_activation_permissible_goals = function($function_activation_permissible_goals, $goals_row) use($function_link_place_by_id){
                /*на случай когда вложений у рефлекса нет*/
                if(!$goals_row){
                    return false;
                }
                /*обходим все рефлексы*/
                foreach ($goals_row as $goal_id => $goal_row){
                    /*если условия рефлекса выполнимы*/
                    if(eval('return ' . $goal_row['Условия'] . ';') == true){
                        /*если есть места у рефлекса*/
                        if($goal_row['Места']){
                            /*обходим все места рефлекса*/
                            foreach($goal_row['Места'] as $place_id){
                                /*если есть связи у места*/
                                if(($communications = $function_link_place_by_id($place_id)['Связи'])){
                                    /*обходим все связи места*/
                                    foreach($communications as $communication_id){
                                        /*если есть реакции у связи*/
                                        if($GLOBALS['Связи'][$communication_id]['Реакция']){
                                            /*обходим реакции связи*/
                                            foreach($GLOBALS['Связи'][$communication_id]['Реакция'] as $reaction_id){
                                                /*обрабатываем возможности*/
                                                $places_data_in = [];
                                                if($GLOBALS['Связи'][$communication_id]['Возможности'] != null){
                                                    foreach($GLOBALS['Связи'][$communication_id]['Возможности'] as $place_id){
                                                        $places_data_in[$place_id] = &$function_link_place_by_id($place_id)['Вложение'];
                                                    }
                                                }
                                                /*выполняем реакцию у связи*/
                                                $results = $GLOBALS['Реакции'][$reaction_id]['Способность']($places_data_in);
                                                /*обрабатываем приобретения*/
                                                if($results!=null and is_array($results)){
                                                    /*сохраняем результат по местам*/
                                                    foreach($results as $place_id=>$place_data){
                                                        /*создаём ссылку на место*/
                                                        $link_result_place_by_id = &$function_link_place_by_id($place_id);
                                                        /*сохраняем значение*/
                                                        $link_result_place_by_id['Вложение'] = $place_data;
                                                        /*убираем ссылку*/
                                                        unset($link_result_place_by_id);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        /*если у рефлекса есть ещё вложение*/
                        if($goal_row['Вложение']){
                            /*обходим рефлексы внутри вложений рефлекса*/
                            $function_activation_permissible_goals($function_activation_permissible_goals, $goal_row['Вложение']);
                        }
                    }
                }
                /*результат*/
                return true;
            };

            /*активируем все допустимые рефлексы*/
            $function_activation_permissible_goals($function_activation_permissible_goals, $GLOBALS['Рефлексы'][1]['Вложение']);

            /*результат*/
            return null;

        },
    ],
    '2'  => [
        'Способность'  => function($opportunities = []){

            $z = $opportunities['1.1.1'] + $opportunities['1.1.2'];

            /*результат*/
            return [
                '1.1.3' => $z,
            ];
        },
    ],
    '3'  => [
        'Способность'  => function($opportunities = []){

            echo 'z = ' . $opportunities['1.1.3'];

            /*результат*/
            return null;
        },
    ],
];

/*активируем ядро*/
$GLOBALS['Реакции'][1]['Способность']();

?>
