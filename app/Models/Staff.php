<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    // កែឈ្មោះ Table ឱ្យត្រូវជាមួយ Database របស់យើង
    protected $table = 'tbl_food';

    // កែ Field ឱ្យត្រូវជាមួយ Column ក្នុង tbl_food (name និង price)
    protected $fillable = [
        'name',
        'price',
    ];

    public $timestamps = false; // បិទ timestamps បើ tbl_food គ្មាន created_at/updated_at
}