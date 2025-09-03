<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Distributor\DashboardController;
use App\Http\Controllers\Distributor\OrderController;

Route::group(['prefix' => 'distributor', 'as' => 'distributor.', 'middleware' => ['auth:distributor']], function () {
    // Rota do dashboard (já existente no projeto)
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Endpoint para dados do gráfico de pedidos por período
    Route::get('/dashboard/orders-chart', [DashboardController::class, 'ordersChart'])->name('dashboard.orders_chart');

    // NOVOS endpoints para os cards/gráficos solicitados
    Route::get('/dashboard/latest-orders-by-vendor', [DashboardController::class, 'latestOrdersByVendor'])->name('dashboard.latest_orders_by_vendor');
    Route::get('/dashboard/top-products', [DashboardController::class, 'topProducts'])->name('dashboard.top_products');

    // Rotas de pedidos - List view por padrão
    Route::get('/orders/list/{status?}', [OrderController::class, 'list'])->name('orders');
    // Kanban opcional
    Route::get('/orders/kanban', [OrderController::class, 'kanban'])->name('orders.kanban');
    Route::post('/orders/update-status', [OrderController::class, 'updateKanbanStatus'])->name('orders.update_status');
    // Detalhes e atualização via formulário
    Route::get('/order/{id}/details', [OrderController::class, 'details'])->name('order.details');
    Route::post('/order/update-status', [OrderController::class, 'status'])->name('order.update_status');
    
    // Dashboard de pedidos e PIX
    Route::get('/orders-dashboard', [\App\Http\Controllers\Distributor\DistributorOrdersController::class, 'dashboard'])->name('orders.dashboard');
    Route::get('/orders/{id}/pix-qr', [\App\Http\Controllers\Distributor\DistributorOrdersController::class, 'generatePixQR'])->name('orders.pix_qr');
    Route::get('/orders/{id}/show', [\App\Http\Controllers\Distributor\DistributorOrdersController::class, 'show'])->name('orders.show');

    // Rotas de perfil
    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::get('/', [\App\Http\Controllers\Distributor\ProfileController::class, 'view'])->name('view');
        Route::post('/update', [\App\Http\Controllers\Distributor\ProfileController::class, 'update'])->name('update');
        Route::post('/settings-password', [\App\Http\Controllers\Distributor\ProfileController::class, 'settings_password_update'])->name('settings_password_update');
        Route::post('/payment-settings', [\App\Http\Controllers\Distributor\ProfileController::class, 'updatePaymentSettings'])->name('payment-settings');
        Route::post('/delivery-settings', [\App\Http\Controllers\Distributor\ProfileController::class, 'updateDeliverySettings'])->name('delivery-settings');
    });

    // Rotas de produtos/food
    Route::group(['prefix' => 'food', 'as' => 'food.'], function () {
        Route::get('/', [\App\Http\Controllers\Distributor\FoodController::class, 'index'])->name('index');
        Route::get('/list', [\App\Http\Controllers\Distributor\FoodController::class, 'list'])->name('list');
        Route::post('/store', [\App\Http\Controllers\Distributor\FoodController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [\App\Http\Controllers\Distributor\FoodController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [\App\Http\Controllers\Distributor\FoodController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [\App\Http\Controllers\Distributor\FoodController::class, 'delete'])->name('delete');
        Route::post('/status', [\App\Http\Controllers\Distributor\FoodController::class, 'status'])->name('status');
        Route::get('/get-categories', [\App\Http\Controllers\Distributor\FoodController::class, 'get_categories'])->name('get-categories');
        
        // Rotas de importação/exportação em lote
        Route::get('/bulk', [\App\Http\Controllers\Distributor\BulkProductController::class, 'form'])->name('bulk.form');
        Route::get('/bulk/template', [\App\Http\Controllers\Distributor\BulkProductController::class, 'template'])->name('bulk.template');
        Route::get('/export', [\App\Http\Controllers\Distributor\BulkProductController::class, 'export'])->name('export');
        Route::post('/bulk/upload', [\App\Http\Controllers\Distributor\BulkProductController::class, 'upload'])->name('bulk.upload');
        Route::get('/categories/export', [\App\Http\Controllers\Distributor\BulkProductController::class, 'categories'])->name('categories.export');
    });

    // Rotas para gerenciar produtos dos jornaleiros
    Route::group(['prefix' => 'vendor-products', 'as' => 'vendor-products.'], function () {
        Route::get('/', [\App\Http\Controllers\Distributor\VendorProductsController::class, 'index'])->name('index');
        Route::post('/update-stock', [\App\Http\Controllers\Distributor\VendorProductsController::class, 'updateStock'])->name('update-stock');
        Route::post('/bulk-update-stock', [\App\Http\Controllers\Distributor\VendorProductsController::class, 'bulkUpdateStock'])->name('bulk-update-stock');
    });

    // Logout do distribuidor
    Route::get('/logout', function () {
        auth('distributor')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login', ['tab' => 'distributor']);
    })->name('auth.logout');
});