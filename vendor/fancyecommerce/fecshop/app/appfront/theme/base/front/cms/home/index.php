<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
?>
<div class="main container">
	<!-- 轮播和主题块 静态块 -->
	<?=  Yii::$service->cms->staticblock->getStoreContentByIdentify('home-big-img','appfront') ?>
	<!-- 热销产品 -->
	<div class="mt10">
		<h4 class="sectionBox_h best_seller"><span><?= Yii::$service->page->translate->__('best seller'); ?></span><em><a href=""><?= Yii::$service->page->translate->__('more'); ?></a></em></h4>
		<div class="pro-content">
			<?php
				$parentThis['products'] = $bestSellerProducts;
				$parentThis['name'] = 'best-seller';
				$config = [
					'view'  		=> 'cms/home/index/product.php',
				];
				echo Yii::$service->page->widget->renderContent('category_product_price',$config,$parentThis);
			?>
		</div>
	</div>
	<div class="clear"></div>
	<!-- 特色产品 -->
	<div class="mt10" style="margin-top:34px;">
		<h4 class="sectionBox_h featured"><span><?= Yii::$service->page->translate->__('featured products'); ?></span><em><a href=""><?= Yii::$service->page->translate->__('more'); ?></a></em></h4>
		<div class="pro-content">
			<?php
				$parentThis['products'] = $bestFeaturedProducts;
				$parentThis['name'] = 'featured';
				$config = [
					'view'  		=> 'cms/home/index/product.php',
				];
				echo Yii::$service->page->widget->renderContent('category_product_price',$config,$parentThis);
			?>
		</div>
	</div>
</div>

<script>
<?php $this->beginBlock('owl_fecshop_slider') ?>  
$(document).ready(function(){
	$("#owl-fecshop").owlCarousel({
		navigation : true,
		slideSpeed : 300,
		paginationSpeed : 400,
		singleItem : true,
		autoPlay:3000,
		lazyLoad:true
      // "singleItem:true" is a shortcut for:
      // items : 1, 
      // itemsDesktop : false,
      // itemsDesktopSmall : false,
      // itemsTablet: false,
      // itemsMobile : false
	});
	
	$("#owl-best-seller").owlCarousel({
		items : 4,
		lazyLoad : true,
		navigation : true,
		scrollPerPage : true,
		pagination:false,
		itemsCustom : false,
        slideSpeed : 900
	});
	$("#owl-featured").owlCarousel({
		items : 4,
		lazyLoad : true,
		navigation : true,
		scrollPerPage : true,
		pagination:false,
		itemsCustom : false,
        slideSpeed : 900
	});
});
<?php $this->endBlock(); ?>  
</script>  
<!-- 先存到block，再注册，避免了麻烦的字符串 -->
<?php $this->registerJs($this->blocks['owl_fecshop_slider'],\yii\web\View::POS_END);//将编写的js代码注册到页面底部 ?>
