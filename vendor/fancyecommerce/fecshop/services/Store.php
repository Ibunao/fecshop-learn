<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fecshop\services;

use Yii;
use yii\base\InvalidValueException;

/**
 * store service
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Store extends Service
{
    /**
     * 配置数组
     * init by config file.
     * all stores config . include : domain,language,theme,themePackage.
     */
    public $stores;
    /**
     * 保存的是当前的store配置
     * @var [type]
     */
    public $store;
    /**
     * 当前语言
     * current store language,for example: en_US,fr_FR.
     */
    public $currentLang;
    /**
     * 语言全名
     * current store language name.
     */
    public $currentLangName;
    /**
     * current store theme package.
     */
    //public $currentThemePackage = 'default';
    /**
     * current store theme.
     */
    //public $currentTheme = 'default';
    /**
     * 当前store的key，也就是当前的store.
     * 如 appfront.fecshoptest.com
     */
    public $currentStore;
    /**
     * current language code example : fr  es cn ru.
     */
    public $currentLangCode;

    public $thirdLogin;
    //public $https;
    
    public $serverLangs;
    
    public $apiAppNameArr = ['appserver','appapi'];
    // 是否是api入口
    public function isApiStore(){
        $appName = Yii::$app->params['appName'];
        if($appName && in_array($appName,$this->apiAppNameArr)){
            return true;
        }else{
            return false;
        }
    }
    // 得到当前入口的名字
    public function getCurrentAppName(){
        return Yii::$app->params['appName'];
    }
    
    /**
     *	Bootstrap:init website,  class property $currentLang ,$currentTheme and $currentStore.
     *  if you not config this ,default class property will be set.
     *  if current store_code is not config , InvalidValueException will be throw.
     *	class property $currentStore will be set value $store_code.
     */
    /**
     * 初始化
     * @param  [type] $app 
     * @return [type]      [description]
     */
    protected function actionBootstrap($app)
    {
        // 分割协议http 和 域名部分
        $host = explode('//', $app->getHomeUrl());
        $stores = $this->stores;
        $init_compelte = 0;
        if (is_array($stores) && !empty($stores)) {
            foreach ($stores as $store_code => $store) {
                // 根据域名获取store中对应域名的配置
                if ($host[1] == $store_code) {
                    // 检查是否从pc跳转到h5
                    $this->html5DevideCheckAndRedirect($store_code, $store);
                    Yii::$service->store->currentStore = $store_code;
                    $this->store = $store;
                    // 设置语言
                    if (isset($store['language']) && !empty($store['language'])) {
                        Yii::$service->store->currentLang = $store['language'];
                        // 获取语言简码
                        Yii::$service->store->currentLangCode = Yii::$service->fecshoplang->getLangCodeByLanguage($store['language']);
                        // 语言全名
                        Yii::$service->store->currentLangName = $store['languageName'];
                        // 设置当前语言 Yii::$app->language
                        Yii::$service->page->translate->setLanguage($store['language']);
                    }
                    // 作者已经注释掉currentTheme属性，这段代码也无用了
                    // if (isset($store['theme']) && !empty($store['theme'])) {
                    //     Yii::$service->store->currentTheme = $store['theme'];
                    // }
                    /**
                     * 是指本地主题路径
                     * set local theme dir.
                     */
                    if (isset($store['localThemeDir']) && $store['localThemeDir']) {
                        //Yii::$service->page->theme->localThemeDir = $store['localThemeDir'];
                        Yii::$service->page->theme->setLocalThemeDir($store['localThemeDir']);
                    }
                    /**
                     * 第三方主题路径
                     * set third theme dir.
                     */
                    if (isset($store['thirdThemeDir']) && $store['thirdThemeDir']) {
                        //Yii::$service->page->theme->thirdThemeDir = $store['thirdThemeDir'];
                        Yii::$service->page->theme->setThirdThemeDir($store['thirdThemeDir']);
                    }
                    /**
                     * 设置货币
                     * init store currency.
                     */
                    if (isset($store['currency']) && !empty($store['currency'])) {
                        $currency = $store['currency'];
                    } else {
                        $currency = '';
                    }
                    Yii::$service->page->currency->initCurrency($currency);
                    /**
                     * current domian is config is store config.
                     */
                    $init_compelte = 1;
                    $this->thirdLogin = $store['thirdLogin'];
                    
                    /**
                     * appserver 部分
                     */
                    if(isset($store['serverLangs']) && !empty($store['serverLangs'])){
                        $this->serverLangs = $store['serverLangs'];
                    }
                    // 获取header
                    $headers = Yii::$app->request->getHeaders();
                    // 设置语言
                    if(isset($headers['fecshop-lang']) && $headers['fecshop-lang']){
                        $h_lang = $headers['fecshop-lang'];
                        if(is_array($this->serverLangs)){
                            foreach($this->serverLangs as $one){
                                if($one['code'] == $h_lang){
                                    Yii::$service->store->currentLangCode = $h_lang;
                                    Yii::$service->store->currentLang = $one['language'];
                                    Yii::$service->store->currentLangName = $one['languageName'];
                                    Yii::$service->page->translate->setLanguage($one['language']);
                                }
                            }
                        }
                    }
                    // 设置币种
                    if(isset($headers['fecshop-currency']) && $headers['fecshop-currency']){
                        $currentC = Yii::$service->page->currency->getCurrentCurrency();
                        if($currentC != $headers['fecshop-currency']){
                            Yii::$service->page->currency->setCurrentCurrency($headers['fecshop-currency']);
                        }
                    }
                    break;
                }
            }
        }
        if (!$init_compelte) {
            throw new InvalidValueException('this domain is not config in store component');
        }
        
    }

    /**
     * pc端自动跳转到html5端的检测
     * @param  [type] $store_code 当前访问的域名
     * @param  [type] $store      当前域名对应的store配置
     * @return [type]             [description]
     */
    protected function html5DevideCheckAndRedirect($store_code, $store)
    {
        if (!isset($store['mobile'])) {
            return;
        }
        $enable = isset($store['mobile']['enable']) ? $store['mobile']['enable'] : false;
        if (!$enable) {
            return;
        }
        // 跳转的条件
        $condition = isset($store['mobile']['condition']) ? $store['mobile']['condition'] : false;
        // 跳转到的域名
        $redirectDomain = isset($store['mobile']['redirectDomain']) ? $store['mobile']['redirectDomain'] : false;
        // 跳转的类型
        $redirectType = isset($store['mobile']['type']) ? $store['mobile']['type'] : false;
        // 如果是要跳h5，可以直接跳转，因为pc的路由和h5的一致
        if (is_array($condition) && !empty($condition) && !empty($redirectDomain) && $redirectType === 'apphtml5') {
            $mobileDetect = Yii::$service->helper->mobileDetect;
            // 是否使用https
            $mobile_https = (isset($store['mobile']['https']) && $store['mobile']['https']) ? true : false;
            // 终端符合条件进行跳转
            if (in_array('phone', $condition) && in_array('tablet', $condition)) {
                if ($mobileDetect->isMobile()) {
                    $this->redirectAppHtml5Mobile($store_code, $redirectDomain, $mobile_https);
                }
            } elseif (in_array('phone', $condition)) {
                if ($mobileDetect->isMobile() && !$mobileDetect->isTablet()) {
                    $this->redirectAppHtml5Mobile($store_code, $redirectDomain, $mobile_https);
                }
            } elseif (in_array('tablet', $condition)) {
                if ($mobileDetect->isTablet()) {
                    $this->redirectAppHtml5Mobile($store_code, $redirectDomain, $mobile_https);
                }
            }
        }
    }

    /**
     * 检测，html5端跳转检测        
     * @param  [type] $store_code     当前访问的域名
     * @param  [type] $redirectDomain 要跳转的域名
     * @param  [type] $mobile_https   是否使用https
     * @return [type]                 [description]
     */
    protected function redirectAppHtml5Mobile($store_code, $redirectDomain, $mobile_https)
    {
        // 当前的url
        $currentUrl = Yii::$service->url->getCurrentUrl();
        $redirectUrl = str_replace($store_code, $redirectDomain, $currentUrl);
        // pc端跳转到html5，可能一个是https，一个是http，因此需要下面的代码进行转换。
        if ($mobile_https) {
            if (strstr($redirectUrl,'https://') || strstr($redirectUrl,'http://')) {
                $redirectUrl = str_replace('http://','https://',$redirectUrl);
            } else {
                $redirectUrl = 'https:'.$redirectUrl;
            }
        } else {
            if (strstr($redirectUrl,'https://') || strstr($redirectUrl,'http://')) {
                $redirectUrl = str_replace('https://','http://',$redirectUrl);
            } else {
                $redirectUrl = 'http:'.$redirectUrl;
            }
        }
        header('Location:'.$redirectUrl);
        exit;
    }
    /**
     * 判断是否符合h5跳转到vue端
     * @return boolean, 检测是否属于满足跳转到appserver的条件
     */
    public function isAppServerMobile(){
        $store = $this->store;
        $condition = isset($store['mobile']['condition']) ? $store['mobile']['condition'] : false;
        $redirectDomain = isset($store['mobile']['redirectDomain']) ? $store['mobile']['redirectDomain'] : false;
        $redirectType = isset($store['mobile']['type']) ? $store['mobile']['type'] : false;
        if (is_array($condition) && !empty($condition) && !empty($redirectDomain) && $redirectType === 'appserver') {
            $mobileDetect = Yii::$service->helper->mobileDetect;
            if (in_array('phone', $condition) && in_array('tablet', $condition)) {
                if ($mobileDetect->isMobile()) {
                    
                    return true;
                }
            } elseif (in_array('phone', $condition)) {
                if ($mobileDetect->isMobile() && !$mobileDetect->isTablet()) {
                    
                    return true;
                }
            } elseif (in_array('tablet', $condition)) {
                if ($mobileDetect->isTablet()) {
                    
                    return true;
                }
            }
        }
        
        return false;
    }
    /**
     * @property $urlPath | String，跳转到vue端的url Path
     * @return boolean, 生成vue端的url，然后进行跳转。
     */
    public function redirectAppServerMobile($urlPath){
        $store = $this->store;
        $redirectDomain = isset($store['mobile']['redirectDomain']) ? $store['mobile']['redirectDomain'] : false;
        $mobile_https = (isset($store['mobile']['https']) && $store['mobile']['https']) ? 'https://' : 'http://';
        $host = $mobile_https.$redirectDomain.'/#/';
        $urlParam = $_SERVER["QUERY_STRING"];
        // 得到当前的语言
        if ($urlParam) {
            $urlParam .= '&lang='.$this->currentLangCode;
        } else {
            $urlParam .= 'lang='.$this->currentLangCode;
        }
        $redirectUrl = $host.$urlPath.'?'.$urlParam;
        header('Location:'.$redirectUrl);
        exit;
    }

    /**
     * 获取字段当前store对应的字段名
     * @property $attrVal|array , language attr array , like   ['title_en' => 'xxxx','title_fr' => 'yyyy']
     * @property $attrName|String, attribute name ,like: title ,description.
     * if  object or array  attribute is a language attribute, you can get current
     * language value by this function.
     * if lang attribute in current store language is empty , default language attribute will be return.
     * if attribute in default language value is empty, $attrVal will be return.
     */
    protected function actionGetStoreAttrVal($attrVal, $attrName)
    {
        $lang = $this->currentLangCode;

        return Yii::$service->fecshoplang->getLangAttrVal($attrVal, $attrName, $lang);
    }

    /**
     * @return array
     *               get all store info, one item in array format is: ['storeCode' => 'store language'].
     */
    protected function actionGetStoresLang()
    {
        $stores = $this->stores;
        $topLang = [];
        foreach ($stores as $storeCode=> $store) {
            $languageName = $store['languageName'];
            $topLang[$storeCode] = $languageName;
        }

        return $topLang;
    }
}
