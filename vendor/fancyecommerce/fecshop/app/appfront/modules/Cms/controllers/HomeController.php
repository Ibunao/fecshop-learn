<?php

namespace fecshop\app\appfront\modules\Cms\controllers;

use fecshop\app\appfront\modules\AppfrontController;
use Yii;

class HomeController extends AppfrontController
{
    public function init()
    {
        parent::init();
    }

    public function behaviors()
    {
        // 判断是否满足h5跳转到vueapp的条件
        if (Yii::$service->store->isAppServerMobile()) {
            $urlPath = '';
            // h5跳转到vueapp
            Yii::$service->store->redirectAppServerMobile($urlPath);
        }
        $behaviors = parent::behaviors();
        $cacheName = 'home';
        // 判断是否允许缓存
        if (Yii::$service->cache->isEnable($cacheName)) {
            // 配置的缓存时间
            $timeout = Yii::$service->cache->timeout($cacheName);
            $disableUrlParam = Yii::$service->cache->disableUrlParam($cacheName);
            $get = Yii::$app->request->get();
            // 存在无缓存参数，则关闭缓存
            if (isset($get[$disableUrlParam])) {
                $behaviors[] =  [
                    'enabled' => false,
                    'class' => 'yii\filters\PageCache',
                    'only' => ['index'],
                ];
            }
            // 当前域名
            $store = Yii::$service->store->currentStore;
            // 当前货币
            $currency = Yii::$service->page->currency->getCurrentCurrency();
            // yii页面缓存过滤器
            $behaviors[] =  [
                'enabled' => true,
                'class' => 'yii\filters\PageCache',
                'only' => ['index'],
                'duration' => $timeout,
                'variations' => [
                    $store, $currency,// 用来区分缓存
                ],
            ];
        }
        return $behaviors;
    }
    
    /**
     * 首页
     * @return [type] [description]
     */
    public function actionIndex()
    {
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }

    /**
     * 更改货币类型
     * @return [type] [description]
     */
    public function actionChangecurrency()
    {
        $currency = \fec\helpers\CRequest::param('currency');
        Yii::$service->page->currency->setCurrentCurrency($currency);
    }
}
