<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fecshop\services\page;

use fecshop\services\Service;
use Yii;
use yii\base\InvalidValueException;

/**
 * Page Theme services. 关于fecshop模板的更多知识可以参阅文档：http://www.fecshop.com/doc/fecshop-guide/develop/cn-1.0/guide-fecshop-theme.html
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Theme extends Service
{
    /**
     * 用户本地的模板路径，优先级最高
     */
    public $localThemeDir;
    /**
     * $thirdThemeDir | Array
     * 第三方模板的模板路径，数组格式，允许多个，数组第一个模板路径的优先级高于第二个模板路径。
     * array[0] priority is higher than array[1],.
     */
    public $thirdThemeDir;
    /**
     * fecshop的模板路径，模板文件最全，但是优先级最低，因此本地模板路径和第三方模板路径的文件会覆盖fecshop的文件，来实现模板文件的重写。
     */
    public $fecshopThemeDir;
    /**
     * current layout file path.
     */
    public $layoutFile;
    /**
     * array that contains mutil theme dir.
     */
    protected $_themeDirArr;
    /**
     * @return  array ，根据模板路径的优先级，以及得到各个模板路径，组合成数组
     * 数组前面的模板路径优先级最高
     */
    protected function actionGetThemeDirArr()
    {
        if (!$this->_themeDirArr || empty($this->_themeDirArr)) {
            $arr = [];
            if ($localThemeDir = Yii::getAlias($this->localThemeDir)) {
                $arr[] = $localThemeDir;
            }

            $thirdThemeDirArr = $this->thirdThemeDir;
            if (!empty($thirdThemeDirArr) && is_array($thirdThemeDirArr)) {
                foreach ($thirdThemeDirArr as $theme) {
                    $arr[] = Yii::getAlias($theme);
                }
            }
            $arr[] = Yii::getAlias($this->fecshopThemeDir);
            $this->_themeDirArr = $arr;
        }

        return $this->_themeDirArr;
    }

    /**
     * @property $view | String ，view路径的字符串。
     * @property $throwError | boolean，view文件找不到的时候是否抛出异常。
     * 根据模板路径的优先级，依次查找view文件，找到后，返回view文件的绝对路径。
     */
    protected function actionGetViewFile($view, $throwError = true)
    {
        $view = trim($view);
        // 如果是别名的形式，直接获取
        if (substr($view, 0, 1) == '@') {
            return Yii::getAlias($view);
        }
        $relativeFile = '';
        $module = Yii::$app->controller->module;
        if ($module && $module->id) {
            $relativeFile = $module->id.'/';
        }
        // 根据module和controller来确定相对位置
        $relativeFile .= Yii::$app->controller->id.'/'.$view.'.php';
        $absoluteDir = Yii::$service->page->theme->getThemeDirArr();
        foreach ($absoluteDir as $dir) {
            if ($dir) {
                $file = $dir.'/'.$relativeFile;
                if (file_exists($file)) {
                    return $file;
                }
            }
        }
        /* not find view file */
        if ($throwError) {
            $notExistFile = [];
            foreach ($absoluteDir as $dir) {
                if ($dir) {
                    $file = $dir.'/'.$relativeFile;
                    $notExistFile[] = $file;
                }
            }
            throw new InvalidValueException('view file is not exist in'.implode(',', $notExistFile));
        } else {
            return false;
        }
    }
    /**
     * @property $dir | string 设置本地模板路径
     */
    protected function actionSetLocalThemeDir($dir)
    {
        $this->localThemeDir = $dir;
    }
    /**
     * @property $dir | string 设置第三方模板路径
     */
    protected function actionSetThirdThemeDir($dir)
    {
        $this->thirdThemeDir = $dir;
    }
}
