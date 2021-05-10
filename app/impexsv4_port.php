<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class impexsv4_port extends Model
{
    //

    protected $connection= 'impex-sv4';

    protected $table = 'dbo.Ports';

    protected $fillable = ['port_name'];

    
}
