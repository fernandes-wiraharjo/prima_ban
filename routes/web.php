<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laravel_example\UserManagement;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\Crm;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\layouts\CollapsedMenu;
use App\Http\Controllers\layouts\ContentNavbar;
use App\Http\Controllers\layouts\ContentNavSidebar;
use App\Http\Controllers\layouts\NavbarFull;
use App\Http\Controllers\layouts\NavbarFullSidebar;
use App\Http\Controllers\layouts\Horizontal;
use App\Http\Controllers\layouts\Vertical;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\front_pages\Landing;
use App\Http\Controllers\front_pages\Pricing;
use App\Http\Controllers\front_pages\Payment;
use App\Http\Controllers\front_pages\Checkout;
use App\Http\Controllers\front_pages\HelpCenter;
use App\Http\Controllers\front_pages\HelpCenterArticle;
use App\Http\Controllers\apps\ParentBrandController;
use App\Http\Controllers\apps\BrandController;
use App\Http\Controllers\apps\Email;
use App\Http\Controllers\apps\Chat;
use App\Http\Controllers\apps\Calendar;
use App\Http\Controllers\apps\CustomerController;
use App\Http\Controllers\apps\DeliveryOrderController;
use App\Http\Controllers\apps\Kanban;
use App\Http\Controllers\apps\EcommerceDashboard;
use App\Http\Controllers\apps\EcommerceProductList;
use App\Http\Controllers\apps\EcommerceProductAdd;
use App\Http\Controllers\apps\EcommerceProductCategory;
use App\Http\Controllers\apps\EcommerceOrderList;
use App\Http\Controllers\apps\EcommerceOrderDetails;
use App\Http\Controllers\apps\EcommerceCustomerAll;
use App\Http\Controllers\apps\EcommerceCustomerDetailsOverview;
use App\Http\Controllers\apps\EcommerceCustomerDetailsSecurity;
use App\Http\Controllers\apps\EcommerceCustomerDetailsBilling;
use App\Http\Controllers\apps\EcommerceCustomerDetailsNotifications;
use App\Http\Controllers\apps\EcommerceManageReviews;
use App\Http\Controllers\apps\EcommerceReferrals;
use App\Http\Controllers\apps\EcommerceSettingsDetails;
use App\Http\Controllers\apps\EcommerceSettingsPayments;
use App\Http\Controllers\apps\EcommerceSettingsCheckout;
use App\Http\Controllers\apps\EcommerceSettingsShipping;
use App\Http\Controllers\apps\EcommerceSettingsLocations;
use App\Http\Controllers\apps\EcommerceSettingsNotifications;
use App\Http\Controllers\apps\AcademyDashboard;
use App\Http\Controllers\apps\AcademyCourse;
use App\Http\Controllers\apps\AcademyCourseDetails;
use App\Http\Controllers\apps\LogisticsDashboard;
use App\Http\Controllers\apps\LogisticsFleet;
use App\Http\Controllers\apps\InvoiceList;
use App\Http\Controllers\apps\InvoicePreview;
use App\Http\Controllers\apps\InvoicePrint;
use App\Http\Controllers\apps\InvoiceEdit;
use App\Http\Controllers\apps\InvoiceAdd;
use App\Http\Controllers\apps\PatternController;
use App\Http\Controllers\apps\ProductController;
use App\Http\Controllers\apps\PurchaseController;
use App\Http\Controllers\apps\SaleController;
use App\Http\Controllers\apps\ServiceController;
use App\Http\Controllers\apps\SizeController;
use App\Http\Controllers\apps\StockHistoryController;
use App\Http\Controllers\apps\StockBrandController;
use App\Http\Controllers\apps\StockSizeController;
use App\Http\Controllers\apps\SupplierController;
use App\Http\Controllers\apps\TandaTerimaController;
use App\Http\Controllers\apps\UserList;
use App\Http\Controllers\apps\UserViewAccount;
use App\Http\Controllers\apps\UserViewSecurity;
use App\Http\Controllers\apps\UserViewBilling;
use App\Http\Controllers\apps\UserViewNotifications;
use App\Http\Controllers\apps\UserViewConnections;
use App\Http\Controllers\apps\AccessRoles;
use App\Http\Controllers\apps\AccessPermission;
use App\Http\Controllers\apps\UOMController;
use App\Http\Controllers\pages\UserProfile;
use App\Http\Controllers\pages\UserTeams;
use App\Http\Controllers\pages\UserProjects;
use App\Http\Controllers\pages\UserConnections;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsSecurity;
use App\Http\Controllers\pages\AccountSettingsBilling;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\Faq;
use App\Http\Controllers\pages\Pricing as PagesPricing;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\pages\MiscComingSoon;
use App\Http\Controllers\pages\MiscNotAuthorized;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\LoginCover;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\RegisterCover;
use App\Http\Controllers\authentications\RegisterMultiSteps;
use App\Http\Controllers\authentications\VerifyEmailBasic;
use App\Http\Controllers\authentications\VerifyEmailCover;
use App\Http\Controllers\authentications\ResetPasswordBasic;
use App\Http\Controllers\authentications\ResetPasswordCover;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\ForgotPasswordCover;
use App\Http\Controllers\authentications\TwoStepsBasic;
use App\Http\Controllers\authentications\TwoStepsCover;
use App\Http\Controllers\wizard_example\Checkout as WizardCheckout;
use App\Http\Controllers\wizard_example\PropertyListing;
use App\Http\Controllers\wizard_example\CreateDeal;
use App\Http\Controllers\modal\ModalExample;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\cards\CardAdvance;
use App\Http\Controllers\cards\CardStatistics;
use App\Http\Controllers\cards\CardAnalytics;
use App\Http\Controllers\cards\CardGamifications;
use App\Http\Controllers\cards\CardActions;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\extended_ui\Avatar;
use App\Http\Controllers\extended_ui\BlockUI;
use App\Http\Controllers\extended_ui\DragAndDrop;
use App\Http\Controllers\extended_ui\MediaPlayer;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\StarRatings;
use App\Http\Controllers\extended_ui\SweetAlert;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\extended_ui\TimelineBasic;
use App\Http\Controllers\extended_ui\TimelineFullscreen;
use App\Http\Controllers\extended_ui\Tour;
use App\Http\Controllers\extended_ui\Treeview;
use App\Http\Controllers\extended_ui\Misc;
use App\Http\Controllers\icons\Boxicons;
use App\Http\Controllers\icons\FontAwesome;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_elements\CustomOptions;
use App\Http\Controllers\form_elements\Editors;
use App\Http\Controllers\form_elements\FileUpload;
use App\Http\Controllers\form_elements\Picker;
use App\Http\Controllers\form_elements\Selects;
use App\Http\Controllers\form_elements\Sliders;
use App\Http\Controllers\form_elements\Switches;
use App\Http\Controllers\form_elements\Extras;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\StickyActions;
use App\Http\Controllers\form_wizard\Numbered as FormWizardNumbered;
use App\Http\Controllers\form_wizard\Icons as FormWizardIcons;
use App\Http\Controllers\form_validation\Validation;
use App\Http\Controllers\tables\Basic as TablesBasic;
use App\Http\Controllers\tables\DatatableBasic;
use App\Http\Controllers\tables\DatatableAdvanced;
use App\Http\Controllers\tables\DatatableExtensions;
use App\Http\Controllers\charts\ApexCharts;
use App\Http\Controllers\charts\ChartJs;
use App\Http\Controllers\maps\Leaflet;

