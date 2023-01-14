<?php

namespace app\components;

use yii\helpers\ArrayHelper;


class NestedArrayHelper extends ArrayHelper
{
    /**
     * buildParentChildTreeArray
     * Получаем детей элементов
     * @param  mixed $models
     * @return array
     */
    public static function buildParentChildTreeArray(array $models): array
    {
        $childs = array();
        foreach ($models as &$model) {
            $model->setLevel(0);
            $childs[$model->parent_id][] = &$model;
            unset($model);
        }

        foreach ($models as &$model) {

            if (isset($childs[$model->id])) {
                $model->setChildren($childs[$model->id]);
            }
        }

        $firstKey = array_key_first($childs);
        $result = self::setElementsDepth($childs[$firstKey]);
        $result = self::flattenArray($result, 'children');
        return $result;
    }


    /**
     * setElementsDepth
     * Задаём глубину новдов
     * @param  mixed $models
     * @param  mixed $depth
     * @return array
     */
    public static function setElementsDepth(array $models, int $depth = 1): array
    {

        foreach ($models as &$item) {
            $item->level = $depth;
            if (isset($item->children)) {
                self::setElementsDepth($item->children, $depth + 1);
            }
        }
        return $models;
    }


    /**
     * flattenArray
     * выравниваем массив из многомерного в одномерный
     * @param  mixed $nestedModels
     * @param  mixed $key
     * @return array
     */
    public static function flattenArray(array $nestedModels, string $key): array
    {
        $output = [];

        foreach ($nestedModels as $object) {

            if (isset($object->$key)) {
                $children = $object->$key;
            } else {
                $children = [];
            }

            $object->$key = [];
            $output[] = $object;

            $children = self::flattenArray($children, $key);

            foreach ($children as $child) {
                $output[] = $child;
            }
        }
        return $output;
    }
}
