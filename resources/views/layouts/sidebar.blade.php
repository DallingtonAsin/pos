<div class="az-sidebar bg-primary-dark">
    <div class="az-sidebar-loggedin nunito-font">
        <div class="az-img-user user-img">

            @isset(Auth::user()->image)
                <img src="{{ asset('uploads/images/' . $user_role . '/' . Auth::user()->image . '') }}" alt="">
            @endisset

            @empty(Auth::user()->image)
                <img src="{{ asset('uploads/images/' . $user_role . '/default/user.png') }}"
                    alt="{{ Auth::user()->first_name }}">
            @endempty

        </div>
        <div class="media-body pt-2 pl-1">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <h6 class="user-profile-name text-cap">
                @isset(Auth::user()->name)
                    {{ Auth::user()->first_name }}
                @endisset
                @empty(Auth::user()->name || Auth::user()->email)
                    {{ __('Guest') }}
                @endempty
            </h6>

            <span class="online text-white nunito-font pt-2">
                <i class="fa fa-circle text-success"></i>
                {{ __('online') }}
            </span>
        </div>
    </div>


    <div class="az-sidebar-body nunito-font">
        <ul class="nav">
            <li class="nav-label">Main Menu</li>


            <li class="nav-item">
                <a href="" class="nav-link with-sub"><i class="fa fa-home"></i>Dashboard</a>

                @can('isSuperAdmin')
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('users.index') }}" class="nav-sub-link">
                                <i class="fa fa-users pl-3 pr-2"></i>List of users
                            </a></li>
                    </ul>
                @endcan

                @can('isAdmin')
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('home') }}" class="nav-sub-link">Home</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('expenses.index') }}" class="nav-sub-link">Expenses</a>
                        </li>
                        <li class="nav-sub-item"><a href="{{ Route('customers.index') }}"
                                class="nav-sub-link">Customers</a></li>
                    </ul>
                @endcan

                @can('isCashier')
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('sales.index') }}" class="nav-sub-link">Sales</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('sales.debts') }}" class="nav-sub-link">Credit
                                Transactions</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('customers.with.debts') }}"
                                class="nav-sub-link">Customers with debts</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('customers.debts.payments.index') }}"
                                class="nav-sub-link">Customer debt payments</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('customers.index') }}" class="nav-sub-link">List of
                                recorded customers</a></li>
                    </ul>
                @endcan
            </li>

            @can('isAdmin')
                <li class="nav-item">
                    <a href="" class="nav-link with-sub"><i class="fa fa-database"></i>Inventory</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('stores.index') }}" class="nav-sub-link">Stores</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('stock.index') }}" class="nav-sub-link">Stock</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('purchases.index') }}"
                                class="nav-sub-link">Purchases</a></li>
                                <li class="nav-sub-item"><a href="{{ Route('taken-bottles.index') }}"
                                    class="nav-sub-link">Taken Bottles</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('damaged-stock-items.index') }}"
                                class="nav-sub-link">Damaged Stock</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('product-categories.index') }}"
                                class="nav-sub-link">Product Categories</a></li>
                    </ul>
                </li>
            @endcan


            @can('isAdmin')
                <li class="nav-item">
                    <a href="" class="nav-link with-sub"><i class="fa fa-user-circle"></i>Suppliers</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('suppliers.index') }}" class="nav-sub-link">List of
                                suppliers</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('supplier-credits.index') }}"
                                class="nav-sub-link">Supplier Credits</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('supplier-debts.index') }}"
                                class="nav-sub-link">Supplier Debts</a></li>
                    </ul>
                </li>
            @endcan

            @can('isCashier')
                <li><a href="{{ Route('pos.index') }}" class="nav-link">
                        <i class="typcn typcn-shopping-bag"></i>Point of Sale</a></li>
            @endcan

            @can('isCashier')
                <li class="nav-item">
                    <a href="" class="nav-link with-sub"><i class="fa fa-database"></i>Inventory</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('stock.index') }}" class="nav-sub-link">Stock</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('damaged-stock-items.index') }}"
                                class="nav-sub-link">Damages</a></li>
                    </ul>
                </li>
            @endcan


            @can('isSuperAdmin')
                <li class="nav-item {{ $active }}">
                    <a href="" class="nav-link with-sub">
                        <i class="fa fa-user-cog"></i>Manage Users</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('user.account.active') }}" class="nav-sub-link">
                                <i class="fa fa-user pr-2"></i>Active users</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('user.account.locked') }}" class="nav-sub-link">
                                <i class="fa fa-user pr-2"></i>Locked users</a></li>
                    </ul>
                </li>
            @endcan



            @can('isAdmin')
                <li class="nav-item">
                    <a href="" class="nav-link with-sub"><i class="fa fa-users"></i>System users</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('cashiers.home') }}"
                                class="nav-sub-link">Cashiers</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('managers.home') }}"
                                class="nav-sub-link">Managers</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('users.create') }}" class="nav-sub-link">Register
                                user</a></li>
                    </ul>
                </li>


                <li class="nav-item">
                    <a href="" class="nav-link with-sub"><i class="fa fa-shopping-cart"></i>Sales</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('sales.index') }}" class="nav-sub-link">Sales</a>
                        </li>
                        <li class="nav-sub-item"><a href="{{ Route('sales.debts') }}" class="nav-sub-link">Sales with
                                debts</a></li>
                        <li class="nav-sub-item"><a href="{{ route('m-sales') }}" class="nav-sub-link">Monthly
                                statistics</a></li>

                    </ul>
                </li>


                <li class="nav-item">
                    <a href="" class="nav-link with-sub"><i class="fa fa-chart-area"></i>Reports</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ route('top-cashiers') }}" class="nav-sub-link">Cashier
                                performance</a></li>
                        <li class="nav-sub-item"><a href="{{ route('customers.with.debts') }}"
                                class="nav-sub-link">Customer debts</a></li>
                        <li class="nav-sub-item"><a href="{{ route('customer-debt-payments.index') }}"
                                class="nav-sub-link">Customer debt payments</a></li>

                        <li class="nav-item">
                            <a href="" class="nav-link with-sub">Stock</a>
                            <ul class="nav-sub">
                                <li class="nav-sub-item"><a href="{{ route('best-selling-items') }}"
                                        class="nav-sub-link">Best selling products</a></li>
                                <li class="nav-sub-item"><a href="{{ route('low-stock', ':quantity') }}"
                                        class="nav-sub-link">
                                        Low running stock</a></li>
                            </ul>
                        </li>


                    </ul>
                </li>

                <li class="nav-item">
                    <a href="" class="nav-link with-sub"><i class="typcn typcn-chart-line"></i>Graphs</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ url('/reports') }}" class="nav-sub-link">Sales</a></li>
                        <li class="nav-sub-item"><a href="{{ url('/reports/charts/purchases') }}"
                                class="nav-sub-link">Purchases</a></li>
                    </ul>
                </li>
            @endcan

        
            @cannot('isSuperAdmin')
                <li class="nav-item">
                    <a href="" class="nav-link with-sub"><i class="typcn typcn-location"></i>Events</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ Route('events.index') }}" class="nav-sub-link">Events</a>
                        </li>
                        <li class="nav-sub-item"><a href="{{ Route('calendar.index') }}"
                                class="nav-sub-link">Calendar</a></li>
                        <li class="nav-sub-item"><a href="{{ Route('events.create') }}" class="nav-sub-link">Add
                                Event</a></li>
                    </ul>
                </li>

                @can('isAdmin')
                    <li class="nav-item">
                        <a href="" class="nav-link with-sub"><i class="typcn typcn-cloud-storage-outline"></i>
                            Audit</a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item"><a href="{{ route('logs.index') }}" class="nav-sub-link">Activity
                                    logs</a></li>
                        </ul>
                    </li>
                @endcan

                @can('isAdmin')
                    <li class="nav-item">
                        <a href="" class="nav-link with-sub"><i class="typcn typcn-cog"></i>Settings</a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item"><a href="{{ route('company.register') }}" class="nav-sub-link">Company
                                    details</a></li>
                        </ul>
                    </li>
                @endcan


                <li class="nav-item">
                    <a href="" class="nav-link with-sub"><i class="fa fa-comments"></i>Communication</a>
                    <ul class="nav-sub">
                        <li class="nav-sub-item"><a href="{{ route('mail.index') }}" class="nav-sub-link">Send Email</a>
                        </li>
                    </ul>
                </li>
            @endcannot

        </ul>
    </div>
