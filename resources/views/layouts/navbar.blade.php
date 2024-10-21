<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <li class="nav-item d-flex justify-content-center align-items-center">
            <a class="nav-link h5 dropdown" href="#" data-target="#lang" data-toggle="dropdown" role="button">
                <div class="d-flex gap-2 align-items-center">
                    <span class="lang-icon lang-icon-{{ app()->getLocale() }}"></span>
                    <span class="ml-2 text-uppercase">{{ app()->getLocale() }}</span>
                </div>
                <div class="dropdown-menu" id="lang">
                    <ul id="lang-dropdown" class="d-flex flex-column gap-2"
                        style="max-height: 12rem;overflow-y: scroll;"></ul>
                </div>
            </a>
        </li>
        <li class="nav-item d-flex justify-content-center align-items-center">
            <a class="nav-link h5" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        @can('super&admin')
            <li class="nav-item dropdown d-flex justify-content-center align-items-center">
                <a class="nav-link h5 position-relative" href="#" id="notificationDropdown" data-toggle="dropdown"
                    role="button">
                    <i class="fas fa-solid fa-envelope"></i>
                    @if (count(getLowStockNotifCount()) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ count(getLowStockNotifCount()) }}
                        </span>
                        @endif
                    </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">{{ count(getLowStockNotifCount()) }}
                        {{ __('Low or Empty Stock Notifications') }}</span>
                    <div class="dropdown-divider"></div>
                    @foreach (Notification::getLowStockNotifGet() as $stoks)
                        <div class="dropdown-item d-flex justify-content-between mb-2">
                            <div>
                                <strong>{{ $stoks->item_code }}</strong><br>
                                <small>{{ $stoks->item_name }}</small><br>
                                <small>{{ $stoks->merk }}</small>
                            </div>
                            <span class="text-danger text-md">{{ $stoks->total_stock }}</span>
                            {{-- <span
                            class="float-right text-muted text-sm">{{ $stoks->created_at->diffForHumans() }}</span> --}}
                        </div>
                        <div class="dropdown-divider"></div>
                    @endforeach
                    <a href="{{ route('laporan.stok') }}"
                        class="dropdown-item dropdown-footer">{{ __('See Report Stok') }}</a>
                </div>
            </li>
            <li class="nav-item dropdown d-flex justify-content-center align-items-center">
                <a class="nav-link h5 position-relative" href="#" id="notificationDropdown" data-toggle="dropdown"
                    role="button">
                    <i class="fas fa-solid fa-inbox"></i>
                    @if (getGoodsInApproval()->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ getGoodsInApproval()->count() }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right">
                    <span class="dropdown-header">{{ getGoodsInApproval()->count() }}
                        {{ __('Approval Notifications') }} </span>
                    <div class="dropdown-divider"></div>
                    @foreach (getGoodsInApproval() as $approval)
                        <div class="dropdown-item d-flex justify-content-between mb-2">
                            <div>
                                <strong>{{ $approval->invoice_number }}</strong><br>
                                <small> {{ Str::limit($approval->item->name, 15, '...') }}</small><br>
                            </div>
                            <span
                                class="float-right text-muted text-sm">{{ $approval->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="dropdown-divider"></div>
                    @endforeach
                    <a href="{{  route('transaksi.masuk.approval') }}"
                        class="dropdown-item dropdown-footer">{{ __('See All Approvals') }}</a>
                </div>
            </li>
            @endcan



        <li class="nav-item dropdown" data-toggle="dropdown-navbar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex justify-content-center align-items-center">
                <div class="info font-weight-bold" style="text-transform:capitalize;">
                    <a href="{{ route('settings.profile') }}">
                        <span class="d-block" style="color:gray !important;"
                            id="user">{{ Auth::user()->name }}</span>

                </div>
                <div class="image">
                    <img src="{{ empty(Auth::user()->image) ? asset('user.png') : asset('storage/profile/' . Auth::user()->image) }}"
                        class="img-circle elevation-2"
                        style="width:100% !important;max-width:35px !important;aspect-ratio:1 !important;object-fit:cover !important;"
                        id="img_profile" alt="User Image">
                </div>
                </a>
            </div>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <a onclick="window.location.href=`{{ route('settings.profile') }}`" class="dropdown-item w-100">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <a onclick="window.location.href=`{{ route('login.delete') }}`" class="dropdown-item w-100">
                    <i class="fas fa-sign-out-alt mr-2"></i> LogOut
                </a>
            </div>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
