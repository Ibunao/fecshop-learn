<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fecshop\app\appfront\modules\Catalog\block\category;

use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Price
{
    public $price;
    public $special_price;
    public $special_from;
    public $special_to;
    /**
     * 获取当前币种的价格
     * @return [type] [description]
     */
    public function getLastData()
    {
        return  Yii::$service->product->price->getCurrentCurrencyProductPriceInfo($this->price, $this->special_price, $this->special_from, $this->special_to);
    }
}
