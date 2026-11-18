<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MasterManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatusOverrideController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Public Website
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/{section}', function () {
    return view('welcome');
})->whereIn('section', ['about', 'features', 'market', 'process', 'portfolio', 'team', 'pricing', 'vision', 'contact']);

Route::post('/public/contact-lead', [LeadController::class, 'publicStore'])->name('public.lead.store');

// Authenticated Internal System
Route::middleware(['auth', 'verified'])->group(function () {
    // 1. Dashboard & System Flow Guide
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/workflow-guide', 'workflow-guide.index')->name('workflow-guide.index');

    // 1b. In-App System Notifications (Static routes MUST precede {notification} binding)
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::get('/notifications/{notification}/visit', [NotificationController::class, 'visit'])->name('notifications.visit');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // 2. Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

    // 3. Leads
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::post('/leads/{lead}/review', [LeadController::class, 'review'])->name('leads.review');
    Route::post('/leads/{lead}/communication', [LeadController::class, 'logCommunication'])->name('leads.communication');
    Route::post('/leads/{lead}/lost', [LeadController::class, 'markLost'])->name('leads.lost');
    Route::post('/leads/{lead}/convert-quotation', [LeadController::class, 'convertToQuotation'])->name('leads.convert-quotation');

    // 4. Quotations
    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::post('/quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
    Route::post('/quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
    Route::post('/quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    Route::post('/quotations/{quotation}/convert-order', [QuotationController::class, 'convertToOrder'])->name('quotations.convert-order');

    // 5. Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('/orders/{order}/delivery', [OrderController::class, 'recordDelivery'])->name('orders.delivery');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // 6. Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::post('/payments/{payment}/void', [PaymentController::class, 'void'])->name('payments.void');

    // 7. Projects & Tasks
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/{project}/start', [ProjectController::class, 'start'])->name('projects.start');
    Route::post('/projects/{project}/submit-qc', [ProjectController::class, 'submitQc'])->name('projects.submit-qc');
    Route::post('/projects/{project}/approve-qc', [ProjectController::class, 'approveQc'])->name('projects.approve-qc');
    Route::post('/projects/{project}/revision', [ProjectController::class, 'requestRevision'])->name('projects.revision');
    Route::post('/projects/{project}/cancel', [ProjectController::class, 'cancel'])->name('projects.cancel');

    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::post('/projects/{project}/apply-template', [ProjectController::class, 'applyTemplate'])->name('projects.apply-template');
    Route::post('/tasks/{task}/start', [TaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{task}/submit', [TaskController::class, 'submit'])->name('tasks.submit');
    Route::post('/tasks/{task}/revision', [TaskController::class, 'requestRevision'])->name('tasks.revision');
    Route::post('/tasks/{task}/approve', [TaskController::class, 'approve'])->name('tasks.approve');

    // 8. Purchases (Purchase Requests)
    Route::get('/purchases', [PurchaseRequestController::class, 'index'])->name('purchases.index');
    Route::post('/purchases', [PurchaseRequestController::class, 'store'])->name('purchases.store');
    Route::post('/purchases/{purchase}/approve', [PurchaseRequestController::class, 'approve'])->name('purchases.approve');
    Route::post('/purchases/{purchase}/reject', [PurchaseRequestController::class, 'reject'])->name('purchases.reject');
    Route::post('/purchases/{purchase}/record-purchase', [PurchaseRequestController::class, 'recordPurchase'])->name('purchases.record-purchase');
    Route::post('/purchases/{purchase}/finalize', [PurchaseRequestController::class, 'finalize'])->name('purchases.finalize');
    Route::post('/purchases/{purchase}/cancel', [PurchaseRequestController::class, 'cancel'])->name('purchases.cancel');

    // 9. Documentation
    Route::get('/documentations', [DocumentationController::class, 'index'])->name('documentations.index');
    Route::post('/documentations', [DocumentationController::class, 'store'])->name('documentations.store');
    Route::delete('/documentations/{documentation}', [DocumentationController::class, 'destroy'])->name('documentations.destroy');

    // 10. Financial Management (Buku Kas & Transaksi)
    Route::resource('transactions', TransactionController::class)->only(['index', 'store', 'destroy']);

    // 11. Monthly Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/{report}/submit', [ReportController::class, 'submitReview'])->name('reports.submit');
    Route::post('/reports/{report}/approve', [ReportController::class, 'approve'])->name('reports.approve');
    Route::post('/reports/{report}/finalize', [ReportController::class, 'finalize'])->name('reports.finalize');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // 12. Master Data & User Management (Super Admin & CEO)
    Route::get('/master-management', [MasterManagementController::class, 'index'])->name('master.index');
    Route::post('/master-management/users', [MasterManagementController::class, 'storeUser'])->name('master.users.store');
    Route::put('/master-management/users/{user}', [MasterManagementController::class, 'updateUser'])->name('master.users.update');
    Route::delete('/master-management/users/{user}', [MasterManagementController::class, 'destroyUser'])->name('master.users.destroy');

    Route::post('/master-management/services', [MasterManagementController::class, 'storeService'])->name('master.services.store');
    Route::put('/master-management/services/{service}', [MasterManagementController::class, 'updateService'])->name('master.services.update');
    Route::post('/master-management/services/{service}/toggle', [MasterManagementController::class, 'toggleService'])->name('master.services.toggle');
    Route::delete('/master-management/services/{service}', [MasterManagementController::class, 'destroyService'])->name('master.services.destroy');

    Route::delete('/master-management/customers/{customer}', [MasterManagementController::class, 'destroyCustomer'])->name('master.customers.destroy');

    // 13. Activity Logs (Audit Trail)
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // 13. Super Admin Status Override
    Route::post('/status-override', [StatusOverrideController::class, 'override'])->name('status-override');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
