<?php

/**
 * Class Config
 *
 * 执行Sample示例所需要的配置，用户在这里配置好Endpoint，AccessId， AccessKey和Sample示例操作的
 * bucket后，便可以直接运行RunAll.php, 运行所有的samples
 */
final class Config
{
    const OSS_ACCESS_ID = 'xEyV22oj1nK87lSI';
    const OSS_ACCESS_KEY = 'XWAOJOKyZcIg6TF4pBAj0BESti4EoC';
    const OSS_ENDPOINT = 'zyq-1.oss-cn-hongkong.aliyuncs.com'; // 外网访问地址
    const OSS_TEST_BUCKET = 'Object';
}