// Main Page Route
Route::middleware(['auth'])->group(function () {
  // Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
  Route::get('/', [UserList::class, 'index'])->name('master-user');

  //masters
  Route::prefix('master')->group(function () {
    //users
    Route::prefix('user')->group(function () {
      Route::get('/', [UserList::class, 'index'])->name('master-user');
      Route::get('/get', [UserList::class, 'get'])->name('get-user');
      Route::get('/{id}', [UserList::class, 'getById'])->name('get-user-by-id');
      Route::post('/add', [UserList::class, 'add'])->name('add-user');
      Route::put('/{id}', [UserList::class, 'edit'])->name('edit-user');
      Route::delete('/{id}', [UserList::class, 'delete'])->name('delete-user');
    });

    //customers
    Route::prefix('customer')->group(function () {
      Route::get('/', [CustomerController::class, 'index'])->name('master-customer');
      Route::get('/get', [CustomerController::class, 'get'])->name('get-customer');
      Route::get('/{id}', [CustomerController::class, 'getById'])->name('get-customer-by-id');
      Route::post('/add', [CustomerController::class, 'add'])->name('add-customer');
      Route::put('/{id}', [CustomerController::class, 'edit'])->name('edit-customer');
      Route::delete('/{id}', [CustomerController::class, 'delete'])->name('delete-customer');
    });

    //suppliers
    Route::prefix('supplier')->group(function () {
      Route::get('/', [SupplierController::class, 'index'])->name('master-supplier');
      Route::get('/get', [SupplierController::class, 'get'])->name('get-supplier');
      Route::get('/{id}', [SupplierController::class, 'getById'])->name('get-supplier-by-id');
      Route::post('/add', [SupplierController::class, 'add'])->name('add-supplier');
      Route::put('/{id}', [SupplierController::class, 'edit'])->name('edit-supplier');
      Route::delete('/{id}', [SupplierController::class, 'delete'])->name('delete-supplier');
    });

    //brands
    Route::prefix('parent-brand')->group(function () {
      Route::get('/', [ParentBrandController::class, 'index'])->name('master-parent-brand');
      Route::get('/get', [ParentBrandController::class, 'get'])->name('get-parent-brand');
      Route::get('/{id}', [ParentBrandController::class, 'getById'])->name('get-parent-brand-by-id');
      Route::post('/add', [ParentBrandController::class, 'add'])->name('add-parent-brand');
      Route::put('/{id}', [ParentBrandController::class, 'edit'])->name('edit-parent-brand');
      Route::delete('/{id}', [ParentBrandController::class, 'delete'])->name('delete-parent-brand');
    });

    //group types
    Route::prefix('brand')->group(function () {
      Route::get('/', [BrandController::class, 'index'])->name('master-brand');
      Route::get('/get', [BrandController::class, 'get'])->name('get-brand');
      Route::get('/{id}', [BrandController::class, 'getById'])->name('get-brand-by-id');
      Route::post('/add', [BrandController::class, 'add'])->name('add-brand');
      Route::put('/{id}', [BrandController::class, 'edit'])->name('edit-brand');
      Route::delete('/{id}', [BrandController::class, 'delete'])->name('delete-brand');
    });

    //patterns
    Route::prefix('pattern')->group(function () {
      Route::get('/', [PatternController::class, 'index'])->name('master-pattern');
      Route::get('/get', [PatternController::class, 'get'])->name('get-pattern');
      Route::get('/{id}', [PatternController::class, 'getById'])->name('get-pattern-by-id');
      Route::get('/brand/{parentBrand}/{id}', [PatternController::class, 'getByBrandId'])->name(
        'get-pattern-by-brand-id'
      );
      Route::post('/add', [PatternController::class, 'add'])->name('add-pattern');
      Route::put('/{id}', [PatternController::class, 'edit'])->name('edit-pattern');
      Route::delete('/{id}', [PatternController::class, 'delete'])->name('delete-pattern');
    });

    //uom
    Route::prefix('uom')->group(function () {
      Route::get('/', [UOMController::class, 'index'])->name('master-uom');
      Route::get('/get', [UOMController::class, 'get'])->name('get-uom');
      Route::get('/{id}', [UOMController::class, 'getById'])->name('get-uom-by-id');
      Route::post('/add', [UOMController::class, 'add'])->name('add-uom');
      Route::put('/{id}', [UOMController::class, 'edit'])->name('edit-uom');
      Route::delete('/{id}', [UOMController::class, 'delete'])->name('delete-uom');
    });

    //size
    Route::prefix('size')->group(function () {
      Route::get('/', [SizeController::class, 'index'])->name('master-size');
      Route::get('/get', [SizeController::class, 'get'])->name('get-size');
      Route::get('/{id}', [SizeController::class, 'getById'])->name('get-size-by-id');
      Route::post('/add', [SizeController::class, 'add'])->name('add-size');
      Route::put('/{id}', [SizeController::class, 'edit'])->name('edit-size');
      Route::delete('/{id}', [SizeController::class, 'delete'])->name('delete-size');
    });

    //product
    Route::prefix('product')->group(function () {
      Route::get('/', [ProductController::class, 'index'])->name('master-product');
      Route::get('/get', [ProductController::class, 'get'])->name('get-product');
      Route::get('/{id}', [ProductController::class, 'getById'])->name('get-product-by-id');
      Route::post('/add', [ProductController::class, 'add'])->name('add-product');
      Route::put('/{id}', [ProductController::class, 'edit'])->name('edit-product');
      Route::delete('/{id}', [ProductController::class, 'delete'])->name('delete-product');
      Route::get('/{id}/{name}/detail', [ProductController::class, 'indexDetail'])
        ->name('master-product-detail')
        ->where('name', '.*');
      Route::get('/{id}/get-detail', [ProductController::class, 'getDetail'])->name('get-product-detail');
      Route::get('/detail/{id}', [ProductController::class, 'getDetailById'])->name('get-product-detail-by-id');
      Route::post('/detail/add', [ProductController::class, 'addProductDetail'])->name('add-product-detail');
      Route::put('/detail/{id}', [ProductController::class, 'editProductDetail'])->name('edit-product-detail');
      Route::delete('/detail/{id}', [ProductController::class, 'deleteProductDetail'])->name('delete-product-detail');
    });

    //service
    Route::prefix('service')->group(function () {
      Route::get('/', [ServiceController::class, 'index'])->name('master-service');
      Route::get('/get', [ServiceController::class, 'get'])->name('get-service');
      Route::get('/{id}', [ServiceController::class, 'getById'])->name('get-service-by-id');
      Route::post('/add', [ServiceController::class, 'add'])->name('add-service');
      Route::put('/{id}', [ServiceController::class, 'edit'])->name('edit-service');
      Route::delete('/{id}', [ServiceController::class, 'delete'])->name('delete-service');
    });
  });

  //transactions
  Route::prefix('transaction')->group(function () {
    //delivery-order
    Route::prefix('delivery-order')->group(function () {
      Route::get('/', [DeliveryOrderController::class, 'index'])->name('transaction-delivery-order');
      Route::get('/get', [DeliveryOrderController::class, 'get'])->name('get-delivery-order');
      Route::get('/get/add', [DeliveryOrderController::class, 'indexAdd'])->name('index-add-delivery-order');
      Route::get('/{id}', [DeliveryOrderController::class, 'getById'])->name('get-delivery-order-by-id');
      Route::get('/{id}/preview', [DeliveryOrderController::class, 'preview'])->name('preview-delivery-order');
      Route::get('/{id}/print', [DeliveryOrderController::class, 'print'])->name('print-delivery-order');
      Route::post('/add', [DeliveryOrderController::class, 'add'])->name('add-delivery-order');
      Route::put('/{id}', [DeliveryOrderController::class, 'edit'])->name('edit-delivery-order');
      Route::delete('/{id}', [DeliveryOrderController::class, 'delete'])->name('delete-delivery-order');
    });

    //tanda-terima
    Route::prefix('tanda-terima')->group(function () {
      Route::get('/', [TandaTerimaController::class, 'index'])->name('transaction-tanda-terima');
      Route::get('/get', [TandaTerimaController::class, 'get'])->name('get-tanda-terima');
      Route::get('/get/add', [TandaTerimaController::class, 'indexAdd'])->name('index-add-tanda-terima');
      Route::get('/{id}', [TandaTerimaController::class, 'getById'])->name('get-tanda-terima-by-id');
      Route::get('/{id}/preview', [TandaTerimaController::class, 'preview'])->name('preview-tanda-terima');
      Route::get('/{id}/print', [TandaTerimaController::class, 'print'])->name('print-tanda-terima');
      Route::post('/add', [TandaTerimaController::class, 'add'])->name('add-tanda-terima');
      Route::put('/{id}', [TandaTerimaController::class, 'edit'])->name('edit-tanda-terima');
      Route::delete('/{id}', [TandaTerimaController::class, 'delete'])->name('delete-tanda-terima');
    });

    //stock-history
    Route::prefix('stock-history')->group(function () {
      Route::get('/', [StockHistoryController::class, 'index'])->name('transaction-stock-history');
      Route::get('/get', [StockHistoryController::class, 'get'])->name('get-stock-history');
    });

    Route::prefix('stock-brand')->group(function () {
      Route::get('/', [StockBrandController::class, 'index'])->name('transaction-stock-brand');
      Route::get('/print/{brand}', [StockBrandController::class, 'print'])->name('print-stock-brand');
    });

    Route::prefix('stock-size')->group(function () {
      Route::get('/', [StockSizeController::class, 'index'])->name('transaction-stock-size');
      Route::get('/print/{size}', [StockSizeController::class, 'print'])
        ->where('size', '.*')
        ->name('print-stock-size');
    });

    //purchase
    Route::prefix('purchase')->group(function () {
      Route::get('/', [PurchaseController::class, 'index'])->name('transaction-purchase');
      Route::get('/get', [PurchaseController::class, 'get'])->name('get-purchase');
      Route::get('/{id}', [PurchaseController::class, 'getById'])->name('get-purchase-by-id');
      Route::post('/add', [PurchaseController::class, 'add'])->name('add-purchase');
      Route::put('/{id}', [PurchaseController::class, 'edit'])->name('edit-purchase');
      Route::delete('/{id}', [PurchaseController::class, 'delete'])->name('delete-purchase');
      Route::get('/{id}/detail', [PurchaseController::class, 'indexDetail'])->name('transaction-purchase-detail');
      Route::get('/{id}/get-detail', [PurchaseController::class, 'getDetail'])->name('get-purchase-detail');
      Route::get('/detail/{id}', [PurchaseController::class, 'getDetailById'])->name('get-purchase-detail-by-id');
      Route::post('/detail/add', [PurchaseController::class, 'addDetail'])->name('add-purchase-detail');
      Route::put('/detail/{id}', [PurchaseController::class, 'editDetail'])->name('edit-purchase-detail');
      Route::delete('/detail/{id}', [PurchaseController::class, 'deleteDetail'])->name('delete-purchase-detail');
    });

    //sale
    Route::prefix('sale')->group(function () {
      Route::get('/', [SaleController::class, 'index'])->name('transaction-sale');
      Route::get('/get', [SaleController::class, 'get'])->name('get-sale');
      Route::get('/get/add', [SaleController::class, 'indexAdd'])->name('index-add-sale');
      Route::get('/{id}', [SaleController::class, 'getById'])->name('get-sale-by-id');
      Route::get('/{id}/preview', [SaleController::class, 'preview'])->name('preview-sale');
      Route::get('/{id}/print', [SaleController::class, 'print'])->name('print-sale');
      Route::post('/add', [SaleController::class, 'add'])->name('add-sale');
      Route::put('/{id}', [SaleController::class, 'edit'])->name('edit-sale');
      Route::delete('/{id}', [SaleController::class, 'delete'])->name('delete-sale');
      Route::get('/get/belum-lunas', [SaleController::class, 'indexBelumLunas'])->name('transaction-sale-belum-lunas');
      Route::get('/print/belum-lunas/{idCustomer}', [SaleController::class, 'printBelumLunas'])->name(
        'print-sale-belum-lunas'
      );
      Route::get('/get/by-customer', [SaleController::class, 'indexByCustomer'])->name('transaction-sale-by-customer');
      Route::get('/print/by-customer/{idCustomer}', [SaleController::class, 'printByCustomer'])->name(
        'print-sale-by-customer'
      );
    });
  });

  Route::post('/check-invoice-no', [SaleController::class, 'checkInvoiceNo']);
});

// Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::get('/dashboard/analytics', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::get('/dashboard/crm', [Crm::class, 'index'])->name('dashboard-crm');

// locale
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

// layout
Route::get('/layouts/collapsed-menu', [CollapsedMenu::class, 'index'])->name('layouts-collapsed-menu');
Route::get('/layouts/content-navbar', [ContentNavbar::class, 'index'])->name('layouts-content-navbar');
Route::get('/layouts/content-nav-sidebar', [ContentNavSidebar::class, 'index'])->name('layouts-content-nav-sidebar');
Route::get('/layouts/navbar-full', [NavbarFull::class, 'index'])->name('layouts-navbar-full');
Route::get('/layouts/navbar-full-sidebar', [NavbarFullSidebar::class, 'index'])->name('layouts-navbar-full-sidebar');
Route::get('/layouts/horizontal', [Horizontal::class, 'index'])->name('dashboard-analytics');
Route::get('/layouts/vertical', [Vertical::class, 'index'])->name('dashboard-analytics');
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

// Front Pages
Route::get('/front-pages/landing', [Landing::class, 'index'])->name('front-pages-landing');
Route::get('/front-pages/pricing', [Pricing::class, 'index'])->name('front-pages-pricing');
Route::get('/front-pages/payment', [Payment::class, 'index'])->name('front-pages-payment');
Route::get('/front-pages/checkout', [Checkout::class, 'index'])->name('front-pages-checkout');
Route::get('/front-pages/help-center', [HelpCenter::class, 'index'])->name('front-pages-help-center');
Route::get('/front-pages/help-center-article', [HelpCenterArticle::class, 'index'])->name(
  'front-pages-help-center-article'
);

