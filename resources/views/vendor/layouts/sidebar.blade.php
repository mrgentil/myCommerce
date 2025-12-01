<!-- Sidebar -->
<nav id="sidebar" class="d-flex flex-column p-3">
    <div class="logo-container">
        <img src="{{ asset('storage/brands/logo-ready.png') }}" alt="{{ __('cms.sidebar.logo') }}">
    </div>
    <div class="search-container position-relative">
        <input type="text" class="form-control" placeholder="{{ __('cms.sidebar.search_placeholder') }}" id="searchInput" autocomplete="off">
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'vendor.dashboard' ? 'active' : '' }}" href="{{ route('vendor.dashboard') }}"><i class="fas fa-home me-2"></i> <span>Tableau de bord</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'vendor.shop.edit' ? 'active' : '' }}" href="{{ route('vendor.shop.edit') }}"><i class="fas fa-store me-2"></i> <span>Ma boutique</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#productMenu" role="button" aria-expanded="false" aria-controls="productMenu">
                <span><i class="fas fa-box me-2"></i> <span>{{ __('cms.sidebar.products.title') }}</span></span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="collapse {{ Route::currentRouteName() == 'vendor.products.create' || Route::currentRouteName() == 'vendor.products.index' ? 'show' : '' }}" id="productMenu">
                <ul class="nav flex-column ms-3">
                    <li><a class="nav-link {{ Route::currentRouteName() == 'vendor.products.create' ? 'active' : '' }}" href="{{ route('vendor.products.create') }}">{{ __('cms.sidebar.products.add_new') }}</a></li>
                    <li><a class="nav-link {{ Route::currentRouteName() == 'vendor.products.index' ? 'active' : '' }}" href="{{ route('vendor.products.index') }}">{{ __('cms.sidebar.products.list') }}</a></li>
                </ul>
            </div>
        </li> 
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#orderMenu" role="button" aria-expanded="false" aria-controls="orderMenu">
                <span><i class="fas fa-shopping-cart me-2"></i> <span>{{ __('cms.sidebar.orders.title') }}</span></span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="collapse {{ Route::is('vendor.orders.*') ? 'show' : '' }}" id="orderMenu">
                <ul class="nav flex-column ms-3">
                    <li>
                        <a class="nav-link {{ Route::currentRouteName() == 'vendor.orders.index' ? 'active' : '' }}" href="{{ route('vendor.orders.index') }}">
                           {{ __('cms.sidebar.orders.list') }}
                        </a>
                    </li>
                </ul>
            </div>
        </li>
          <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#vendorProductReviewMenu" role="button" aria-expanded="false" aria-controls="vendorProductReviewMenu">
                <span><i class="fas fa-star me-2"></i> <span>{{ __('cms.sidebar.product_reviews.title') }}</span></span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="collapse {{ in_array(Route::currentRouteName(), ['vendor.reviews.index']) ? 'show' : '' }}" id="vendorProductReviewMenu">
                <ul class="nav flex-column ms-3">
                    <li>
                        <a class="nav-link {{ Route::currentRouteName() == 'vendor.reviews.index' ? 'active' : '' }}" href="{{ route('vendor.reviews.index') }}">
                            {{ __('cms.sidebar.product_reviews.list') }}
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'vendor.finance.index' ? 'active' : '' }}" href="{{ route('vendor.finance.index') }}"><i class="fas fa-wallet me-2"></i> <span>Mes finances</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('vendor.messages.*') ? 'active' : '' }}" href="{{ route('vendor.messages.index') }}"><i class="fas fa-comments me-2"></i> <span>Messages</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('vendor.returns.*') ? 'active' : '' }}" href="{{ route('vendor.returns.index') }}"><i class="fas fa-undo me-2"></i> <span>Retours</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('vendor.notifications.*') ? 'active' : '' }}" href="{{ route('vendor.notifications.index') }}"><i class="fas fa-bell me-2"></i> <span>Notifications</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('vendor.questions.*') ? 'active' : '' }}" href="{{ route('vendor.questions.index') }}"><i class="fas fa-question-circle me-2"></i> <span>Questions</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'vendor.statistics.index' ? 'active' : '' }}" href="{{ route('vendor.statistics.index') }}"><i class="fas fa-chart-line me-2"></i> <span>Statistiques</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('vendor.coupons.*') ? 'active' : '' }}" href="{{ route('vendor.coupons.index') }}"><i class="fas fa-tag me-2"></i> <span>Mes coupons</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'vendor.profile.edit' ? 'active' : '' }}" href="{{ route('vendor.profile.edit') }}"><i class="fas fa-user-cog me-2"></i> <span>Mon profil</span></a>
        </li>
    </ul>
</nav>