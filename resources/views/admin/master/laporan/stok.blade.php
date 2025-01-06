@extends('layouts.app')
@section('title', __('stock report'))
@section('content')
    <x-head-datatable />
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100">
                    <div class="card-header row">
                        <div class="row w-100">
                            <div class="col-lg-6  w-100">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="date_start">{{ __('start date') }}: </label>
                                            <input type="date" name="start_date" class="form-control w-100">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="date_start">{{ __('end date') }}: </label>
                                            <input type="date" name="end_date" class="form-control w-100">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="date_start">{{ __('supplier') }}: </label>
                                            <select name="supplier_id" id="supplier_id" class="form-control w-100">
                                                <option value="">-- {{ __('select supplier') }} --</option>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 pt-4">
                                        <div class="d-flex">
                                            <button class="btn btn-primary font-weight-bold m-1" id="toggle-filters"
                                                title="Toggle Sortir">
                                                <i class="fas fa-sliders-h"></i>
                                            </button>
                                            <button class="btn btn-primary font-weight-bold m-1 mt-1" id="filter"
                                                title="Sortir"><i class="fas fa-filter m-1"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Filter tambahan -->
                                <div id="additional-filters" style="display: none;">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="item_name">{{ __('item') }}: </label>
                                                <input type="text" name="item_name" id="item_name"
                                                    class="form-control w-100">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="brand_name">{{ __('brand') }}: </label>
                                                <select name="brands" id="brands" class="form-control">
                                                    <option selected value="">--
                                                        {{ __('pilih merk') }} --</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- @if (Auth::user()->role->name != 'staff') --}}
                            @can('super&admin')
                                <div class="col-lg-6  w-100 d-flex justify-content-end align-items-center">
                                    <button class="btn btn-outline-primary font-weight-bold m-1" id="print"><i
                                            class="fas fa-print m-1"></i><span
                                            class="d-none d-lg-block d-xl-inline">{{ __('Print') }}</span></button>
                                    <button class="btn btn-outline-danger font-weight-bold m-1" id="export-pdf"><i
                                            class="fas fa-file-pdf m-1"></i><span
                                            class="d-none d-lg-block d-xl-inline">{{ __('messages.export-to', ['file' => 'pdf']) }}</span></button>
                                    <button class="btn btn-outline-success font-weight-bold m-1" id="export-excel"><i
                                            class="fas fa-file-excel m-1"></i><span
                                            class="d-none d-lg-block d-xl-inline">{{ __('messages.export-to', ['file' => 'excel']) }}</span></button>
                                </div>
                            @endcan
                            {{-- @endif --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-tabel" width="100%"
                                class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0" width="2%">{{ __('no') }}</th>
                                        <th class="border-bottom-0">{{ __('item code') }}</th>
                                        <th class="border-bottom-0">{{ __('supplier') }}</th>
                                        <th class="border-bottom-0">{{ __('item') }}</th>
                                        <th class="border-bottom-0">{{ __('brand') }}</th>
                                        <th class="border-bottom-0">{{ __('stock amount') }}</th>
                                        <th class="border-bottom-0">{{ __('Aksi') }}</th>
                                        {{-- <th class="border-bottom-0">{{ __('first stock') }}</th>
                                        <th class="border-bottom-0">{{ __('incoming amount') }}</th>
                                        <th class="border-bottom-0">{{ __('outgoing amount') }}</th>
                                        <th class="border-bottom-0">{{ __('returned amount') }}</th>
                                        <th class="border-bottom-0">{{ __('difference') }}</th> --}}
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog"
                        aria-labelledby="detailModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel">
                                        {{ __('Detail Stok untuk Barang') }}: <span id="modal-item-code"
                                            class="text-primary"></span>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Dropdown for Unit Conversion -->
                                    <div class="form-group">
                                        <label for="unit-conversion">{{ __('Pilih Satuan Konversi') }}</label>
                                        <select id="unit-conversion" class="form-control">
                                            <option value="1">{{ __('Pilih Satuan') }}</option>
                                        </select>
                                    </div>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Kategori') }}</th>
                                                <th>{{ __('Jumlah') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ __('first stock') }}</td>
                                                <td id="modal-stok-awal"></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('incoming amount') }}</td>
                                                <td id="modal-jumlah-masuk"></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('outgoing amount') }}</td>
                                                <td id="modal-jumlah-keluar"></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('returned amount') }}</td>
                                                <td id="modal-jumlah-retur"></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('difference') }}</td>
                                                <td id="modal-jumlah-selisih"></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Total Stok') }}</th>
                                                <th id="modal-total-stock" class="text-success"></th>
                                            </tr>
                                        </tbody>
                                    </table>


                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal">{{ __('Close') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <x-data-table />
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('#supplier_id, #brands').select2({
                theme: 'bootstrap4',
                allowClear: true,
                minimumInputLength: 0 // Set this to enable search after 1 character
            });

            // Toggle untuk filter tambahan
            $('#toggle-filters').on('click', function() {
                $('#additional-filters')
                    .slideToggle(); // Animasi slide untuk menampilkan/menyembunyikan filter
            });

            // Define language settings
            const langID = {
                decimal: "",
                searchPlaceholder: "Cari Data",
                emptyTable: "Tabel kosong",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                infoPostFix: "",
                thousands: ".",
                lengthMenu: "Tampilkan _MENU_ data",
                loadingRecords: "Memuat...",
                processing: "Sedang memproses...",
                search: "Cari:",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "<<",
                    last: ">>",
                    next: ">",
                    previous: "<",
                },
                aria: {
                    orderable: "Urutkan kolom ini",
                    orderableReverse: "Urutkan kolom ini terbalik",
                },
            };

            const langEN = {
                decimal: "",
                searchPlaceholder: "Search Data",
                emptyTable: "No data available",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Show _MENU_ entries",
                loadingRecords: "Loading...",
                processing: "Processing...",
                search: "Search:",
                zeroRecords: "No matching records found",
                paginate: {
                    first: "<<",
                    last: ">>",
                    next: ">",
                    previous: "<",
                },
                aria: {
                    orderable: "Order by this column",
                    orderableReverse: "Reverse order this column",
                },
            };

            const currentLang = $('html').attr('lang');
            const languageSettings = currentLang === 'id' ? langID : langEN;

            const tabel = $('#data-tabel').DataTable({
                lengthChange: true,
                processing: true,
                serverSide: true,
                responsive: true,
                language: languageSettings,
                ajax: {
                    url: `{{ route('laporan.stok.list') }}`,
                    data: function(d) {
                        d.start_date = $("input[name='start_date']").val();
                        d.end_date = $("input[name='end_date']").val();
                        d.supplier = $("#supplier_id").val();
                        d.item_name = $("#item_name").val();
                        d.brands = $("#brands").val();
                    }
                },
                columns: [{
                        "data": null,
                        "sortable": false,
                        "className": "text-center",
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: "kode_barang",
                        name: "kode_barang"
                    },
                    {
                        data: "pemasok",
                        name: "pemasok"
                    },
                    {
                        data: "nama_barang",
                        name: "nama_barang"
                    },
                    {
                        data: "brand",
                        name: "brand"
                    },
                    {
                        data: "total",
                        name: "total"
                    },
                    {
                        data: null,
                        sortable: false,
                        className: "text-center",
                        render: function(data, type, row, meta) {
                            return `<button class="btn btn-primary btn-sm detail-btn" data-id="${row.id}">
                                {{ __('Lihat Detail') }}
                            </button>`;
                        }
                    }

                    // {
                    //     data: "stok_awal",
                    //     name: "stok_awal"
                    // },
                    // {
                    //     data: "jumlah_masuk",
                    //     name: "jumlah_masuk"
                    // },
                    // {
                    //     data: "jumlah_keluar",
                    //     name: "jumlah_keluar"
                    // },
                    // {
                    //     data: "jumlah_retur",
                    //     name: "jumlah_retur"
                    // },
                    // {
                    //     data: "jumlah_selisih",
                    //     name: "jumlah_selisih"
                    // }

                ],
                buttons: [{
                        extend: 'excel',
                        class: 'buttons-excel',
                        title: function() {
                            const startDate = $("input[name='start_date']").val();
                            const endDate = $("input[name='end_date']").val();
                            const today = new Date().toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });
                            const title = '{{ __('Report') }}';
                            if (startDate && endDate) {
                                return `${title} (${startDate}_{{ __('to') }}_${endDate})`;
                            }
                            return `${title} (${today})`;
                        }
                    },
                    {
                        extend: 'print',
                        class: 'buttons-print',
                        title: function() {
                            const startDate = $("input[name='start_date']").val();
                            const endDate = $("input[name='end_date']").val();
                            const today = new Date().toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });
                            const title = '{{ __('Report') }}';
                            if (startDate && endDate) {
                                return `${title} (${startDate}_{{ __('to') }}_${endDate})`;
                            }
                            return `${title} (${today})`;
                        }
                    },
                    {
                        extend: 'pdf',
                        class: 'buttons-pdf',
                        title: function() {
                            const startDate = $("input[name='start_date']").val();
                            const endDate = $("input[name='end_date']").val();
                            const today = new Date().toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });
                            const title = '{{ __('Report') }}';
                            if (startDate && endDate) {
                                return `${title} (${startDate}_{{ __('to') }}_${endDate})`;
                            }
                            return `${title} (${today})`;
                        }
                    }
                ]
            });

            $("#filter").on('click', function() {
                tabel.draw();
            });
            $("#print").on('click', function() {
                tabel.button(".buttons-print").trigger();
            });
            $("#export-pdf").on('click', function() {
                tabel.button(".buttons-pdf").trigger();
            });
            $("#export-excel").on('click', function() {
                tabel.button(".buttons-excel").trigger();
            });
        });

        $('#data-tabel').on('click', '.detail-btn', function() {
            const itemId = $(this).data('id'); // Dapatkan ID item

            // Fetch data detail melalui AJAX
            $.ajax({
                url: `{{ route('laporan.stok.detail') }}`,
                type: 'GET',
                data: {
                    id: itemId
                },
                success: function(response) {
                    // Isi data modal dengan response dari server
                    $('#modal-item-code').text(response.kode_barang || '-');
                    $('#modal-stok-awal').text(response.stok_awal || '0');
                    $('#modal-jumlah-masuk').text(response.jumlah_masuk || '0');
                    $('#modal-jumlah-keluar').text(response.jumlah_keluar || '0');
                    $('#modal-jumlah-retur').text(response.jumlah_retur || '0');
                    $('#modal-jumlah-selisih').text(response.jumlah_selisih || '0');
                    $('#modal-total-stock').text(response.total_stock || '0');

                    // Kosongkan dropdown konversi
                    const $unitConversion = $('#unit-conversion');
                    $unitConversion.empty();
                    $unitConversion.append(
                        `<option value="1">{{ __('Pilih Satuan') }}</option>`);

                    // Simpan data asli untuk referensi
                    const originalData = {
                        stok_awal: parseFloat(response.stok_awal) || 0,
                        jumlah_masuk: parseFloat(response.jumlah_masuk) || 0,
                        jumlah_keluar: parseFloat(response.jumlah_keluar) || 0,
                        jumlah_retur: parseFloat(response.jumlah_retur) || 0,
                        jumlah_selisih: parseFloat(response.jumlah_selisih) || 0,
                        total_stock: parseFloat(response.total_stock) || 0,
                    };

                    // Tambahkan opsi unit konversi ke dropdown
                    if (response.units && response.units.length > 0) {
                        response.units.forEach((unit, index) => {
                            $unitConversion.append(
                                `<option value="${unit.factor}">${unit.from_unit} Ke ${unit.to_unit} ( ${unit.factor} ${unit.to_unit}/${unit.from_unit} )</option>`
                            );
                        });
                    }

                    // Event listener untuk perubahan dropdown
                    $unitConversion.on('change', function() {
                        const factor = parseFloat($(this).val()) || 1;

                        // Update data jumlah berdasarkan faktor konversi
                        $('#modal-stok-awal').text((originalData.stok_awal * factor).toFixed(
                            2));
                        $('#modal-jumlah-masuk').text((originalData.jumlah_masuk * factor)
                            .toFixed(2));
                        $('#modal-jumlah-keluar').text((originalData.jumlah_keluar * factor)
                            .toFixed(2));
                        $('#modal-jumlah-retur').text((originalData.jumlah_retur * factor)
                            .toFixed(2));
                        $('#modal-jumlah-selisih').text((originalData.jumlah_selisih * factor)
                            .toFixed(2));
                        $('#modal-total-stock').text((originalData.total_stock * factor)
                            .toFixed(2));
                    });

                    // Tampilkan modal
                    $('#detailModal').modal('show');
                },
                error: function(error) {
                    console.error('Error fetching details:', error);
                    alert('{{ __('Failed to load details. Please try again later.') }}');
                },
            });
        });
    </script>
@endsection
