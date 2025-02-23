@extends('layouts.app')
@section('title', __('incoming goods report'))
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
                                            <label for="status">{{ __('status') }}: </label>
                                            <select name="status" id="status" class="form-control w-100">
                                                <option value="">-- {{ __('status') }} -- </option>
                                                <option value="0">Pending</option>
                                                <option value="1">Approved</option>
                                                <option value="2">Retur</option>
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
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="supplier_name">{{ __('supplier') }}: </label>
                                                <select name="suppliers" id="suppliers" class="form-control">
                                                    <option selected value="">--
                                                        {{ __('choose a supplier') }} --</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="item_name">{{ __('item') }}: </label>
                                                <input type="text" name="item_name" id="item_name"
                                                    class="form-control w-100">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
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
                            <div class="col-lg-6  w-100 d-flex justify-content-end align-items-center">
                                <button class="btn btn-outline-primary font-weight-bold m-1" id="print"><i
                                        class="fas fa-print m-1"></i><span
                                        class="d-none d-lg-block d-xl-inline">{{ __('Print') }}</span></button>
                                <button class="btn btn-outline-danger font-weight-bold m-1" id="export-pdf"><i
                                        class="fas fa-file-pdf m-1"></i><span
                                        class="d-none d-lg-block d-xl-inline">{{ __('messages.export-to', ['file' => 'pdf']) }}</span></button>
                                <button class="btn btn-outline-success font-weight-bold m-1" id="export-excel"><i
                                        class="fas fa-file-excel m-1"></i><span
                                        class="d-none d-lg-block d-xl-inline">{{ __('messages.export', ['file' => 'excel']) }}</span></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-tabel" width="100%"
                                class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0" width="4%">{{ __('no') }}</th>
                                        <th class="border-bottom-0">{{ __('date') }}</th>
                                        <th class="border-bottom-0">{{ __('incoming item code') }}</th>
                                        <th class="border-bottom-0">{{ __('item code') }}</th>
                                        <th class="border-bottom-0">{{ __('supplier') }}</th>
                                        <th class="border-bottom-0">{{ __('item') }}</th>
                                        <th class="border-bottom-0">{{ __('brand') }}</th>
                                        <th class="border-bottom-0">{{ __('incoming amount') }}</th>
                                        <th class="border-bottom-0">{{ __('status') }}</th>
                                    </tr>
                                </thead>
                            </table>
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
            // Define language settings
            const langID = {
                decimal: "",
                searchPlaceholder: "Cari Kode Masuk",
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

            $('#suppliers, #brands').select2({
                theme: 'bootstrap4',
                allowClear: true,
                minimumInputLength: 0 // Set this to enable search after 1 character
            });

            // Toggle untuk filter tambahan
            $('#toggle-filters').on('click', function() {
                $('#additional-filters')
                    .slideToggle(); // Animasi slide untuk menampilkan/menyembunyikan filter
            });

            const tabel = $('#data-tabel').DataTable({
                lengthChange: true,
                processing: true,
                serverSide: true,
                responsive: true,
                language: languageSettings,
                ajax: {
                    url: `{{ route('laporan.masuk.list') }}`,
                    data: function(d) {
                        d.start_date = $("input[name='start_date']").val();
                        d.end_date = $("input[name='end_date']").val();
                        d.status = $("#status").val();
                        d.item_name = $("#item_name").val();
                        d.suppliers = $("#suppliers").val();
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
                        data: "date_received",
                        name: "date_received"
                    },
                    {
                        data: "invoice_number",
                        name: "invoice_number"
                    }, {
                        data: "kode_barang",
                        name: "kode_barang"
                    },
                    {
                        data: "supplier_name",
                        name: "supplier_name"
                    }, {
                        data: "item_name",
                        name: "item_name"
                    },
                    {
                        data: "brand_name",
                        name: "brand_name"
                    },
                    {
                        data: "quantity",
                        name: "quantity"
                    },
                    {
                        data: "status",
                        name: "status"
                    }
                ],
                buttons: [{
                        extend: 'excel',
                        class: 'buttons-excel',
                        action: function(e, dt, button, config) {
                            if (dt.data().any()) {
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt,
                                    button, config);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Tidak ada data untuk diekspor!',
                                });
                            }
                        },
                        title: function() {
                            const startDate = $("input[name='start_date']").val();
                            const endDate = $("input[name='end_date']").val();
                            const today = new Date().toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });
                            const title = '{{ __('Incoming Report') }}';
                            if (startDate && endDate) {
                                return `${title} (${startDate}_{{ __('to') }}_${endDate})`;
                            }
                            return `${title} (${today})`;
                        }
                    },
                    {
                        extend: 'print',
                        class: 'buttons-print',
                        action: function(e, dt, button, config) {
                            if (dt.data().any()) {
                                $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button,
                                    config);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Tidak ada data untuk dicetak!',
                                });
                            }
                        },
                        title: function() {
                            const startDate = $("input[name='start_date']").val();
                            const endDate = $("input[name='end_date']").val();
                            const today = new Date().toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });
                            const title = '{{ __('Incoming Report') }}';
                            if (startDate && endDate) {
                                return `${title} (${startDate}_{{ __('to') }}_${endDate})`;
                            }
                            return `${title} (${today})`;
                        }
                    },
                    {
                        extend: 'pdf',
                        class: 'buttons-pdf',
                        action: function(e, dt, button, config) {
                            if (dt.data().any()) {
                                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button,
                                    config);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Tidak ada data untuk diekspor ke PDF!',
                                });
                            }
                        },
                        title: function() {
                            const startDate = $("input[name='start_date']").val();
                            const endDate = $("input[name='end_date']").val();
                            const today = new Date().toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });
                            const title = '{{ __('Incoming Report') }}';
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
    </script>
@endsection
