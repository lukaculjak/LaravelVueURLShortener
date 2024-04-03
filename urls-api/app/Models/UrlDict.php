<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlDict extends Model
{
    use HasFactory;
    protected $table = 'url_dicts';
    protected $fillable = ['url', 'shortened_url'];
}
