<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class impexsv4_supplier extends Model
{
    //

    protected $connection= 'impex-sv4';

    protected $table = 'dbo.Suppliers';
}
