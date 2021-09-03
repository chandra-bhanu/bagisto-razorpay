<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * CREATE TABLE `rzp_payments` (
  `id` int(11) NOT NULL,
  `razorpay_event_id` varchar(50) DEFAULT NULL,  
  `razorpay_invoice_id` varchar(40) DEFAULT NULL,
  `razorpay_order_id` varchar(80) DEFAULT NULL,
  `razorpay_payment_id` varchar(40) DEFAULT NULL,
  `razorpay_invoice_status` varchar(40) DEFAULT 'issued',
  `razorpay_invoice_receipt` varchar(40) DEFAULT NULL,
  `razorpay_signature` longtext,  
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 */
namespace Neexpg\Razorpay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RazorpayEvents extends Model
{
   protected $table = 'rzp_payments';
   
    use SoftDeletes;
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
   
   /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'core_order_id',
        'razorpay_event_id',
        'razorpay_invoice_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_invoice_status',
        'razorpay_invoice_receipt',
        'razorpay_signature',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

   
}
