<?php
/*
 * 存放 一些基本的非数据库数据 如 html
 * 都是数组
 */

namespace fecshop\app\appfront\modules\Cms\block\home;

use Yii;

class Index
{
    public function getLastData()
    {
        $this->initHead();
        // change current layout File.
        //Yii::$service->page->theme->layoutFile = 'home.php';
        return [
            // 热销产品
            'bestFeaturedProducts'     => $this->getFeaturedProduct(),
            // 特色产品
            'bestSellerProducts'    => $this->getBestSellerProducts(),
        ];
    }

    public function getFeaturedProduct()
    {
        $featured_skus = Yii::$app->controller->module->params['homeFeaturedSku'];

        return $this->getProductBySkus($featured_skus);
    }

    public function getBestSellerProducts()
    {
        $bestSellSkus = Yii::$app->controller->module->params['homeBestSellerSku'];

        return $this->getProductBySkus($bestSellSkus);
    }

    public function getProductBySkus($skus)
    {
        if (is_array($skus) && !empty($skus)) {
            $filter['select'] = [
                'sku', 'spu', 'name', 'image',
                'price', 'special_price',
                'special_from', 'special_to',
                'url_key', 'score',
            ];
            $filter['where'] = ['in', 'sku', $skus];
            // 根据条件获取产品
            $products = Yii::$service->product->getProducts($filter);
            $products = Yii::$service->category->product->convertToCategoryInfo($products);
            // var_dump($products);exit;
            return $products;
        }
    }
    /**
     * 注册html中的meta标签
     * @return [type] [description]
     */
    public function initHead()
    {
        // 标题
        $home_title = Yii::$app->controller->module->params['home_title'];
        // 关键词
        $home_meta_keywords = Yii::$app->controller->module->params['home_meta_keywords'];
        // 描述
        $home_meta_description = Yii::$app->controller->module->params['home_meta_description'];
        // 注册关键词meta标签
        Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => Yii::$service->store->getStoreAttrVal($home_meta_keywords, 'home_meta_keywords'),
        ]);
        // 注册描述meta标签
        Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => Yii::$service->store->getStoreAttrVal($home_meta_description, 'home_meta_description'),
        ]);
        // 设置视图title
        Yii::$app->view->title = Yii::$service->store->getStoreAttrVal($home_title, 'home_title');
    }
}
