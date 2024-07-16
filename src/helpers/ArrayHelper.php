<?php

namespace davidxu\adminlte4\helpers;

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
     * @param int|string|null $parent_id
     * @param int $level
     * @param string $idField
     * @param string $parentField
     * @param string $levelField
     * @return array
     */
    public static function getMenuItems(array $categories, int|string|null $parent_id = null, int $level = 1, string $idField = 'id', string $parentField = 'parent_id', string $levelField = 'level'): array
    {
        $items = [];
        foreach ($categories as $category) {
            if ($category[$parentField] === $parent_id) {
                $category[$levelField] = $level;
                $category['children'] = static::getMenuItems($categories, $category[$idField], $level + 1);
                if (count($category['children'])) {
                    $category['items'] = $category['children'];
                }
                unset($category['children'], $category[$levelField]);
                $items[] = $category;
            }
        }
        return $items;
    }
}
