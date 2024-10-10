@extends('layouts.app')
@section('title', __('outbound transaction'))
@section('content')
    <x-head-datatable />
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100">
                    <div class="card-header row">
                        <div class="row w-100">
                            <div class="col-lg-12  w-100">
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
                                            @if (Auth::user()->role->id <= 2)
                                                <label for="date_start">{{ __('users') }}: </label>
                                                <select name="inputer" id="inputer" class="form-control w-100">
                                                    <option value="">-- {{ __('select user responsible') }} --
                                                    </option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end col-sm-3 pt-4">
                                        <div class = "d-flex justify-content-end">
                                            <button class="btn btn-primary font-weight-bold m-1 mt-1" id="filter"><i
                                                    class="fas fa-filter m-1"></i><span
                                                    class="d-none d-lg-block d-xl-inline">{{ __('filter') }}</span></button>
                                            <button
                                                class="btn {{ $in_status != 0 ? 'btn-success' : 'btn-danger' }} m-1 mt-1"
                                                type="button" data-toggle="modal"
                                                {{ $in_status != 0 ? 'data-target="#TambahData"' : 'data-target="alert"' }}
                                                id="modal-button"><i class="fas fa-plus m-1"></i><span
                                                    class="d-none d-lg-block d-xl-inline">
                                                    {{ __('add data') }}</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Barang -->
                    <div class="modal fade" id="modal-barang" data-backdrop="static" data-keyboard="false" tabindex="-1"
                        aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog  modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">{{ __('select items') }}</h5>
                                    <button type="button" class="close" id="close-modal-barang">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="data-barang" width="100%"
                                                class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0" width="8%">{{ __('no') }}
                                                        </th>
                                                        <th class="border-bottom-0">{{ __('photo') }}</th>
                                                        <th class="border-bottom-0">{{ __('item code') }}</th>
                                                        <th class="border-bottom-0">{{ __('name') }}</th>
                                                        <th class="border-bottom-0">{{ __('type') }}</th>
                                                        <th class="border-bottom-0">{{ __('unit') }}</th>
                                                        <th class="border-bottom-0">{{ __('brand') }}</th>
                                                        <th class="border-bottom-0">{{ __('stock amount') }}</th>
                                                        {{-- <th class="border-bottom-0">{{__('price')}}</th> --}}
                                                        <th class="border-bottom-0" width="1%">{{ __('action') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="TambahData" tabindex="-1" aria-labelledby="TambahDataModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="TambahDataModalLabel">
                                        {{ __('create an outgoing transaction') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label for="kode"
                                                    class="form-label">{{ __('outgoing item code') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="kode" readonly class="form-control">
                                                <input type="hidden" name="id" />
                                                <input type="hidden" name="id_barang" />
                                            </div>
                                            <div class="form-group">
                                                <label for="tanggal_keluar" class="form-label">{{ __('out date') }}
                                                    <span class="text-danger">*</span></label>
                                                <input type="date" id="tanggal_keluar" name="tanggal_keluar"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="kode_barang" class="form-label">{{ __('item code') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="kode_barang" class="form-control">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-primary" type="button"
                                                        id="cari-barang"><i class="fas fa-search"></i></button>
                                                    <button class="btn btn-success" type="button" id="barang"><i
                                                            class="fas fa-box"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="nama_barang" class="form-label">{{ __('item name') }}</label>
                                                <input type="text" name="nama_barang" id="nama_barang" readonly
                                                    class="form-control">
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="satuan_barang"
                                                            class="form-label">{{ __('unit') }}</label>
                                                        <input type="text" name="satuan_barang" readonly
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="jenis_barang"
                                                            class="form-label">{{ __('type') }}</label>
                                                        <input type="text" name="jenis_barang" readonly
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="jumlah" class="form-label">{{ __('outgoing amount') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="jumlah" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                        id="kembali">{{ __('cancel') }}</button>
                                    <button type="button" class="btn btn-success"
                                        id="simpan" data-action="simpan">{{ __('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-tabel" width="100%"
                                class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0" width="8%">{{ __('no') }}</th>
                                        <th class="border-bottom-0">{{ __('date') }}</th>
                                        <th class="border-bottom-0">{{ __('outgoing item code') }}</th>
                                        <th class="border-bottom-0">{{ __('item code') }}</th>
                                        <th class="border-bottom-0">{{ __('item') }}</th>
                                        <th class="border-bottom-0">{{ __('outgoing amount') }}</th>
                                        <th class="border-bottom-0" width="1%">{{ __('action') }}</th>
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

        function load() {
            $('#data-barang').DataTable({
                lengthChange: true,
                processing: true,
                serverSide: true,
                ajax: `{{ route('barang.list') }}`,
                columns: [{
                        "data": null,
                        "sortable": false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'img',
                        name: 'img'
                    }, {
                        data: 'code',
                        name: 'code'
                    }, {
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'unit_name',
                        name: 'unit_name'
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'tindakan',
                        render: function(data) {
                            const pattern = /id='(\d+)'/;
                            const matches = data.match(pattern);
                            return `<button class='pilih-data-barang btn btn-success' data-id='${matches[1]}'>{{ __('select') }}</button>`;
                        }
                    }
                ]
            }).buttons().container();
        }

        $(document).ready(function() {
            load();

            // pilih data barang
            $(document).on("click", ".pilih-data-barang", function() {
                id = $(this).data("id");
                $.ajax({
                    url: "{{ route('barang.detail') }}",
                    type: "post",
                    data: {
                        id: id,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function({
                        data
                    }) {
                        $("input[name='kode_barang']").val(data.code);
                        $("input[name='id_barang']").val(data.id);
                        $("input[name='nama_barang']").val(data.name);
                        $("input[name='satuan_barang']").val(data.unit_name);
                        $("input[name='jenis_barang']").val(data.category_name);
                        $('#modal-barang').modal('hide');
                        $('#TambahData').modal('show');
                    }
                });
            });
        });
    </script>
    <script>
        function detail() {
            const kode_barang = $("input[name='kode_barang']").val();
            $.ajax({
                url: `{{ route('barang.code') }}`,
                type: 'post',
                data: {
                    code: kode_barang
                },
                success: function({
                    data
                }) {
                    $("input[name='id_barang']").val(data.id);
                    $("input[name='nama_barang']").val(data.name);
                    $("input[name='satuan_barang']").val(data.unit_name);
                    $("input[name='jenis_barang']").val(data.category_name);
                }
            });

        }

        function simpan() {
            const item_id = $("input[name='id_barang']").val();
            const user_id = `{{ Auth::user()->id }}`;
            const date_out = $("input[name='tanggal_keluar']").val();
            // const customer_id = $("select[name='customer']").val();
            const customer_id = 1;
            const invoice_number = $("input[name='kode'").val();
            const quantity = $("input[name='jumlah'").val();

            if (!item_id || !date_out || !quantity) {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __('There is Empty Data !!') }}',
                    showConfirmButton: false,
                    imer: 1500
                });
                return;
            }

            const Form = new FormData();
            Form.append('user_id', user_id);
            Form.append('item_id', item_id);
            Form.append('date_out', date_out);
            Form.append('quantity', quantity);
            Form.append('customer_id', customer_id);
            Form.append('invoice_number', invoice_number);
            $.ajax({
                url: `{{ route('transaksi.keluar.save') }}`,
                type: "post",
                processData: false,
                contentType: false,
                dataType: 'json',
                data: Form,
                success: function(res) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#kembali').click();
                    $("input[name='id_barang']").val(null);
                    $("input[name='tanggal_keluar']").val(null);
                    $("select[name='customer']").val('-- Pilih Customer --');
                    $("input[name='nama_barang']").val(null);
                    $("input[name='kode_barang']").val(null);
                    $("select[name='jenis_barang']").val(null);
                    $("select[name='satuan_barang']").val(null);
                    $("input[name='jumlah']").val(0);
                    $('#data-tabel').DataTable().ajax.reload();
                },
                statusCode: {
                    400: function(res) {
                        const {
                            message
                        } = res.responseJSON;
                        Swal.fire({
                            position: "center",
                            icon: "warning",
                            title: "Oops...",
                            text: message,
                            showConfirmButton: false,
                            timer: 1900
                        });
                    }
                }

            }).then(() => {
                setTimeout(function() {
                    location.reload(); // Reloads the page after 1500ms
                }, 1500);
            });
        }


        function ubah() {
            const id = $("input[name='id']").val();
            const item_id = $("input[name='id_barang']").val();
            const user_id = `{{ Auth::user()->id }}`;
            const date_out = $("input[name='tanggal_keluar']").val();
            const customer_id = 1;
            const invoice_number = $("input[name='kode']").val();
            const quantity = $("input[name='jumlah'").val();
            $.ajax({
                url: `{{ route('transaksi.keluar.update') }}`,
                type: "put",
                data: {
                    id,
                    item_id,
                    user_id,
                    date_out,
                    customer_id,
                    invoice_number,
                    quantity
                },
                success: function(res) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#kembali').click();
                    $("input[name='id']").val(null);
                    $("input[name='id_barang']").val(null);
                    $("select[name='customer']").val('-- Pilih Customer --');
                    $("input[name='nama_barang']").val(null);
                    $("input[name='tanggal_keluar']").val(null);
                    $("input[name='kode_barang']").val(null);
                    $("select[name='jenis_barang']").val(null);
                    $("select[name='satuan_barang']").val(null);
                    $("input[name='jumlah']").val(0);
                    $('#data-tabel').DataTable().ajax.reload();
                },
                error: function(err) {
                    console.log(err);
                },
            }).then(() => {
                setTimeout(function() {
                    location.reload(); // Reloads the page after 1500ms
                }, 1500);
            });
        }

        $(document).ready(function() {
            $('#TambahData').on('shown.bs.modal', function() {
                var today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
                document.getElementById('tanggal_keluar').value = today; // Set the input value
            });

            $('#inputer').select2({
                theme: 'bootstrap4',
                allowClear: true,
                minimumInputLength: 0 // Set this to enable search after 1 character
            });

            const tabel = $('#data-tabel').DataTable({
                lengthChange: true,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: `{{ route('transaksi.keluar.list') }}`,
                    data: function(d) {
                        d.start_date = $("input[name='start_date']").val();
                        d.end_date = $("input[name='end_date']").val();
                        d.inputer = $("#inputer").val();
                    }
                },
                columns: [{
                        "data": null,
                        "sortable": false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: "date_out",
                        name: "date_out"
                    },
                    {
                        data: "invoice_number",
                        name: "invoice_number"
                    }, {
                        data: "kode_barang",
                        name: "kode_barang"
                    },
                    {
                        data: "item_name",
                        name: "item_name"
                    },
                    {
                        data: "quantity",
                        name: "quantity"
                    },
                    {
                        data: "tindakan",
                        name: "tindakan"
                    }
                ]
            });
            $("#barang").on("click", function() {
                $('#modal-barang').modal('show');
                $('#TambahData').modal('hide');
            });
            $("#close-modal-barang").on("click", function() {
                $('#modal-barang').modal('hide');
                $('#TambahData').modal('show');
            });
            $("#cari-barang").on("click", detail);

            $('#simpan').on('click', function() {
                var action = $(this).data('action');
                if (action === 'ubah') {
                    ubah();
                } else {
                    simpan();
                }
            });

            $("#modal-button").on("click", function() {
                if ($(this).attr('data-target') === 'alert') {
                    return Swal.fire({
                        position: "center",
                        icon: "warning",
                        title: "Oops...",
                        text: "Barang Stok Masuk Kosong",
                        showConfirmButton: false,
                        timer: 1900
                    });
                }

                $('#TambahData').modal('show');

                id = new Date().getTime();
                $("input[name='kode']").val("BRGTRX-" + id);
                $("input[name='id']").val(null);
                $("input[name='id_barang']").val(null);
                $("select[name='customer']").val('-- Pilih Customer --');
                $("input[name='nama_barang']").val(null);
                $("input[name='tanggal_keluar']").val(null);
                $("input[name='kode_barang']").val(null);
                $("input[name='jenis_barang']").val(null);
                $("input[name='satuan_barang']").val(null);
                $("input[name='jumlah']").val(null);
                $("#simpan").data('action', 'simpan');
            });

            $("#filter").on('click', function() {
                tabel.draw();
            });


        });



        $(document).on("click", ".ubah", function() {
            $("#modal-button").click();
            $("#simpan").data('action', 'ubah');
            let id = $(this).attr('id');
            $.ajax({
                url: "{{ route('transaksi.keluar.detail') }}",
                type: "post",
                data: {
                    id: id,
                },
                success: function({
                    data
                }) {
                    $("input[name='id']").val(data.id);
                    $("input[name='kode']").val(data.invoice_number);
                    $("input[name='id_barang']").val(data.id_barang);
                    $("select[name='customer']").val(data.customer_id);
                    $("input[name='nama_barang']").val(data.nama_barang);
                    $("input[name='tanggal_keluar']").val(data.date_out);
                    $("input[name='kode_barang']").val(data.kode_barang);
                    $("input[name='jenis_barang']").val(data.jenis_barang);
                    $("input[name='satuan_barang']").val(data.satuan_barang);
                    $("input[name='jumlah']").val(data.quantity);
                }
            });

        });

        $(document).on("click", ".hapus", function() {
            let id = $(this).attr('id');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success m-1",
                    cancelButton: "btn btn-danger m-1"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Anda Yakin ?",
                text: "Data Ini Akan Di Hapus",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya,Hapus",
                cancelButtonText: "Tidak, Kembali!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('transaksi.keluar.delete') }}",
                        type: "delete",
                        data: {
                            id: id
                        },
                        success: function(res) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $('#data-tabel').DataTable().ajax.reload();
                        }
                    });
                }
            });


        });
    </script>
@endsection