// apps
Route::get('/app/email', [Email::class, 'index'])->name('app-email');
Route::get('/app/chat', [Chat::class, 'index'])->name('app-chat');
Route::get('/app/calendar', [Calendar::class, 'index'])->name('app-calendar');
Route::get('/app/kanban', [Kanban::class, 'index'])->name('app-kanban');
Route::get('/app/ecommerce/dashboard', [EcommerceDashboard::class, 'index'])->name('app-ecommerce-dashboard');
Route::get('/app/ecommerce/product/list', [EcommerceProductList::class, 'index'])->name('app-ecommerce-product-list');
Route::get('/app/ecommerce/product/add', [EcommerceProductAdd::class, 'index'])->name('app-ecommerce-product-add');
Route::get('/app/ecommerce/product/category', [EcommerceProductCategory::class, 'index'])->name(
  'app-ecommerce-product-category'
);
Route::get('/app/ecommerce/order/list', [EcommerceOrderList::class, 'index'])->name('app-ecommerce-order-list');
Route::get('app/ecommerce/order/details', [EcommerceOrderDetails::class, 'index'])->name('app-ecommerce-order-details');
Route::get('/app/ecommerce/customer/all', [EcommerceCustomerAll::class, 'index'])->name('app-ecommerce-customer-all');
Route::get('app/ecommerce/customer/details/overview', [EcommerceCustomerDetailsOverview::class, 'index'])->name(
  'app-ecommerce-customer-details-overview'
);
Route::get('app/ecommerce/customer/details/security', [EcommerceCustomerDetailsSecurity::class, 'index'])->name(
  'app-ecommerce-customer-details-security'
);
Route::get('app/ecommerce/customer/details/billing', [EcommerceCustomerDetailsBilling::class, 'index'])->name(
  'app-ecommerce-customer-details-billing'
);
Route::get('app/ecommerce/customer/details/notifications', [
  EcommerceCustomerDetailsNotifications::class,
  'index',
])->name('app-ecommerce-customer-details-notifications');
Route::get('/app/ecommerce/manage/reviews', [EcommerceManageReviews::class, 'index'])->name(
  'app-ecommerce-manage-reviews'
);
Route::get('/app/ecommerce/referrals', [EcommerceReferrals::class, 'index'])->name('app-ecommerce-referrals');
Route::get('/app/ecommerce/settings/details', [EcommerceSettingsDetails::class, 'index'])->name(
  'app-ecommerce-settings-details'
);
Route::get('/app/ecommerce/settings/payments', [EcommerceSettingsPayments::class, 'index'])->name(
  'app-ecommerce-settings-payments'
);
Route::get('/app/ecommerce/settings/checkout', [EcommerceSettingsCheckout::class, 'index'])->name(
  'app-ecommerce-settings-checkout'
);
Route::get('/app/ecommerce/settings/shipping', [EcommerceSettingsShipping::class, 'index'])->name(
  'app-ecommerce-settings-shipping'
);
Route::get('/app/ecommerce/settings/locations', [EcommerceSettingsLocations::class, 'index'])->name(
  'app-ecommerce-settings-locations'
);
Route::get('/app/ecommerce/settings/notifications', [EcommerceSettingsNotifications::class, 'index'])->name(
  'app-ecommerce-settings-notifications'
);
Route::get('/app/academy/dashboard', [AcademyDashboard::class, 'index'])->name('app-academy-dashboard');
Route::get('/app/academy/course', [AcademyCourse::class, 'index'])->name('app-academy-course');
Route::get('/app/academy/course-details', [AcademyCourseDetails::class, 'index'])->name('app-academy-course-details');
Route::get('/app/logistics/dashboard', [LogisticsDashboard::class, 'index'])->name('app-logistics-dashboard');
Route::get('/app/logistics/fleet', [LogisticsFleet::class, 'index'])->name('app-logistics-fleet');
Route::get('/app/invoice/list', [InvoiceList::class, 'index'])->name('app-invoice-list');
Route::get('/app/invoice/preview', [InvoicePreview::class, 'index'])->name('app-invoice-preview');
Route::get('/app/invoice/print', [InvoicePrint::class, 'index'])->name('app-invoice-print');
Route::get('/app/invoice/edit', [InvoiceEdit::class, 'index'])->name('app-invoice-edit');
Route::get('/app/invoice/add', [InvoiceAdd::class, 'index'])->name('app-invoice-add');
Route::get('/app/user/list', [UserList::class, 'index'])->name('app-user-list');
Route::get('/app/user/view/account', [UserViewAccount::class, 'index'])->name('app-user-view-account');
Route::get('/app/user/view/security', [UserViewSecurity::class, 'index'])->name('app-user-view-security');
Route::get('/app/user/view/billing', [UserViewBilling::class, 'index'])->name('app-user-view-billing');
Route::get('/app/user/view/notifications', [UserViewNotifications::class, 'index'])->name(
  'app-user-view-notifications'
);
Route::get('/app/user/view/connections', [UserViewConnections::class, 'index'])->name('app-user-view-connections');
Route::get('/app/access-roles', [AccessRoles::class, 'index'])->name('app-access-roles');
Route::get('/app/access-permission', [AccessPermission::class, 'index'])->name('app-access-permission');

