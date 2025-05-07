<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/4/30 14:59
 */

namespace DeepSeek\constant;

/**
 * @Description
 * 枚举基类
 *
 * @Class  : Enum
 * @Package: extend\slms\llm\constant
 */
abstract class Enum
{
    /**
     * [val => name]
     * or
     * [parent => [val => name]]
     *
     * @var array
     */
    protected static array $nameList = [];

    /**
     * [val => code]
     *
     * @var array
     */
    protected static array $valueList = [];

    /**
     * 获取值列表，key 不存在返回全部
     *
     * @param null $key
     * @param bool $returnAll 是否返回全部
     *
     * @return array
     */
    public static function getValueList($key = null, bool $returnAll = true): array
    {
        // 返回指定区间值
        if (isset(static::$nameList[$key]) && is_array(static::$nameList[$key])) {
            return array_keys(static::$nameList[$key]);
        }

        // 返回全部值
        return $returnAll ? array_keys(static::$valueList) : [];
    }

    /**
     * 获取名称列表，key 不存在返回全部
     *
     * @param null $key
     * @param bool $returnAll 是否返回全部
     *
     * @return array
     * @throws \Exception
     */
    public static function getNameList($key = null, bool $returnAll = true): array
    {
        if (isset(static::$nameList[$key]) && is_array(static::$nameList[$key])) {
            return static::$nameList[$key];
        } else {
            if (!$returnAll) return [];

            if (
                !empty(
                    array_filter(
                        static::$nameList,
                        function ($item) { return !is_array($item); }
                    )
                )
            ) {
                throw new \Exception('nameList格式错误');
            }

            if (is_array(current(static::$nameList))) {
                $res = [];
                foreach (static::$nameList as $item) {
                    $res = array_merge($res, array_keys($item));
                }

                return $res;
            } else {
                return static::$nameList;
            }
        }
    }

    public static function getName($value)
    {
        if (isset(static::$nameList[$value])) {
            return static::$nameList[$value];
        } else {
            throw new \Exception("枚举名称(".$value.")不存在");
        }
    }

    public static function getValue($code)
    {
        $list = array_flip(static::$valueList);
        if (isset($list[$code])) {
            return $list[$code];
        } else {
            throw new \Exception("枚举码(".$code.")不存在");
        }
    }

    public static function getCode($value)
    {
        if (isset(static::$valueList[$value])) {
            return static::$valueList[$value];
        } else {
            throw new \Exception("枚举值(".$value.")不存在");
        }
    }
}
