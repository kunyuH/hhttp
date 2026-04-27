<?php

return [

    'HP_ENABLE'=>env('HP_ENABLE', false), //是否启用hhttp.io  默认不启用

    #--------------------hoo_hm--------------------------
    'IS_LOGIN'=>env('IS_LOGIN', true),      //是否需要账密登录  默认需要
    'HP_NAME'=>env('HP_NAME'),
    'HP_PASSWORD'=>env('HP_PASSWORD'),
    'HP_LOGIN_RETRY'=>env('HP_LOGIN_RETRY',5), //登录重试次数 默认5次

    'SERVICE_NAME'=>env('SERVICE_NAME', ''),    //服务前缀
    #---------------------------------------------------

    #--------------------动态从逻辑块中加载配置-------------------
    'HP_LOAD_CONFIG_ENABLE'=>env('HP_LOAD_CONFIG_ENABLE', false),            //是否启用动态从逻辑块中加载配置
    'HP_LOGICAL_BLOCK_OBJECT_ID'=>env('HP_LOGICAL_BLOCK_OBJECT_ID', 'c9cf7d34-8294-b348-0796-94718ae48f0d'),  //逻辑块对象id
    #---------------------------------------------------

    #--------------------api与依赖服务接口请求日志【api hhttp sql】--------------------------
    'HP_DATABASE_DEFAULT'=>env('HP_DATABASE_DEFAULT', null),    //数据连接 不填写则默认mysql

    # api日志开关 是否记录
    'HP_API_LOG'=>env('HP_API_LOG', true),
    # 默认api日志中user_id 提取 request 对象内的属性
    'HP_API_LOG_USER_FILED'=>env('HP_API_LOG_USER_FILED', 'member_id'),
    # 允许不记录日志的路由
    'HP_API_LOG_NOT_ROUTE'=>env('HP_API_LOG_NOT_ROUTE','hm/*,hmr/*,__clockwork/*,clockwork/*'),

    # hhttp日志开关 是否记录
    'HP_HTTP_LOG'=>env('HP_HTTP_LOG', true),
    # hhttp日志开关 跑命令时的hhttp日志是否记录
    'HP_COMMAND_HTTP_LOG'=>env('HP_COMMAND_HTTP_LOG', false),

    # sql日志开关 是否记录
    'HP_SQL_LOG'=>env('HP_SQL_LOG', true),
    # sql日志开关 跑命令时的sql是否记录
    'HP_SQL_COMMAND_LOG'=>env('HP_SQL_COMMAND_LOG', false),
    # 允许不记录日志的路由
    'HP_SQL_LOG_NOT_ROUTE'=>env('HP_SQL_LOG_NOT_ROUTE', 'hm/*,hmr/*,__clockwork/*'),

    # 长度限制
    'HM_API_HTTP_LOG_LENGTH'=>env('HM_API_HTTP_LOG_LENGTH', 5000),

    # 日志清理设置
    # api日志清理多久之前的日志 默认 60天前的
    'HM_API_LOG_CLEAN'=>env('HM_API_LOG_CLEAN', 60),
    # hhttp日志清理多久之前的日志 默认 60天前的
    'HM_HPPT_LOG_CLEAN'=>env('HM_HPPT_LOG_CLEAN', 60),
    # sql日志清理多久之前的日志 默认 60天前的
    'HM_SQL_LOG_CLEAN'=>env('HM_SQL_LOG_CLEAN', 60),
    #-------------------------------------------------------

    #-----------------------------gateway代理配置-----------------------------
    # 最先执行的中间件 可在中间件中执行代理服务的数据清洗
    'GATE_FIRST_MID'                        =>explode(',', env('GATE_FIRST_MID')),
    # 中间件 默认 authLogin
    'GATE_DEFAULT_MID'                      =>explode(',', env('GATE_DEFAULT_MID', 'authLogin')),
    # 最后执行的中间件 可在中间件中执行代理服务的数据清洗
    'GATE_LAST_MID'                         =>explode(',', env('GATE_LAST_MID')),
    # input 内需忽略传递的参数 默认'member_info,member_id,account_id'
    'GATE_IGNORE_INPUT'                     =>explode(',', env('GATE_IGNORE_INPUT', 'member_info,member_id,account_id')),
    # header 中允许可代理过去的参数 默认为空
    'GATE_HEADER_ARG'                       =>explode(',', env('GATE_HEADER_ARG')),
    /**
     * 代理模式 默认 loose
     * strict 严格模式【线上需使用此配置】
     *      代理host必须从值域中选取
     *      gateway-host值域中可提取的参数 必须属于配置的可使用值域      //TODO 待实现
     * loose 宽松模式
     *      代理host可从值域中选取 也可直接填写
     *      gateway-host值域中可提取的参数 可自由使用                 //TODO 待实现
     */
    'GATE_MODE'                             => env('GATE_MODE', 'loose'),
    # 值域为config时 默认从apis的配置内提取
    'GATE_MODE_DEFAULT_STRICT_CONFIG'       => env('GATE_MODE_DEFAULT_STRICT_CONFIG', 'apis'),
    #-------------------------------------------------------

];
