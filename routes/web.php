<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InterstitialController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ShortLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

Route::get('/sitemap.xml', function () {
    $base = rtrim(config('app.url'), '/');
    $urls = [
        ['loc' => $base.'/', 'priority' => '1.0', 'freq' => 'daily'],
        ['loc' => $base.'/faq', 'priority' => '0.7', 'freq' => 'weekly'],
        ['loc' => $base.'/login', 'priority' => '0.5', 'freq' => 'monthly'],
        ['loc' => $base.'/register', 'priority' => '0.6', 'freq' => 'monthly'],
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    foreach ($urls as $u) {
        $xml .= "  <url><loc>{$u['loc']}</loc><changefreq>{$u['freq']}</changefreq><priority>{$u['priority']}</priority></url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');
Route::post('/shorten', [HomeController::class, 'shortenGuest'])
    ->middleware('throttle:10,1')
    ->name('shorten.guest');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/links/export', [ShortLinkController::class, 'export'])->name('links.export');
    Route::get('/links/bulk-create', [ShortLinkController::class, 'bulkCreate'])->name('links.bulk-create');
    Route::post('/links/bulk-create', [ShortLinkController::class, 'bulkStore'])->name('links.bulk-store');
    Route::post('/links/bulk', [ShortLinkController::class, 'bulk'])->name('links.bulk');
    Route::post('/links/{link}/clone', [ShortLinkController::class, 'clone'])->name('links.clone');
    Route::get('/links/{link}/stats', [ShortLinkController::class, 'stats'])->name('links.stats');
    Route::get('/links/{link}/qr', [ShortLinkController::class, 'qr'])->name('links.qr');
    Route::resource('links', ShortLinkController::class)->except('show');
    Route::get('/payout', [PayoutController::class, 'index'])->name('payout.index');
    Route::post('/payout', [PayoutController::class, 'store'])->name('payout.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

require __DIR__.'/auth.php';

// Redirect / interstitial routes — MUST be last so /{slug} wildcard doesn't shadow named routes.
Route::get('/{slug}', [RedirectController::class, 'show'])
    ->where('slug', '[A-Za-z0-9_-]+')->name('link.show');
Route::post('/{slug}/unlock', [RedirectController::class, 'unlock'])
    ->middleware('throttle:5,1')
    ->name('link.unlock');
Route::post('/{slug}/verify', [InterstitialController::class, 'verify'])
    ->middleware('throttle:60,1')
    ->name('link.verify');
