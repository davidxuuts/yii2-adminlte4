<?php

namespace davidxu\adminlte4\helpers;

use Yii;
use yii\helpers\BaseArrayHelper;
use yii\helpers\HtmlPurifier;

class ArrayHelper extends BaseArrayHelper
{
    /**
     * @param array $lists
     * @param int|string|null $catId
     * @param int $level
     * @return array
     */
    public static function getItems(array $lists, int|string $catId = null, int $level = 1): array
    {
        $subs = [];
        if (count($lists) <= 0) {
            return $subs;
        }
        foreach($lists as $item) {
            if($item['parent_id'] === $catId) {
                $item['level'] = $level;
                $subs[] = $item;
                $subs = array_merge($subs, static::getItems($lists, $item['id'], $level + 1));
            }
        }
        return $subs;
    }

    /**
     * @param array $categories
     * @param string $idField
     * @param string $titleField
     * @param string $levelField
     * @param string $strPad
     * @return array
     */
    public static function getDropdownItems(array $categories, string $idField = 'id', string $titleField = 'title', string $levelField = 'level', string $strPad = '&nbsp;&nbsp;&nbsp;&nbsp;|--&nbsp;'): array
    {
        $items = [];
        if (count($categories) <= 0) {
            return $items;
        }
        foreach ($categories as $category) {
            $items[$category[$idField]] = HtmlPurifier::process(str_repeat($strPad, $category[$levelField] - 1)) . $category[$titleField];
        }
        return $items;
    }


    /**
     * @param array $categories
     * @param string $idField
     * @param string $titleField
     * @param string $levelField
     * @param string $strPad
     * @return array
     */
    public static function getMenuItems(array $categories, string $idField = 'id', string $titleField = 'title', string $levelField = 'level', string $strPad = '&nbsp;&nbsp;&nbsp;&nbsp;|--&nbsp;'): array
    {
        $items = [];
        if (count($categories) <= 0) {
            return $items;
        }
        Yii::info($categories);
        foreach ($categories as $category) {
//            $items[$category[$idField]] = HtmlPurifier::process(str_repeat($strPad, $category[$levelField] - 1)) . $category[$titleField];
            $items[] = [
                'label' => $category[$titleField],
                'url' => $category['url'] ?? '#',
            ];
        }
        return $items;
    }
}
