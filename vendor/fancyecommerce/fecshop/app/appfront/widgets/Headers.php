<?php

namespace fecshop\app\appfront\widgets;

use fecshop\interfaces\block\BlockCache;
use Yii;

class Headers implements BlockCache
{
    /**
     * 获取渲染需要的数据
     * @return [type] [description]
     */
    public function getLastData()
    {
        // 获取当前的url
        $currentUrl = Yii::$service->url->getCurrentUrl();
        // 获取退出的url
        $logoutUrl = Yii::$service->url->getUrl('customer/account/logout', ['rt'=>base64_encode($currentUrl)]);

        //$currentLang =
        //$currency = Yii::$service->page->currency->getCurrentCurrency();
        return [
            // 退出
            'logoutUrl'            => $logoutUrl,
            // 首页
            'homeUrl'            => Yii::$service->url->homeUrl(),
            'currentBaseUrl'    => Yii::$service->url->getCurrentBaseUrl(),
            'currentStore'        => Yii::$service->store->currentStore,
            'currentStoreLang'    => Yii::$service->store->currentLangName,
            'stores'            => Yii::$service->store->getStoresLang(),
            'currency'            => Yii::$service->page->currency->getCurrencyInfo(),
            'currencys'            => Yii::$service->page->currency->getCurrencys(),
        ];
    }

    public function getCacheKey()
    {
        $lang = Yii::$service->store->currentLangCode;
        $currency = Yii::$service->page->currency->getCurrentCurrency();
        $appName        = Yii::$service->helper->getAppName();
        $cacheKeyName   = 'footer';
        $currentStore   = Yii::$service->store->currentStore;
        return self::BLOCK_CACHE_PREFIX.'_'.$currentStore.'_'.$lang.'_'.$currency.'_'.$appName.'_'.$cacheKeyName;
    }
}
