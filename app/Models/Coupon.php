<?php

namespace App\Models;

use App\Modules\ArrayModel;

class Coupon extends ArrayModel
{
    protected static array $data = [
        [
            'id' => 1,
            'code' => 'SUMMER',
            'type' => 'fixed',
            'value' => 10,
            'status' => 0,
            'condition' => [
                'total_amount' => 100,
                'total_items' => 2,
            ],
            'auto_apply' => 0
        ],
        [
            'id' => 2,
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
            'status' => 1,
            'condition' => [
                'total_amount' => null,
                'total_items' => 3,
            ],
            'auto_apply' => 1
        ]
    ];

    protected $fillable = [
        'id',
        'code',
        'type',
        'value',
        'status',
        'condition',
        'auto_apply'
    ];
}
