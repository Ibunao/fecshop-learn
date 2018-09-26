<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_COMPILE_WARNING ); //除去 E_NOTICE E_COMPILE_WARNING 之外的所有错误信息, 不显示E_NOTICE E_COMPILE_WARNING 错误
//ini_set('session.cookie_domain', '.fancyecommerce.com'); //初始化域名，共享session
$http = ($_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
// 获取入口文件目录，看web/fr/index
$homeUrl = $http.'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
// echo $homeUrl;exit;
/**
 * fecshop 使用合并配置（config）数组进行加速，true 代表打开。
 * 打开配置加速开关前，您需要执行 http://domain/index-merge-config.php 进行生成单文件配置数组。
 * 注意：打开后，当您修改了配置，都需要访问一次上面的链接，重新生成单文件配置数组，否则修改的配置不会生效
 * 建议：本地开发环境关闭，开发环境如果访问量不大，关闭也行，如果访问量大，建议打开
 * 
 */
$use_merge_config_file = false; 
 
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
// 使用自己扩展的Yii
require(__DIR__ . '/../../vendor/fancyecommerce/fecshop/yii/Yii.php');
// 设置路径别名
require(__DIR__ . '/../../common/config/bootstrap.php');

require(__DIR__ . '/../config/bootstrap.php');
// 使用生成的合并的配置文件
if($use_merge_config_file){
	$config = require('../merge_config.php');
}else{
	$config = yii\helpers\ArrayHelper::merge(
		require(__DIR__ . '/../../common/config/main.php'),
		require(__DIR__ . '/../../common/config/main-local.php'),
		require(__DIR__ . '/../config/main.php'),
		require(__DIR__ . '/../config/main-local.php'),
        
		# fecshop 公用配置
		require(__DIR__ . '/../../vendor/fancyecommerce/fecshop/config/fecshop.php'),
		# fecshop 入口配置
		require(__DIR__ . '/../../vendor/fancyecommerce/fecshop/app/appfront/config/appfront.php'),
		
		# thrid part confing
        # 第三方 公用配置
        require(__DIR__ . '/../../common/config/fecshop_third.php'),
        # 第三方 入口配置
        require(__DIR__ . '/../config/fecshop_third.php'),
        
		# 本地 公用配置
		require(__DIR__ . '/../../common/config/fecshop_local.php'),
		# 本地 入口配置
		require(__DIR__ . '/../config/fecshop_local.php')
		
	);
}
// 自己设置，不用yii获取的homeUrl
$config['homeUrl'] = $homeUrl;

/**
 * 配置的classMap，自动加载会快一点
 * yii class Map Custom 
 */ 
$yiiClassMap = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../config/YiiClassMap.php'),
    require(__DIR__ . '/../../common/config/YiiClassMap.php')
);
if(is_array($yiiClassMap) && !empty($yiiClassMap)){
	foreach($yiiClassMap as $namespace => $filePath){
		Yii::$classMap[$namespace] = $filePath;
	}
}

/**
 * Yii 重写block controller model等
 * 也就是说：除了compoent 和services，其他的用RewriteMap的方式来实现重写
 * 重写的类可以集成被重写的类
 */ 
/**
 * 为了后期更改不动原代码
 * @var [type]
 */
$yiiRewriteMap = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../config/YiiRewriteMap.php'),
    require(__DIR__ . '/../../common/config/YiiRewriteMap.php')
);
if(is_array($yiiRewriteMap) && !empty($yiiRewriteMap)){
	Yii::$rewriteMap = $yiiRewriteMap;
}

/**
 * 添加fecshop的服务 ，Yii::$service  ,  将services的配置添加到这个对象。
 * 使用方法：Yii::$service->cms->article;
 * 上面的例子就是获取cms服务的子服务article。
 */
new fecshop\services\Application($config['services']);
unset($config['services']);

$application = new yii\web\Application($config);
$application->run();
