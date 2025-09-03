<!-- Kanban Board Styles -->
<style>
    /* ajustes adicionais base */
    .kanban-compact .kanban-container { display: flex; overflow-x: auto; gap: 12px; }
    .kanban-compact .kanban-column { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 8px; min-width: 260px; }
    .kanban-compact .kanban-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
    .kanban-compact .kanban-title { font-size: 13px; font-weight: 600; color: #334257; }
    .kanban-compact .kanban-badge__count { background: #e9ecef; border-radius: 999px; min-width: 20px; height: 20px; line-height: 20px; text-align: center; font-size: 11px; color: #334257; padding: 0 6px; }
    .kanban-compact .kanban-cards { display: flex; flex-direction: column; gap: 8px; min-height: 60vh; }

    .kanban-compact .order-card { border: 1px solid #e9ecef; border-radius: 8px; cursor: move; padding: 8px 10px; box-shadow: var(--card-shadow, 0 1px 2px rgba(17,24,39,0.06), 0 4px 8px rgba(17,24,39,0.08)); transition: box-shadow 0.2s ease; }
    .kanban-compact .order-card:hover { box-shadow: var(--card-shadow-hover, 0 3px 6px rgba(17,24,39,0.10), 0 10px 20px rgba(17,24,39,0.12)); }
    .kanban-compact .order-card:active { box-shadow: var(--card-shadow-active, 0 6px 12px rgba(17,24,39,0.12), 0 16px 24px rgba(17,24,39,0.14)); }
    .kanban-compact .order-card .order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
    .kanban-compact .order-card .order-id { font-weight: 700; color: #334257; font-size: 12px; }
    .kanban-compact .order-card .order-status { font-size: 11px; color: #6c757d; }
    .kanban-compact .order-card .vendor-info { font-size: 11px; color: #495057; margin-bottom: 6px; }
    .kanban-compact .order-card .order-address { font-size: 11px; color: #6c757d; margin-bottom: 6px; }
    .kanban-compact .order-card .detail-row { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px; }

    .kanban-compact .drop-zone { border: 2px dashed #007bff; background: rgba(0,123,255,0.08); }
    .kanban-compact .payment-method { background: #6f42c1; color: #fff; padding: 2px 6px; border-radius: 8px; font-size: 9px; font-weight: 600; text-transform: uppercase; }
    .kanban-compact .payment-method.pix { background: #fd7e14; }
    .kanban-compact .payment-method.card { background: #20c997; }
    .kanban-compact .stock-status { font-size: 10px; color: #28a745; font-weight: 600; background: rgba(40,167,69,0.1); padding: 2px 6px; border-radius: 4px; margin-bottom: 4px; display: inline-block; }
    .kanban-compact .toast-container { position: fixed; top: 16px; right: 16px; z-index: 1050; }
    .kanban-compact .toast { background: #28a745; color: #fff; padding: 10px 16px; border-radius: 6px; display: none; }

    /* Borda mais sutil e linhas mais finas */
    .kanban-compact .kanban-column, .kanban-compact .order-card { border-color: #e4e6ef; }
    /* Drag-to-scroll (panning) like Trello */
    .kanban-compact .kanban-container { cursor: grab; }
    .kanban-compact .kanban-container.is-panning { cursor: grabbing; user-select: none; }

    /* Badge styles for column titles - cores suaves (pastéis) */
    .kanban-compact .kanban-badge { display: inline-flex; align-items: center; gap: 8px; padding: 4px 10px; border-radius: 999px; font-weight: 600; font-size: 12px; color: #334257; }
    .kanban-compact .kanban-badge__title { line-height: 1; }
    .kanban-compact .kanban-badge__count { background: rgba(0,0,0,0.06); border-radius: 999px; padding: 2px 8px; font-size: 11px; line-height: 1; color: #334257; }

    /* Status colors (tons claros) */
    .kanban-compact .kanban-badge--pending { background: #fff3cd; }
    .kanban-compact .kanban-badge--confirmed { background: #d4edda; }
    .kanban-compact .kanban-badge--processing { background: #cfe2ff; }
    .kanban-compact .kanban-badge--delivering { background: #e7e1ff; }
    .kanban-compact .kanban-badge--delivered { background: #d1e7dd; }
    .kanban-compact .kanban-badge--canceled { background: #f8d7da; }
    .kanban-compact .kanban-badge--transport { background: #ffe5d0; }

    /* Cards com amarelo bem clarinho */
    .kanban-compact .order-card { background-color: #fffbea; border-color: #ffecb5; }
    .kanban-compact .order-card:focus-visible { outline: none; box-shadow: var(--card-shadow, 0 1px 2px rgba(17,24,39,0.06), 0 4px 8px rgba(17,24,39,0.08)), 0 0 0 3px rgba(59,130,246,0.30); }
    /* Sombras por etapa (variáveis por coluna) */
    .kanban-compact .kanban-column[data-status="pending"] {
      --card-shadow: 0 1px 2px rgba(255,193,7,0.18), 0 4px 8px rgba(255,193,7,0.22);
      --card-shadow-hover: 0 3px 6px rgba(255,193,7,0.22), 0 10px 20px rgba(255,193,7,0.28);
      --card-shadow-active: 0 6px 12px rgba(255,193,7,0.26), 0 16px 24px rgba(255,193,7,0.32);
    }
    .kanban-compact .kanban-column[data-status="confirmed"] {
      --card-shadow: 0 1px 2px rgba(25,135,84,0.18), 0 4px 8px rgba(25,135,84,0.22);
      --card-shadow-hover: 0 3px 6px rgba(25,135,84,0.22), 0 10px 20px rgba(25,135,84,0.28);
      --card-shadow-active: 0 6px 12px rgba(25,135,84,0.26), 0 16px 24px rgba(25,135,84,0.32);
    }
    .kanban-compact .kanban-column[data-status="processing"] {
      --card-shadow: 0 1px 2px rgba(13,110,253,0.18), 0 4px 8px rgba(13,110,253,0.22);
      --card-shadow-hover: 0 3px 6px rgba(13,110,253,0.22), 0 10px 20px rgba(13,110,253,0.28);
      --card-shadow-active: 0 6px 12px rgba(13,110,253,0.26), 0 16px 24px rgba(13,110,253,0.32);
    }
    .kanban-compact .kanban-column[data-status="delivering"] {
      --card-shadow: 0 1px 2px rgba(111,66,193,0.18), 0 4px 8px rgba(111,66,193,0.22);
      --card-shadow-hover: 0 3px 6px rgba(111,66,193,0.22), 0 10px 20px rgba(111,66,193,0.28);
      --card-shadow-active: 0 6px 12px rgba(111,66,193,0.26), 0 16px 24px rgba(111,66,193,0.32);
    }
    .kanban-compact .kanban-column[data-status="delivered"] {
      --card-shadow: 0 1px 2px rgba(32,201,151,0.18), 0 4px 8px rgba(32,201,151,0.22);
      --card-shadow-hover: 0 3px 6px rgba(32,201,151,0.22), 0 10px 20px rgba(32,201,151,0.28);
      --card-shadow-active: 0 6px 12px rgba(32,201,151,0.26), 0 16px 24px rgba(32,201,151,0.32);
    }
    .kanban-compact .kanban-column[data-status="canceled"] {
      --card-shadow: 0 1px 2px rgba(220,53,69,0.18), 0 4px 8px rgba(220,53,69,0.22);
      --card-shadow-hover: 0 3px 6px rgba(220,53,69,0.22), 0 10px 20px rgba(220,53,69,0.28);
      --card-shadow-active: 0 6px 12px rgba(220,53,69,0.26), 0 16px 24px rgba(220,53,69,0.32);
    }
    .kanban-compact .kanban-column[data-status="transport"] {
      --card-shadow: 0 1px 2px rgba(253,126,20,0.18), 0 4px 8px rgba(253,126,20,0.22);
      --card-shadow-hover: 0 3px 6px rgba(253,126,20,0.22), 0 10px 20px rgba(253,126,20,0.28);
      --card-shadow-active: 0 6px 12px rgba(253,126,20,0.26), 0 16px 24px rgba(253,126,20,0.32);
    }
</style>

<div class="kanban-compact">
    <!-- Kanban Board -->
    <div class="kanban-container">
        @foreach($kanbanColumns as $statusKey => $statusLabel)
            <div class="kanban-column" data-status="{{ $statusKey }}">
                <div class="kanban-header">
                    <span class="kanban-badge kanban-badge--{{ $statusKey }}">
                        <span class="kanban-badge__title">{{ $statusLabel }}</span>
                        <span class="kanban-badge__count">{{ count($ordersByStatus[$statusKey] ?? []) }}</span>
                    </span>
                </div>
                <div class="kanban-cards" ondragover="allowDrop(event)" ondrop="drop(event)">
                    @foreach($ordersByStatus[$statusKey] ?? [] as $order)
                        <div class="order-card" draggable="true" ondragstart="drag(event)" data-order-id="{{ $order->id }}">
                            <div class="days-ago">{{ $order->created_at->diffInDays() }}d</div>

                            <div class="order-header">
                                <span class="order-id">#{{ $order->id }}</span>
                                <span class="order-status">{{ ucfirst($order->status) }}</span>
                            </div>

                            <div class="vendor-info">
                                <div class="vendor-name">{{ $order->vendor->f_name ?? 'Nome não disponível' }} {{ $order->vendor->l_name ?? '' }}</div>
                                <div class="vendor-id">ID: {{ $order->vendor->id ?? 'N/A' }}</div>
                            </div>

                            <div class="stock-status">Estoque Disponível</div>

                            <div class="order-address">
                                {{ $order->vendor->address ?? 'Endereço não disponível' }}
                            </div>

                            <div class="order-details">
                                <div class="detail-row">
                                    <span class="detail-label">Total:</span>
                                    <span class="detail-value">R$ {{ number_format(($order->total_amount ?? 0) > 0 ? $order->total_amount : ($order->items->sum('total_price') ?? 0), 2, ',', '.') }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Data:</span>
                                    <span class="detail-value">{{ $order->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Pagamento:</span>
                                    <span class="payment-method {{ strtolower($order->payment_method ?? 'dinheiro') }}">
                                        {{ $order->payment_method ?? 'Dinheiro' }}
                                    </span>
                                    <span class="items-count">{{ $order->items->count() ?? 0 }} itens</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <!-- Toasts -->
    <div class="toast-container">
        <div class="toast" id="successToast">Status do pedido atualizado com sucesso!</div>
        <div class="toast" id="errorToast" style="background:#dc3545;">Erro ao atualizar status do pedido!</div>
    </div>
</div>

<script>
let draggedElement = null;

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    draggedElement = ev.target;
    ev.target.classList.add('dragging');
    ev.dataTransfer.setData("text", ev.target.getAttribute('data-order-id'));
}

function drop(ev) {
    ev.preventDefault();
    
    if (draggedElement) {
        draggedElement.classList.remove('dragging');
    }
    
    const orderId = ev.dataTransfer.getData("text");
    const targetColumn = ev.target.closest('.kanban-column');
    
    if (!targetColumn) return;
    
    const newStatus = targetColumn.getAttribute('data-status');
    const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
    
    if (!orderCard) return;
    
    // Add visual feedback
    targetColumn.classList.add('drop-zone');
    
    // Move the card visually
    targetColumn.querySelector('.kanban-cards').appendChild(orderCard);
    
    // Update status badge
    const statusBadge = orderCard.querySelector('.order-status');
    if (statusBadge) {
        statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
    }
    
    // Update column counts
    updateColumnCounts();
    
    // Send AJAX request to update database
    updateOrderStatus(orderId, newStatus);
    
    // Remove visual feedback after a short delay
    setTimeout(() => {
        targetColumn.classList.remove('drop-zone');
    }, 300);
}

function updateColumnCounts() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const count = column.querySelectorAll('.order-card').length;
        const countElement = column.querySelector('.kanban-badge__count');
        if (countElement) {
            countElement.textContent = count;
        }
    });
}

function updateOrderStatus(orderId, newStatus) {
    fetch('{{ route("distributor.orders.update_status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            order_id: orderId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('successToast');
        } else {
            showToast('errorToast');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('errorToast');
    });
}

function showToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }
}

// Prevent default drag behavior on document
document.addEventListener('dragover', function(e) {
    e.preventDefault();
});

document.addEventListener('drop', function(e) {
    e.preventDefault();
});

// Add dragend event listener to remove dragging class
document.addEventListener('dragend', function(e) {
    if (e.target.classList.contains('order-card')) {
        e.target.classList.remove('dragging');
    }
});

// Drag-to-scroll (panning) like Trello for horizontal Kanban scrolling
(function () {
  const container = document.querySelector('.kanban-compact .kanban-container');
  if (!container) return;

  let isDown = false;
  let startX = 0;
  let startScrollLeft = 0;

  const shouldStartPan = (target) => {
    // Don't hijack when interacting with a draggable card
    return !target.closest('.order-card');
  };

  // Mouse support
  container.addEventListener('mousedown', (e) => {
    if (e.button !== 0) return; // left button only
    if (!shouldStartPan(e.target)) return;
    isDown = true;
    container.classList.add('is-panning');
    startX = e.clientX;
    startScrollLeft = container.scrollLeft;
  });

  container.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const delta = e.clientX - startX;
    container.scrollLeft = startScrollLeft - delta;
  });

  const endPan = () => {
    if (!isDown) return;
    isDown = false;
    container.classList.remove('is-panning');
  };

  document.addEventListener('mouseup', endPan);
  container.addEventListener('mouseleave', endPan);

  // Touch support
  container.addEventListener('touchstart', (e) => {
    if (e.touches.length !== 1) return;
    const t = e.touches[0];
    const el = document.elementFromPoint(t.clientX, t.clientY) || container;
    if (!shouldStartPan(el)) return;
    isDown = true;
    container.classList.add('is-panning');
    startX = t.clientX;
    startScrollLeft = container.scrollLeft;
  }, { passive: true });

  container.addEventListener('touchmove', (e) => {
    if (!isDown) return;
    const t = e.touches[0];
    const delta = t.clientX - startX;
    container.scrollLeft = startScrollLeft - delta;
  }, { passive: true });

  container.addEventListener('touchend', endPan);
  container.addEventListener('touchcancel', endPan);
})();
</script>