<?php

return [
    [
        'key'    => 'sales.paymentmethods.razorpay',
        'name'   => 'Razorpay',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'admin::app.admin.system.title',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'admin::app.admin.system.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'clientid',
                'title'         => 'admin::app.admin.system.client-id',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                 'name'          => 'clientsecret',
                'title'         => 'admin::app.admin.system.client-secret',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'testclientid',
                'title'         => 'Test Mode ID',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                 'name'          => 'testclientsecret',
                'title'         => 'Test Mode Secret',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'admin::app.admin.system.status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ]
            ,  [
                'name'          => 'sandbox',
                'title'         => 'Test Mode',
                'type'          => 'boolean',
                'channel_based' => false,
                'locale_based'  => true,
            ],[
                'name'          => 'merchantname',
                'title'         => 'Merchant Name (To be shown on payment form)',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'merchantdesc',
                'title'         => 'Transaction Description (To be shown on payment form)',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ]
        ]
    ]
];