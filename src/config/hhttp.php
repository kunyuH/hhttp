<?php

return [

    #--------------------api与依赖服务接口请求日志【api hhttp sql】--------------------------
    'HP_DATABASE_DEFAULT'=>env('HP_DATABASE_DEFAULT', null),    //数据连接 不填写则默认mysql


    # api日志开关 是否记录
    'HP_API_LOG'=>env('HP_API_LOG', true),
    # 默认api日志中user_id 提取 request 对象内的属性
    'HP_API_LOG_USER_FILED'=>env('HP_API_LOG_USER_FILED', 'member_id'),
    # 允许不记录日志的路由
    'HP_API_LOG_NOT_ROUTE'=>env('HP_API_LOG_NOT_ROUTE',''),


    # hhttp日志开关 是否记录
    'HP_HTTP_LOG'=>env('HP_HTTP_LOG', true),
    # hhttp日志开关 跑命令时的hhttp日志是否记录
    'HP_COMMAND_HTTP_LOG'=>env('HP_COMMAND_HTTP_LOG', false),


    # sql日志开关 是否记录
    'HP_SQL_LOG'=>env('HP_SQL_LOG', true),
    # sql日志开关 跑命令时的sql是否记录
    'HP_SQL_COMMAND_LOG'=>env('HP_SQL_COMMAND_LOG', false),

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

];
