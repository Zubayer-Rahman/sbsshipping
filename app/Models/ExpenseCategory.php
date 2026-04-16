<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'code', 'parent_category'];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'sub_category', 'name');
    }
}