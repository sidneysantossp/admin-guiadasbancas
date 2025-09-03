<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'distributor_id',
        'vendor_id',
        'total_amount',
        'total_items',
        'status',
        'notes',
        'delivery_date',
        'delivery_address',
        'payment_status',
        'payment_method'
    ];

    protected $casts = [
        'total_amount' => 'float',
        'total_items' => 'integer',
        'delivery_date' => 'datetime',
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
     * Relacionamento com os itens do pedido
     */
    public function items()
    {
        return $this->hasMany(DistributorOrderItem::class);
    }

    /**
     * Gera um número único para o pedido
     */
    public static function generateOrderNumber()
    {
        $prefix = 'DIST';
        $timestamp = now()->format('YmdHis');
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Scope para pedidos de um distribuidor específico
     */
    public function scopeForDistributor($query, $distributorId)
    {
        return $query->where('distributor_id', $distributorId);
    }

    /**
     * Scope para pedidos de um vendor específico
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope para pedidos pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para pedidos confirmados
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope para pedidos entregues
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Verifica se o pedido pode ser cancelado
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Verifica se o pedido pode ser editado
     */
    public function canBeEdited()
    {
        return $this->status === 'pending';
    }

    /**
     * Atualiza o status do pedido
     */
    public function updateStatus($newStatus, $notes = null)
    {
        $this->status = $newStatus;
        if ($notes) {
            $this->notes = $this->notes ? $this->notes . "\n" . $notes : $notes;
        }
        $this->save();
    }

    /**
     * Calcula o total do pedido baseado nos itens
     */
    public function calculateTotal()
    {
        $total = $this->items()->sum('total_price');
        $this->total_amount = $total;
        $this->total_items = $this->items()->sum('quantity');
        $this->save();
        
        return $total;
    }

    /**
     * Adiciona um item ao pedido
     */
    public function addItem($foodId, $quantity, $unitPrice, $productName, $productDetails = null)
    {
        $totalPrice = $quantity * $unitPrice;
        
        $item = $this->items()->create([
            'food_id' => $foodId,
            'product_name' => $productName,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'total_price' => $totalPrice,
            'product_details' => $productDetails ? json_encode($productDetails) : null
        ]);

        $this->calculateTotal();
        
        return $item;
    }

    /**
     * Status em português
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pendente',
            'confirmed' => 'Confirmado',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Status de pagamento em português
     */
    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'cancelled' => 'Cancelado'
        ];

        return $labels[$this->payment_status] ?? $this->payment_status;
    }
}
