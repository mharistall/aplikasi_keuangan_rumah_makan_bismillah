<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'kategori';

    protected $fillable = ['name', 'type'];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'category_id', 'id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id', 'id');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'pengeluaran');
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'pemasukan');
    }
}