</div>


<div class="az-content az-content-dashboard-five">

    <div class="az-header">
        <div class="container-fluid">

            <div class="az-header-left">
                <a href="" id="azSidebarToggle" class="az-header-menu-icon"><span></span></a>
            </div>

            <div class="az-header-center nunito-font">
                <h5 class="nav-label colored-icon-1">{{ $company->name }} </h5>
            </div>

            <div class="dropdown az-profile-menu">
                <a href="" class="text-decoration-none nunito-font username text-cap">
                    <span
                        class="mt-5 pr-1">{{ isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email }}</span>
                    <i class="dropdown-toggle"></i></a>
                <div class="dropdown-menu">
                    <div class="az-dropdown-header d-sm-none">
                        <a href="" class="az-header-arrow">
                            <i class="icon ion-md-arrow-back"></i>
                        </a>
                    </div>

                    <div class="az-header-profile nunito-font">
                        <div class="az-img-user ">
                            @isset(Auth::user()->image)
                                <img src="{{ asset('uploads/images/' . $user_role . '/' . Auth::user()->image . '') }}"
                                    alt="">
                            @endisset

                            @empty(Auth::user()->image)
                                <img src="{{ asset('uploads/images/' . $user_role . '/default/user.png') }}"
                                    alt="{{ Auth::user()->name }}" class="az-img-user pull-right">
                            @endempty
                        </div>
                        <div class="text-center">
                            <label
                                class="text-cap">{{ isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email }}</label>
                            <span>{{ $user_role }}</span>
                        </div>
                    </div>

                    <a href="{{ Route('profile.index') }}" class="dropdown-item"><i
                            class="typcn typcn-user-outline"></i> My Profile</a>
                    <a href="{{ route('account-settings') }}" class="dropdown-item"><i class="typcn typcn-edit"></i>
                        Edit Profile</a>
                    <a href="{{ route('logs.index') }}" class="dropdown-item"><i class="typcn typcn-time"></i>
                        Activity Logs</a>
                    <a href="{{ route('account-settings') }}" class="dropdown-item">
                        <i class="typcn typcn-cog-outline"></i> Account Settings</a>
                    <a class="dropdown-item" href="{{ route('signout') }}">
                        <i class="typcn typcn-power-outline"></i>{{ __('Sign Out') }}
                    </a>

                </div>
            </div>
        </div>
    </div>

    <!-- main content that is filled by other external sections -->
    <div class="az-content-body nunito-font">
        @yield('content')
    </div>
    @include('layouts.footer')
</div>