// pages
Route::get('/pages/profile-user', [UserProfile::class, 'index'])->name('pages-profile-user');
Route::get('/pages/profile-teams', [UserTeams::class, 'index'])->name('pages-profile-teams');
Route::get('/pages/profile-projects', [UserProjects::class, 'index'])->name('pages-profile-projects');
Route::get('/pages/profile-connections', [UserConnections::class, 'index'])->name('pages-profile-connections');
Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name(
  'pages-account-settings-account'
);
Route::get('/pages/account-settings-security', [AccountSettingsSecurity::class, 'index'])->name(
  'pages-account-settings-security'
);
Route::get('/pages/account-settings-billing', [AccountSettingsBilling::class, 'index'])->name(
  'pages-account-settings-billing'
);
Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name(
  'pages-account-settings-notifications'
);
Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name(
  'pages-account-settings-connections'
);
Route::get('/pages/faq', [Faq::class, 'index'])->name('pages-faq');
Route::get('/pages/pricing', [PagesPricing::class, 'index'])->name('pages-pricing');
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name(
  'pages-misc-under-maintenance'
);
Route::get('/pages/misc-comingsoon', [MiscComingSoon::class, 'index'])->name('pages-misc-comingsoon');
Route::get('/pages/misc-not-authorized', [MiscNotAuthorized::class, 'index'])->name('pages-misc-not-authorized');

