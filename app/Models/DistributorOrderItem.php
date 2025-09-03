<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_order_id',
        'food_id',
        'product_name',
        'unit_price',
        'quantity',
        'total_price',
        'product_details'
    ];

    protected $casts = [
        'unit_price' => 'float',
        'quantity' => 'integer',
        'total_price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com o pedido do distribuidor
     */
    public function distributorOrder()
    {
        return $this->belongsTo(DistributorOrder::class);
    }

    /**
     * Relacionamento com o produto
     */
    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    /**
     * Accessor para detalhes do produto (JSON decode)
     */
    public function getProductDetailsAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * Mutator para detalhes do produto (JSON encode)
     */
    public function setProductDetailsAttribute($value)
    {
        $this->attributes['product_details'] = $value ? json_encode($value) : null;
    }

    /**
     * Calcula o preço total do item
     */
    public function calculateTotalPrice()
    {
        $this->total_price = $this->quantity * $this->unit_price;
        return $this->total_price;
    }

    /**
     * Atualiza a quantidade e recalcula o total
     */
    public function updateQuantity($newQuantity)
    {
        $this->quantity = $newQuantity;
        $this->calculateTotalPrice();
        $this->save();
        
        // Recalcula o total do pedido
        $this->distributorOrder->calculateTotal();
    }
}
