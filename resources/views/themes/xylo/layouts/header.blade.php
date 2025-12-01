<header>

     {{--  Wishlist Count --}}
    @php
        $wishlistCount = 0;
        if (auth('customer')->check()) {
            $wishlistCount = auth('customer')->user()->wishlistProducts()->count();
        }
    @endphp

    <div class="top-bar w-100 bg-light py-1 header-top-bar">
        <div class="text-center small">
            {{ __('store.header.top_bar_message') }} 
        </div>
    </div>  

    <div class="container py-3">
        <!-- Row 2: Logo Left / Search Right -->
        <div class="row align-items-center">
            <div class="col-md-4 col-6">
                <a href="{{ route('xylo.home') }}" class="navbar-brand">
                    <img src="https://i.ibb.co/dHx2ZR3/velstore.png" width="80" alt="Logo" />
                </a>
            </div>
            <div class="col-md-8 col-6 text-end">
                <form class="d-flex justify-content-end" action="{{ url('/search') }}" method="GET">
                    <div class="input-group search-input-width">
                        <input type="text" class="form-control" id="search-input"  name="q" placeholder="{{ __('store.header.search_placeholder') }}">
                        <button type="submit" class="btn btn-outline-secondary search-style"><i class="fa fa-search"></i></button>
                        <div id="search-suggestions" class="dropdown-menu show w-100 mt-5 d-none"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container py-3">
        <!-- Row 3: Menu Left / Actions Right -->
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav>
                    <ul class="nav">
                        @if ($headerMenu && $headerMenu->menuItems->count())
                            @foreach ($headerMenu->menuItems as $menuItem)
                                <li class="nav-item">
                                    <a class="nav-link menu-text-color" href="{{ $menuItem->slug === 'home' ? url('/') : url($menuItem->slug) }}">
                                        {{ $menuItem->translation->title ?? 'No Translation' }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                        {{-- Static menu items --}}
                        <li class="nav-item">
                            <a class="nav-link menu-text-color" href="{{ route('shop.index') }}">
                                <i class="fa fa-th-large me-1"></i>Produits
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-text-color" href="{{ route('shops.index') }}">
                                <i class="fa fa-store me-1"></i>Boutiques
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="col-md-4 d-flex justify-content-end align-items-center gap-3">
                <!-- Language Selector -->
                <form action="{{ route('change.store.language') }}" method="POST">
                    @csrf
                    <select name="lang" class="form-select form-select-sm font-style" onchange="this.form.submit()">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
                        <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>FR</option>
                        <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>ES</option>
                        <option value="de" {{ app()->getLocale() == 'de' ? 'selected' : '' }}>DE</option>
                    </select>
                </form>

                <!-- Currency Selector -->
                <form action="{{ route('change.currency') }}" method="POST">
                    @csrf
                    <select name="currency_code" class="form-select form-select-sm font-style" onchange="this.form.submit()">
                        @foreach (\App\Models\Currency::all() as $currency)
                            <option value="{{ $currency->code }}" {{ session('currency', 'USD') == $currency->code ? 'selected' : '' }}>
                                {{ strtoupper($currency->code) }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Wishlist Icon -->
                 <a href="{{ auth('customer')->check() ? route('customer.wishlist.index') : route('customer.login') }}" class="text-dark position-relative homepage-icon">
                    <i class="fa-regular fa-heart"></i>

                    @if($wishlistCount > 0)
                        <span id="wishlist-count"
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $wishlistCount }}
                        </span>
                    @endif
                </a>

                 <!-- Account Icon -->
                @php
                    $isAdmin = Auth::check() && Auth::user() instanceof \App\Models\User;
                    $isVendor = Auth::guard('vendor')->check();
                    $isCustomer = Auth::guard('customer')->check();
                @endphp
                <a href="#" class="text-dark dropdown-toggle homepage-icon" data-bs-toggle="dropdown" style="text-decoration: none;">
                    @if($isAdmin)
                        {{-- Admin User --}}
                        @php $admin = Auth::user(); @endphp
                        @if($admin->profile_image)
                            <img src="{{ asset('storage/' . $admin->profile_image) }}" alt="Admin" class="rounded-circle" style="width:32px; height:32px; object-fit:cover; border: 2px solid #dc3545;">
                        @else
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold" style="width:32px; height:32px; background-color:#dc3545; font-size:14px;">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </span>
                        @endif
                    @elseif($isVendor)
                        @php $vendor = Auth::guard('vendor')->user(); @endphp
                        @if($vendor->profile_image)
                            <img src="{{ asset('storage/' . $vendor->profile_image) }}" alt="Vendor" class="rounded-circle" style="width:32px; height:32px; object-fit:cover; border: 2px solid #667eea;">
                        @else
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold" style="width:32px; height:32px; background-color:#667eea; font-size:14px;">
                                {{ strtoupper(substr($vendor->name, 0, 2)) }}
                            </span>
                        @endif
                    @elseif($isCustomer)
                        @php $customer = Auth::guard('customer')->user(); @endphp
                        @if($customer->profile_image)
                            <img src="{{ asset('storage/' . $customer->profile_image) }}" alt="Customer" class="rounded-circle" style="width:32px; height:32px; object-fit:cover; border: 2px solid #28a745;">
                        @else
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold" style="width:32px; height:32px; background-color:#28a745; font-size:14px;">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </span>
                        @endif
                    @else
                        <i class="fa-regular fa-user"></i>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-2">
                    {{-- Admin Menu --}}
                    @if($isAdmin)
                        <li class="dropdown-header">
                            <strong>{{ Auth::user()->name }}</strong>
                            <br><small class="text-danger"><i class="fa fa-shield me-1"></i>Administrateur</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard me-2"></i>Dashboard Admin</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.products.index') }}"><i class="fa fa-box me-2"></i>Produits</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="fa fa-shopping-cart me-2"></i>Commandes</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.vendors.index') }}"><i class="fa fa-users me-2"></i>Vendeurs</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.shops.index') }}"><i class="fa fa-store me-2"></i>Boutiques</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i class="fa fa-user me-2"></i>Mon Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                            <i class="fa fa-sign-out me-2"></i>Déconnexion
                            </a>
                            <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </li>
                    {{-- Vendor Menu --}}
                    @elseif($isVendor)
                        @php $vendor = Auth::guard('vendor')->user(); @endphp
                        <li class="dropdown-header">
                            <strong>{{ $vendor->name }}</strong>
                            <br><small class="text-muted">Vendeur</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('vendor.dashboard') }}"><i class="fa fa-dashboard me-2"></i>Mon Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('vendor.products.index') }}"><i class="fa fa-box me-2"></i>Mes Produits</a></li>
                        <li><a class="dropdown-item" href="{{ route('vendor.orders.index') }}"><i class="fa fa-shopping-cart me-2"></i>Mes Commandes</a></li>
                        <li><a class="dropdown-item" href="{{ route('vendor.profile.edit') }}"><i class="fa fa-user me-2"></i>Mon Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('vendor.logout') }}"
                            onclick="event.preventDefault(); document.getElementById('vendor-logout-form').submit();">
                            <i class="fa fa-sign-out me-2"></i>Déconnexion
                            </a>
                            <form id="vendor-logout-form" action="{{ route('vendor.logout') }}" method="POST" class="d-none">@csrf</form>
                        </li>
                    {{-- Customer Menu --}}
                    @elseif($isCustomer)
                        @php $customer = Auth::guard('customer')->user(); @endphp
                        <li class="dropdown-header">
                            <strong>{{ $customer->name }}</strong>
                            <br><small class="text-muted">Client</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('customer.profile.edit') }}"><i class="fa fa-user me-2"></i>Mon profil</a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.wishlist.index') }}"><i class="fa fa-heart me-2"></i>Mes favoris</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('customer.logout') }}"
                            onclick="event.preventDefault(); document.getElementById('customer-logout-form').submit();">
                            <i class="fa fa-sign-out me-2"></i>Déconnexion
                            </a>
                            <form id="customer-logout-form" action="{{ route('customer.logout') }}" method="POST" class="d-none">@csrf</form>
                        </li>
                    {{-- Guest Menu --}}
                    @else
                        <li><a class="dropdown-item" href="{{ route('customer.login') }}"><i class="fa fa-sign-in me-2"></i>Connexion</a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.register') }}"><i class="fa fa-user-plus me-2"></i>Créer un compte</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-primary" href="{{ route('vendor.register') }}"><i class="fa fa-store me-2"></i>Devenir vendeur</a></li>
                        <li><a class="dropdown-item text-muted small" href="{{ route('vendor.login') }}"><i class="fa fa-sign-in me-2"></i>Espace vendeur</a></li>
                    @endif
                </ul>

                <!-- Cart Icon -->
                <a href="{{ route('cart.view') }}" class="text-dark position-relative homepage-icon">
                    <i class="fa fa-shopping-bag"></i>
                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ session('cart') ? collect(session('cart'))->sum('quantity') : 0 }}
                    </span>
                </a>
            </div>
        </div>
    </div>
</header>