// authentication
// Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/login', [LoginBasic::class, 'index'])->name('auth-login');
Route::post('/auth/login', [LoginBasic::class, 'doLogin'])->name('do-login');
Route::get('/logout', [LoginBasic::class, 'logout'])->name('logout');
Route::get('/auth/login-cover', [LoginCover::class, 'index'])->name('auth-login-cover');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/auth/register-cover', [RegisterCover::class, 'index'])->name('auth-register-cover');
Route::get('/auth/register-multisteps', [RegisterMultiSteps::class, 'index'])->name('auth-register-multisteps');
Route::get('/auth/verify-email-basic', [VerifyEmailBasic::class, 'index'])->name('auth-verify-email-basic');
Route::get('/auth/verify-email-cover', [VerifyEmailCover::class, 'index'])->name('auth-verify-email-cover');
Route::get('/auth/reset-password-basic', [ResetPasswordBasic::class, 'index'])->name('auth-reset-password-basic');
Route::get('/auth/reset-password-cover', [ResetPasswordCover::class, 'index'])->name('auth-reset-password-cover');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');
Route::get('/auth/forgot-password-cover', [ForgotPasswordCover::class, 'index'])->name('auth-forgot-password-cover');
Route::get('/auth/two-steps-basic', [TwoStepsBasic::class, 'index'])->name('auth-two-steps-basic');
Route::get('/auth/two-steps-cover', [TwoStepsCover::class, 'index'])->name('auth-two-steps-cover');

