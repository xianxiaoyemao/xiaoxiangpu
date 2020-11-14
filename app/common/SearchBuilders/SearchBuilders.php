<?php

namespace app\common\SearchBuilders;

//use app\admin\model\Corporation;
use app\BaseController;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\ElasticsearchException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use think\db\Query;
use think\facade\Cache;

class SearchBuilders{
    // 初始化
    public function index(){
        // 只能创建一次
        $this->delete_index();

        $this->create_index(); //1.创建索引

        $this->create_mappings(); //2.创建文档模板
        echo '<pre>';
        print_r("INIT OK");
        exit();
    }

    public function put_settings($index){
        $params1 =
            [
                'index' => $index,
                'body' => [
                    'settings' => [
                        'blocks' =>
                            [
                                'read_only_allow_delete' => 'false'
                            ]
                    ],
                ]
            ];
        app('es')->indices()->putSettings($params1);
        echo 'SUCCESS';
    }

    // 创建索引
    public function create_index($index){ // 只能创建一次
        $params = [
            'index' => $index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 3,
                    'number_of_replicas' => 2,
                    'blocks' =>
                        [
                            'read_only_allow_delete' => 'false'
                        ],
                    /*'transient' =>
                        [
                            'cluster' =>
                                [
                                    'routing' =>
                                        [
                                            'allocation' =>
                                                [
                                                    'disk' =>
                                                        [
                                                            'threshold_enabled' => 'true',
                                                            'watermark' =>
                                                                [
                                                                    'flood_stage' => '99%'
                                                                ]
                                                        ]
                                                ]
                                        ]
                                ]
                        ]*/
                ],
            ]
        ];


        try {
            app('es')->indices()->create($params);

        } catch (BadRequest400Exception $e) {
            $msg = $e->getMessage();
            $msg = json_decode($msg, true);
            return $msg;
        }
    }


    // 删除索引
    public function delete_index($index){
        $params = ['index' => $index];
        $index =  app('es')->indices()->get($params);
        if ($index) {
            return  app('es')->indices()->delete($params);
        }
    }

    // 创建文档模板
    public function create_mappings($index){
        /*--------------------允许type的写法 老版本 已去除--------------------------*/
        /*--------------------不允许type的写法--------------------------*/
        // Set the index and type
        $params = [
            'index' => $index,
            'body' => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => [
                    'id' => [
                        'type' => 'integer',
                    ],
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'ik_smart'
                        // 'analyzer' => 'keyword'
                    ],

                    /*-------------------------------------*/

                    /*'profile' => [
                        'type' => 'text',
                        'analyzer' => 'ik_max_word'
                    ],
                    'age' => [
                        'type' => 'integer',
                    ],*/
                ]
            ]
        ];

        app('es')->indices()->putMapping($params);

        /*       echo '<pre>';
               print_r('success');
               exit();*/
    }

    // 查看映射
    public function get_mapping($index)
    {
        $params = [
            'index' => $index,
        ];
        $re = app('es')->indices()->getMapping($params);
        echo '<pre>';
        print_r($re);
        exit();

    }

    // 添加一个文档（记录）
    public function add_doc($index,$id, $doc){
        $params = [
            'index' => $index,
            //  'type' => $this->type,
            'id' => $id,
            'body' => $doc
        ];
        return app('es')->index($params);
    }

    // 判断文档（记录）
    public function exists_doc($index,$type,$id = 1)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'id' => $id
        ];
        return app('es')->exists($params);
    }

    // 获取一条文档（记录）
    public function get_doc($index,$id = 1){
        $params =
            [
                'index' => $index,
                // 'type' => $this->type,
                'id' => $id
            ];
        try {
            $re = app('es')->get($params);
        } catch (Missing404Exception $e) {
            echo '<pre>';
            print_r('未找到对应数据');
            exit();

        }
        echo '<pre>';
        print_r($re);
        exit();
    }

    // 查询文档 (分页，排序，权重，过滤)
    public function search_doc($index,$keywords = "", $from = 0, $size = 10, $order = ['id' => ['order' => 'desc']]){
        $keywords_arr = array_filter(explode(" ", $keywords));
//        dump($keywords_arr);die;
        $query = '';
        $number = count($keywords_arr);
        if($number > 10){
            return "ERROR";
        }
        if ($number > 1) {
            $arr = [];
            foreach ($keywords_arr as $ka){
                $arr[] = $ka;
            }
            $mathc_phrase = [];
            switch ($number){
                case 2:
                    $mathc_phrase =
                        [
                            'name'=>$arr[0],
                            'name'=>$arr[1]
                        ];
                    break;
                case 3:
                    $mathc_phrase =
                        [
                            'name'=>$arr[0],
                            'name'=>$arr[1],
                            'name'=>$arr[2]
                        ];
                    break;
                case 4:
                    $mathc_phrase =
                        [
                            'name'=>$arr[0],
                            'name'=>$arr[1],
                            'name'=>$arr[2],
                            'name'=>$arr[3],
                        ];
                    break;
                case 5:
                    $mathc_phrase =
                        [
                            'name'=>$arr[0],
                            'name'=>$arr[1],
                            'name'=>$arr[2],
                            'name'=>$arr[3],
                            'name'=>$arr[4],
                        ];
                    break;
                case 6:
                    $mathc_phrase =
                        [
                            'name'=>$arr[0],
                            'name'=>$arr[1],
                            'name'=>$arr[2],
                            'name'=>$arr[3],
                            'name'=>$arr[4],
                            'name'=>$arr[5],
                        ];
                    break;
                case 7:
                    $mathc_phrase =
                        [
                            'name'=>$arr[0],
                            'name'=>$arr[1],
                            'name'=>$arr[2],
                            'name'=>$arr[3],
                            'name'=>$arr[4],
                            'name'=>$arr[5],
                            'name'=>$arr[6],
                        ];
                    break;
                case 8:
                    $mathc_phrase =
                        [
                            'name'=>$arr[0],
                            'name'=>$arr[1],
                            'name'=>$arr[2],
                            'name'=>$arr[3],
                            'name'=>$arr[4],
                            'name'=>$arr[5],
                            'name'=>$arr[6],
                            'name'=>$arr[7],
                        ];
                    break;
                case 9:
                    $mathc_phrase =
                        [
                            'name'=>$arr[0],
                            'name'=>$arr[1],
                            'name'=>$arr[2],
                            'name'=>$arr[3],
                            'name'=>$arr[4],
                            'name'=>$arr[5],
                            'name'=>$arr[6],
                            'name'=>$arr[7],
                            'name'=>$arr[8],
                        ];
                    break;
                case 10:
                    $mathc_phrase =
                        [
                            'name'=>$arr[0],
                            'name'=>$arr[1],
                            'name'=>$arr[2],
                            'name'=>$arr[3],
                            'name'=>$arr[4],
                            'name'=>$arr[5],
                            'name'=>$arr[6],
                            'name'=>$arr[7],
                            'name'=>$arr[8],
                            'name'=>$arr[9],
                        ];
                    break;

            }
            $query_func = [
                'bool' =>
                    [
                        'must' =>
                            [
                                'match_phrase'=>$mathc_phrase,
                                /*  'match_phrase'=>
                                 [
                                     'name'=>'研究会',
                                 ]*/
                            ]
                    ]


            ];
        } else {
            // $query = $keywords;
            $query_func = [
                /*-----------------------------name 单字段单匹配---------------------------*/
                'bool' =>
                    [
                        'should' =>
                            [
                                'match_phrase' =>
                                    [
                                        'title' => $keywords
                                    ]
                            ]
                    ]

            ];
        }

        if ($keywords) {
            $params = [
                'index' => $index,
                //  'type' => $this->type,
                'body' => [
                    'query' => $query_func,
                    'sort' =>
                        [$order],
                    'from' => $from,
                    'size' => $size
                ]
            ];
        } else {
            $params = [
                'index' => $index,
                //  'type' => $this->type,
                'body' => [
                    /* 'query' => [
                         'match_all'=>[]
                     ],*/
                    'sort' => [$order]
                    , 'from' => $from, 'size' => $size
                ]
            ];
        }
//        dump($params);die;

        try {
            $re = app('es')->search($params);
        } catch (\Exception $e) {
            echo '<pre>';
            print_r($e->getMessage());
            exit();
        }
        return $re;
    }


    // 删除一条文档（）
    public function delete_doc($index,$id = 1){
        $params = [
            'index' => $index,
            //'type' => $this->type,
            'id' => $id
        ];
        return app('es')->delete($params);
    }
    // 更新一条文档（）
    public function update_doc($index,$id = 1){
        // 可以灵活添加新字段,最好不要乱添加
        $params = [
            'index' => $index,
            'id' => $id,
            'body' => [
                'doc' => [
                    'name' => '大王'
                ]
            ]
        ];
        return app('es')->update($params);
    }
    /**
     * 批量插入数据到索引
     */
    public function insertCorporation(){
        $corporations = Corporation::select();
        foreach ($corporations as $corporation) {
            $corporation = $corporation->toArray();
            $this->add_doc($corporation['id'], $corporation);
        }
        echo '<pre>';
        print_r('完成了');
        exit();
    }

}
