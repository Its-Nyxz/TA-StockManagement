  <!-- Main Sidebar Container -->
  <aside class="main-sidebar bg-blue elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('dashboard') }}" class="brand-link">
          <img src="{{ asset('icon.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
              style="opacity: .8">
          <span class="brand-text font-weight-bold">{{ config('app.name') }}</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
          <!-- Sidebar user panel (optional) -->



          <!-- Sidebar Menu -->
          <nav class="mt-2 text-capitalize">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                  data-accordion="false">
                  <li class="nav-header">{{ __('menu') }}</li>
                  <li class="nav-item">
                      <a href="{{ route('dashboard') }}" class="nav-link text-white">
                          <i class="nav-icon fas fa-tachometer-alt"></i>
                          <p>
                              {{ __('dashboard') }}
                          </p>
                      </a>
                  </li>

                  <li class="nav-item {{ request()->routeIs('barang.*') ? 'menu-open' : '' }}">
                      <a href="javascript:void(0)"
                          class="nav-link text-white {{ request()->routeIs('barang.*') ? 'active' : '' }}">
                          <i class="nav-icon fas fa-box"></i>
                          <p>
                              {{ __('master of goods') }}
                              <i class="right fas fa-angle-down"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          <li class="nav-item">
                              <a href="{{ route('barang.jenis') }}"
                                  class="nav-link text-white {{ request()->routeIs('barang.jenis') ? 'active' : '' }}">
                                  <p>{{ __('category') }}</p>
                              </a>
                          </li>
                          <li class="nav-item">
                              <a href="{{ route('barang.satuan') }}"
                                  class="nav-link text-white {{ request()->routeIs('barang.satuan') ? 'active' : '' }}">
                                  <p>{{ __('unit') }}</p>
                              </a>
                          </li>
                          <li class="nav-item">
                              <a href="{{ route('barang.merk') }}"
                                  class="nav-link text-white {{ request()->routeIs('barang.merk') ? 'active' : '' }}">
                                  <p>{{ __('brand') }}</p>
                              </a>
                          </li>
                          <li class="nav-item">
                              <a href="{{ route('barang.goods') }}"
                                  class="nav-link text-white {{ request()->routeIs('barang.goods') ? 'active' : '' }}">
                                  <p>{{ __('goods') }}</p>
                              </a>
                          </li>
                      </ul>
                  </li>
                  <!-- <li class="nav-item">
            <a href="{{ route('customer') }}" class="nav-link text-white">
              <i class="nav-icon far fa-user"></i>
              <p>
              {{ __('customer') }}
              </p>
            </a>
          </li> -->
                  <li class="nav-item">
                      <a href="{{ route('supplier') }}" class="nav-link text-white">
                          <i class="nav-icon fas fa-shipping-fast"></i>
                          <p>
                              {{ __('supplier') }}
                          </p>
                      </a>
                  </li>
                  <li class="nav-item {{ request()->routeIs('transaksi.*') ? 'menu-open' : '' }}">
                      <a href="javascript:void(0)"
                          class="nav-link text-white {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                          <i class="nav-icon fas fa-exchange-alt"></i>
                          <p>
                              {{ __('transaction') }}
                              <i class="right fas fa-angle-down"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview ">
                          <li class="nav-item">
                              <a href="{{ route('transaksi.masuk') }}"
                                  class="nav-link text-white {{ request()->routeIs('transaksi.masuk') ? 'active' : '' }}">
                                  {{-- <i class="fas fa-circle"></i> --}}
                                  <p>{{ __('incoming transaction') }}</p>
                              </a>
                          </li>
                          <li class="nav-item">
                              <a href="{{ route('transaksi.keluar') }}"
                                  class="nav-link text-white {{ request()->routeIs('transaksi.keluar') ? 'active' : '' }}">
                                  {{-- <i class="fas fa-circle"></i> --}}
                                  <p>{{ __('outbound transaction') }}</p>
                              </a>
                          </li>
                          @if (Auth::user()->role->name != 'staff')
                              <li class="nav-item">
                                  <a href="{{ route('transaksi.kembali') }}"
                                      class="nav-link text-white {{ request()->routeIs('transaksi.kembali') ? 'active' : '' }}">
                                      {{-- <i class="fas fa-circle"></i> --}}
                                      <p>{{ __('return transaction') }}</p>
                                  </a>
                              </li>
                          @endif
                      </ul>
                  </li>

                  @can('super&admin')
                      <li
                          class="nav-item {{ request()->routeIs('laporan.masuk') || request()->routeIs('laporan.keluar') || request()->routeIs('laporan.kembali') ? 'menu-open' : '' }}">
                          <a href="javascript:void(0)"
                              class="nav-link text-white {{ request()->routeIs('laporan.masuk') || request()->routeIs('laporan.keluar') || request()->routeIs('laporan.kembali') ? 'active' : '' }}">
                              <i class="nav-icon fas fa-file-signature"></i>
                              <p>
                                  {{ __('item report') }}
                                  <i class="right fas fa-angle-down"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">
                              <li class="nav-item">
                                  <a href="{{ route('laporan.masuk') }}"
                                      class="nav-link text-white {{ request()->routeIs('laporan.masuk') ? 'active' : '' }}">
                                      <p>{{ __('incoming goods report') }}</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('laporan.keluar') }}"
                                      class="nav-link text-white {{ request()->routeIs('laporan.keluar') ? 'active' : '' }}">
                                      <p>{{ __('outgoing goods report') }}</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('laporan.kembali') }}"
                                      class="nav-link text-white {{ request()->routeIs('laporan.kembali') ? 'active' : '' }}">
                                      <p>{{ __('returned goods report') }}</p>
                                  </a>
                              </li>
                          </ul>
                      </li>
                  @endcan

                  <li
                      class="nav-item {{ request()->routeIs('laporan.so') || request()->routeIs('laporan.stok') ? 'menu-open' : '' }}">
                      <a href="javascript:void(0)"
                          class="nav-link text-white {{ request()->routeIs('laporan.so') || request()->routeIs('laporan.stok') ? 'active' : '' }}">
                          <i class="nav-icon fas fa-print"></i>
                          <p>
                              {{ __('report') }}
                              <i class="right fas fa-angle-down"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          @can('super&admin')
                              <li class="nav-item">
                                  <a href="{{ route('laporan.so') }}"
                                      class="nav-link text-white {{ request()->routeIs('laporan.so') ? 'active' : '' }}">
                                      <p>{{ __('stock opname report') }}</p>
                                  </a>
                              </li>
                          @endcan

                          <li class="nav-item">
                              <a href="{{ route('laporan.stok') }}"
                                  class="nav-link text-white {{ request()->routeIs('laporan.stok') ? 'active' : '' }}">
                                  <p>{{ __('stock report') }}</p>
                              </a>
                          </li>
                      </ul>
                  </li>


                  <li class="nav-header">{{ __('others') }}</li>
                  <li class="nav-item">
                      <a href="javascript:void(0)" class="nav-link text-white">
                          <i class="nav-icon fas fa-cog"></i>
                          <p>
                              {{ __('setting') }}
                              <i class="right fas fa-angle-down"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          @can('super&admin')
                              <li class="nav-item">
                                  <a href="{{ route('settings.employee') }}" class="nav-link text-white">
                                      <i class="fas fa-regular-circle"></i>
                                      <p>{{ __('employee') }}</p>
                                  </a>
                              </li>
                          @endcan
                          <!-- <li class="nav-item">
                <a href="" class="nav-link text-white">
                <i class="fas fa-regular-circle"></i>
                  <p>web</p>
                </a>
              </li> -->
                          <li class="nav-item">
                              <a href="{{ route('settings.profile') }}" class="nav-link text-white">
                                  <i class="fas fa-regular-circle"></i>
                                  <p>{{ __('profile') }}</p>
                              </a>
                          </li>
                      </ul>
                  </li>
                  <li class="nav-item">
                      <a href="{{ route('login.delete') }}" class="nav-link text-white">
                          <i class="nav-icon fas fa-sign-out-alt"></i>
                          <p>
                              {{ __('messages.logout') }}
                          </p>
                      </a>
                  </li>
              </ul>
          </nav>
          <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
  </aside>