// wizard example
Route::get('/wizard/ex-checkout', [WizardCheckout::class, 'index'])->name('wizard-ex-checkout');
Route::get('/wizard/ex-property-listing', [PropertyListing::class, 'index'])->name('wizard-ex-property-listing');
Route::get('/wizard/ex-create-deal', [CreateDeal::class, 'index'])->name('wizard-ex-create-deal');

// modal
Route::get('/modal-examples', [ModalExample::class, 'index'])->name('modal-examples');

// cards
Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');
Route::get('/cards/advance', [CardAdvance::class, 'index'])->name('cards-advance');
Route::get('/cards/statistics', [CardStatistics::class, 'index'])->name('cards-statistics');
Route::get('/cards/analytics', [CardAnalytics::class, 'index'])->name('cards-analytics');
Route::get('/cards/gamifications', [CardGamifications::class, 'index'])->name('cards-gamifications');
Route::get('/cards/actions', [CardActions::class, 'index'])->name('cards-actions');

// User Interface
Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

// extended ui
Route::get('/extended/ui-avatar', [Avatar::class, 'index'])->name('extended-ui-avatar');
Route::get('/extended/ui-blockui', [BlockUI::class, 'index'])->name('extended-ui-blockui');
Route::get('/extended/ui-drag-and-drop', [DragAndDrop::class, 'index'])->name('extended-ui-drag-and-drop');
Route::get('/extended/ui-media-player', [MediaPlayer::class, 'index'])->name('extended-ui-media-player');
Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
Route::get('/extended/ui-star-ratings', [StarRatings::class, 'index'])->name('extended-ui-star-ratings');
Route::get('/extended/ui-sweetalert2', [SweetAlert::class, 'index'])->name('extended-ui-sweetalert2');
Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');
Route::get('/extended/ui-timeline-basic', [TimelineBasic::class, 'index'])->name('extended-ui-timeline-basic');
Route::get('/extended/ui-timeline-fullscreen', [TimelineFullscreen::class, 'index'])->name(
  'extended-ui-timeline-fullscreen'
);
Route::get('/extended/ui-tour', [Tour::class, 'index'])->name('extended-ui-tour');
Route::get('/extended/ui-treeview', [Treeview::class, 'index'])->name('extended-ui-treeview');
Route::get('/extended/ui-misc', [Misc::class, 'index'])->name('extended-ui-misc');

