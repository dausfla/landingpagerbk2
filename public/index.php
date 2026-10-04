<?php

/**
 * RBK Studio × RBK Konstruksi — Front Controller
 * Entry point for all HTTP requests
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RateLimitMiddleware;

// Load Environment Variables (.env)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// Error Reporting
if (env('APP_DEBUG', false)) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Initialize Router & Request
$request = new Request();
$router = new Router();

// PUBLIC ROUTES
$router->get('/', ['App\Controllers\Site\HomeController', 'index']);
$router->get('/kebijakan-privasi', ['App\Controllers\Site\PageController', 'kebijakanPrivasi']);
$router->get('/terima-kasih', ['App\Controllers\Site\LeadController', 'terimaKasih']);
$router->get('/sitemap.xml', ['App\Controllers\Site\PageController', 'sitemap']);

// PUBLIC API ROUTES (LEADS & TRACKING)
$router->post('/api/lead/step1', ['App\Controllers\Site\LeadController', 'step1'], [RateLimitMiddleware::class, CsrfMiddleware::class]);
$router->post('/api/lead/step2', ['App\Controllers\Site\LeadController', 'step2'], [CsrfMiddleware::class]);
$router->post('/api/lead/wa-click', ['App\Controllers\Site\LeadController', 'waClick']);

// STYLEGUIDE (Protected by AuthMiddleware in Production)
$router->get('/styleguide', function(Request $req) {
    include __DIR__ . '/styleguide.php';
}, [AuthMiddleware::class]);

// ADMIN AUTH ROUTES
$router->get('/admin/login', [\App\Controllers\Admin\AuthController::class, 'showLogin']);
$router->post('/admin/login', [\App\Controllers\Admin\AuthController::class, 'login'], [CsrfMiddleware::class]);
$router->get('/admin/logout', [\App\Controllers\Admin\AuthController::class, 'logout'], [AuthMiddleware::class]);

// ADMIN DASHBOARD & MODULE ROUTES
$router->get('/admin', [\App\Controllers\Admin\DashboardController::class, 'index'], [AuthMiddleware::class]);

// LEADS MANAGEMENT
$router->get('/admin/leads', [\App\Controllers\Admin\LeadAdminController::class, 'index'], [AuthMiddleware::class]);
$router->get('/admin/leads/export', [\App\Controllers\Admin\LeadAdminController::class, 'exportCsv'], [AuthMiddleware::class]);
$router->get('/admin/leads/detail/{id}', [\App\Controllers\Admin\LeadAdminController::class, 'detail'], [AuthMiddleware::class]);
$router->post('/admin/leads/update-status/{id}', [\App\Controllers\Admin\LeadAdminController::class, 'updateStatus'], [AuthMiddleware::class, CsrfMiddleware::class]);

// PACKAGES MANAGEMENT
$router->get('/admin/packages', [\App\Controllers\Admin\PackageController::class, 'index'], [AuthMiddleware::class]);
$router->get('/admin/packages/create', [\App\Controllers\Admin\PackageController::class, 'create'], [AuthMiddleware::class]);
$router->post('/admin/packages/store', [\App\Controllers\Admin\PackageController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/admin/packages/edit/{id}', [\App\Controllers\Admin\PackageController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/admin/packages/update/{id}', [\App\Controllers\Admin\PackageController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/packages/delete/{id}', [\App\Controllers\Admin\PackageController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);

// PORTFOLIOS MANAGEMENT
$router->get('/admin/portfolios', [\App\Controllers\Admin\PortfolioController::class, 'index'], [AuthMiddleware::class]);
$router->get('/admin/portfolios/create', [\App\Controllers\Admin\PortfolioController::class, 'create'], [AuthMiddleware::class]);
$router->post('/admin/portfolios/store', [\App\Controllers\Admin\PortfolioController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/admin/portfolios/edit/{id}', [\App\Controllers\Admin\PortfolioController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/admin/portfolios/update/{id}', [\App\Controllers\Admin\PortfolioController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/portfolios/delete/{id}', [\App\Controllers\Admin\PortfolioController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);

// USER MANAGEMENT
$router->get('/admin/users', [\App\Controllers\Admin\UserController::class, 'index'], [AuthMiddleware::class]);
$router->get('/admin/users/create', [\App\Controllers\Admin\UserController::class, 'create'], [AuthMiddleware::class]);
$router->post('/admin/users/store', [\App\Controllers\Admin\UserController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/admin/users/edit/{id}', [\App\Controllers\Admin\UserController::class, 'edit'], [AuthMiddleware::class]);
$router->post('/admin/users/update/{id}', [\App\Controllers\Admin\UserController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/users/delete/{id}', [\App\Controllers\Admin\UserController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);

// SYSTEM SETTINGS
$router->get('/admin/settings', [\App\Controllers\Admin\SettingsController::class, 'index'], [AuthMiddleware::class]);
$router->post('/admin/settings/update', [\App\Controllers\Admin\SettingsController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);

// AUDIT LOGS
$router->get('/admin/activity-logs', [\App\Controllers\Admin\AuditLogController::class, 'index'], [AuthMiddleware::class]);

// Dispatch Request
$router->dispatch($request);
