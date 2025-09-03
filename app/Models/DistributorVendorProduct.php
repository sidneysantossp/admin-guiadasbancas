<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\RestaurantScope;
use App\Scopes\ZoneScope;

class DistributorVendorProduct extends Model
{
    use HasFactory;

    protected $table = 'distributor_vendor_products';

    protected $fillable = [
        'distributor_id',
        'vendor_id',
        'food_id',
        'vendor_price',
        'margin_percentage',
        'min_quantity',
        'stock_quantity',
        'status',
        'auto_approve'
    ];

    protected $casts = [
        'vendor_price' => 'float',
        'margin_percentage' => 'float',
        'min_quantity' => 'integer',
        'stock_quantity' => 'integer',
        'status' => 'boolean',
        'auto_approve' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com o distribuidor
     */
    public function distributor()
    {
        return $this->belongsTo(Vendor::class, 'distributor_id');
    }

    /**
     * Relacionamento com o jornaleiro/vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Relacionamento com o produto
     */
    public function food()
    {
        // Ignora escopos globais (RestaurantScope, ZoneScope) para garantir acesso ao produto do distribuidor
        return $this->belongsTo(Food::class, 'food_id')
            ->withoutGlobalScope(RestaurantScope::class)
            ->withoutGlobalScope(ZoneScope::class);
    }

    /**
     * Scope para produtos ativos
     */
    public function scopeActive($query)
    {
        // Qualifica a coluna para evitar ambiguidade após joins
        return $query->where($this->getTable() . '.status', 1);
    }

    /**
     * Scope para produtos de um distribuidor específico
     */
    public function scopeForDistributor($query, $distributorId)
    {
        return $query->where($this->getTable() . '.distributor_id', $distributorId);
    }

    /**
     * Scope para produtos disponíveis para um vendor específico
     */
    public function scopeForVendor($query, $vendorId)
    {
        // Qualifica a coluna para evitar ambiguidade com a tabela food (que também possui vendor_id)
        return $query->where($this->getTable() . '.vendor_id', $vendorId);
    }

    /**
     * Scope para produtos com estoque disponível
     */
    public function scopeInStock($query)
    {
        return $query->where($this->getTable() . '.stock_quantity', '>', 0);
    }

    /**
     * Retorna o estoque disponível para pedido.
     * Se o estoque do pivot (jornaleiro) estiver zerado ou nulo,
     * usa o estoque do produto do distribuidor (food.item_stock).
     */
    public function getAvailableStock()
    {
        $pivotStock = (int) ($this->stock_quantity ?? 0);
        if ($pivotStock > 0) {
            return $pivotStock;
        }
        return (int) ($this->food->item_stock ?? 0);
    }

    /**
     * Verifica se o produto está disponível para pedido
     */
    public function isAvailableForOrder($quantity = 1)
    {
        return $this->status &&
               $this->getAvailableStock() >= $quantity &&
               $quantity >= $this->min_quantity;
    }

    /**
     * Calcule o preço final com margem
     */
    public function getFinalPriceAttribute()
    {
        $basePrice = $this->food->price ?? 0;
        if ($this->margin_percentage > 0) {
            return $basePrice + ($basePrice * $this->margin_percentage / 100);
        }
        return $this->vendor_price > 0 ? $this->vendor_price : $basePrice;
    }

    /**
     * Verifica se há estoque suficiente
     */
    public function hasStock($quantity = 1)
    {
        return $this->getAvailableStock() >= $quantity;
    }

    /**
     * Reduz o estoque após um pedido
     */
    public function reduceStock($quantity)
    {
        if ($this->hasStock($quantity)) {
            // Mantém a política atual de decrementar apenas o estoque do pivot
            $this->decrement('stock_quantity', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Aumenta o estoque (reposição)
     */
    public function addStock($quantity)
    {
        $this->increment('stock_quantity', $quantity);
    }
}