// icons
Route::get('/icons/boxicons', [Boxicons::class, 'index'])->name('icons-boxicons');
Route::get('/icons/font-awesome', [FontAwesome::class, 'index'])->name('icons-font-awesome');

// form elements
Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');
Route::get('/forms/custom-options', [CustomOptions::class, 'index'])->name('forms-custom-options');
Route::get('/forms/editors', [Editors::class, 'index'])->name('forms-editors');
Route::get('/forms/file-upload', [FileUpload::class, 'index'])->name('forms-file-upload');
Route::get('/forms/pickers', [Picker::class, 'index'])->name('forms-pickers');
Route::get('/forms/selects', [Selects::class, 'index'])->name('forms-selects');
Route::get('/forms/sliders', [Sliders::class, 'index'])->name('forms-sliders');
Route::get('/forms/switches', [Switches::class, 'index'])->name('forms-switches');
Route::get('/forms/extras', [Extras::class, 'index'])->name('forms-extras');

// form layouts
Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');
Route::get('/form/layouts-sticky', [StickyActions::class, 'index'])->name('form-layouts-sticky');

// form wizards
Route::get('/form/wizard-numbered', [FormWizardNumbered::class, 'index'])->name('form-wizard-numbered');
Route::get('/form/wizard-icons', [FormWizardIcons::class, 'index'])->name('form-wizard-icons');
Route::get('/form/validation', [Validation::class, 'index'])->name('form-validation');

// tables
Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');
Route::get('/tables/datatables-basic', [DatatableBasic::class, 'index'])->name('tables-datatables-basic');
Route::get('/tables/datatables-advanced', [DatatableAdvanced::class, 'index'])->name('tables-datatables-advanced');
Route::get('/tables/datatables-extensions', [DatatableExtensions::class, 'index'])->name(
  'tables-datatables-extensions'
);

// charts
Route::get('/charts/apex', [ApexCharts::class, 'index'])->name('charts-apex');
Route::get('/charts/chartjs', [ChartJs::class, 'index'])->name('charts-chartjs');

// maps
Route::get('/maps/leaflet', [Leaflet::class, 'index'])->name('maps-leaflet');

// laravel example
Route::get('/laravel/user-management', [UserManagement::class, 'UserManagement'])->name(
  'laravel-example-user-management'
);
Route::resource('/user-list', UserManagement::class);
