<?php

namespace App\Models\Enum;
class OrderEnum
{
    // 状态类别
    const PENDING = 1;      // 待付款
    const HASPAY  = 2;      // 付款，待发货
    const SHIPPED = 3;      // 已发货
    const SIGNED  = 4;      // 已签收，未确定
    const SUBMIT  = 5;      // 已确定
    const CANCEL  = 6;      // 已取消

    public static function getOrderStatus($status){
        switch ($status){
            case self::PENDING:
                return '待付款';
            case self::HASPAY:
                return '已付款';
            case self::SHIPPED:
                return '已发货';
            case self::SIGNED:
                return '已签收';
            case self::SUBMIT:
                return '已确定';
            case self::CANCEL:
                return '已取消';
        }
    }
}