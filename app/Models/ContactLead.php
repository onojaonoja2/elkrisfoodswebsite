<?php

namespace App\Models;

use Database\Factories\ContactLeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactLead extends Model
{
    /** @use HasFactory<ContactLeadFactory> */
    use HasFactory;

    protected $fillable = [
        'intent',
        'name',
        'email',
        'phone',
        'message',
        'status',
    ];
}
