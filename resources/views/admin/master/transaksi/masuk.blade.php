@extends('layouts.app')
@section('title', __('incoming transaction'))
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
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="date_start">{{ __('start date') }}: </label>
                                            <input type="date" name="start_date" class="form-control w-100">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="date_start">{{ __('end date') }}: </label>
                                            <input type="date" name="end_date" class="form-control w-100">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="status">{{ __('status') }}: </label>
                                            <select name="status" id="status" class="form-control w-100">
                                                <option value="">-- {{ __('status') }} -- </option>
                                                <option value="0">Pending</option>
                                                <option value="1">Approved</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        @if (Auth::user()->role->id <= 2)
                                            <div class="form-group">
                                                <label for="date_start">{{ __('users') }}: </label>
                                                <select name="inputer" id="inputer" class="form-control w-100">
                                                    <option value="">-- {{ __('select user responsible') }} --
                                                    </option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end col-sm-4 pt-4">
                                        <div class = "d-flex justify-content-end">
                                            <button class="btn btn-primary font-weight-bold m-1 mt-1" id="filter"><i
                                                    class="fas fa-filter m-1"></i><span
                                                    class="d-none d-lg-block d-xl-inline">{{ __('filter') }}</span></button>
                                            @if (Auth::user()->role->id <= 2)
                                                <button class="btn btn-info m-1 mt-1 position-relative" type="button"
                                                    data-toggle="modal" data-target="#modal_approve"
                                                    id="modal-button-approve"><i class="fas fa-list m-1"></i><span
                                                        class="d-none d-lg-block d-xl-inline">
                                                        {{ __('approval') }}</span> <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ count($approvals) }}</span></button>
                                            @endif
                                            <button class="btn btn-success m-1 mt-1" type="button" data-toggle="modal"
                                                data-target="#TambahData" id="modal-button"><i
                                                    class="fas fa-plus m-1"></i><span class="d-none d-lg-block d-xl-inline">
                                                    {{ __('add data') }}</span> </button>
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
                    <div class="modal fade" id="TambahData" aria-labelledby="TambahDataModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="TambahDataModalLabel">
                                        {{ __('create incoming transactions') }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid"> <!-- Use container-fluid for better spacing -->
                                        <div class="row">
                                            <div class="col-md-7 col-12">
                                                <!-- Adjust column size for mobile and larger screens -->
                                                <div class="form-group">
                                                    <label for="kode" class="form-label">
                                                        {{ __('incoming item code') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="kode" readonly class="form-control">
                                                    <input type="hidden" name="id" />
                                                    <input type="hidden" name="id_barang" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="tanggal_masuk" class="form-label">
                                                        {{ __('date of entry') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" id="tanggal_masuk" name="tanggal_masuk"
                                                        class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="supplier" class="form-label">
                                                        {{ __('choose a supplier') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="supplier" id="supplier" class="form-control">
                                                        <option selected value="">--
                                                            {{ __('choose a supplier') }} --</option>
                                                        @foreach ($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-5 col-12">
                                                <!-- Adjust column size for mobile and larger screens -->
                                                <div class="form-group">
                                                    <label for="kode_barang" class="form-label">{{ __('item code') }}
                                                        <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="kode_barang" class="form-control">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-primary" type="button"
                                                                id="cari-barang"><i class="fas fa-search"></i></button>
                                                            <button class="btn btn-success" type="button"
                                                                id="barang"><i class="fas fa-box"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nama_barang"
                                                        class="form-label">{{ __('item name') }}</label>
                                                    <input type="text" name="nama_barang" id="nama_barang" readonly
                                                        class="form-control">
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 col-12"> <!-- Adjusted for responsiveness -->
                                                        <div class="form-group">
                                                            <label for="satuan_barang"
                                                                class="form-label">{{ __('unit') }}</label>
                                                            <input type="text" name="satuan_barang" readonly
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-12"> <!-- Adjusted for responsiveness -->
                                                        <div class="form-group">
                                                            <label for="jenis_barang"
                                                                class="form-label">{{ __('type') }}</label>
                                                            <input type="text" name="jenis_barang" readonly
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="jumlah"
                                                        class="form-label">{{ __('incoming amount') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="jumlah" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                        id="kembali">{{ __('cancel') }}</button>
                                    <button type="button" class="btn btn-success" id="simpan"
                                        data-action="simpan">{{ __('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Approve -->
                    <div class="modal fade" id="modal_approve" aria-labelledby="ApproveDataModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">
                                        {{ __('select transaction in') }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="data-approve" width="100%"
                                                class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">{{ __('date of entry') }}</th>
                                                        <th class="border-bottom-0">{{ __('incoming item code') }}</th>
                                                        <th class="border-bottom-0">{{ __('item code') }}</th>
                                                        <th class="border-bottom-0">{{ __('supplier') }}</th>
                                                        <th class="border-bottom-0">{{ __('brand') }}</th>
                                                        <th class="border-bottom-0">{{ __('item') }}</th>
                                                        <th class="border-bottom-0">{{ __('incoming amount') }}</th>
                                                        <th class="border-bottom-0" width="1%">{{ __('action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($approvals as $item)
                                                        <tr>
                                                            <td>
                                                                {{ $item->date_received }}
                                                            </td>
                                                            <td>
                                                                {{ $item->invoice_number }}
                                                            </td>
                                                            <td>
                                                                {{ $item->item->code }}
                                                            </td>
                                                            <td>
                                                                {{ $item->supplier->name }}
                                                            </td>
                                                            <td>
                                                                {{ $item->item->brand->name }}
                                                            </td>
                                                            <td>
                                                                {{ $item->item->name }}
                                                            </td>
                                                            <td>
                                                                {{ $item->quantity }} / {{ $item->item->unit->name }}
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-success btn-sm"
                                                                    id="approve_{{ $item->id }}">
                                                                    {{ __('Approve') }}
                                                                </button>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    id="cancel_{{ $item->id }}"
                                                                    data-user-id="{{ Auth()->id() }}"
                                                                    data-return-date="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                    data-quantity="{{ $item->quantity }}"
                                                                    data-supplier-id="{{ $item->supplier->id }}"
                                                                    data-invoice-number="{{ $item->invoice_number }}"
                                                                    data-item-id="{{ $item->item->id }}">{{ __('Cancel') }}
                                                                </button>
                                                                <script>
                                                                    // sweetalertconfirm

                                                                    $('#approve_{{ $item->id }}').on('click', function() {
                                                                        Swal.fire({
                                                                            title: "{{ __('are you sure?') }}",
                                                                            text: "{{ __('you want to approve this transaction?') }}",
                                                                            icon: "warning",
                                                                            showCancelButton: true,
                                                                            confirmButtonColor: "#3085d6",
                                                                            cancelButtonColor: "#d33",
                                                                            confirmButtonText: "{{ __('save') }}",
                                                                            cancelButtonText: "{{ __('cancel') }}"
                                                                        }).then((result) => {
                                                                            if (result.isConfirmed) {
                                                                                $.ajax({
                                                                                    url: "{{ route('transaksi.masuk.approve', $item->id) }}", // Use the route defined earlier
                                                                                    type: 'POST',
                                                                                    data: {
                                                                                        _token: '{{ csrf_token() }}' // Include CSRF token for security
                                                                                    },
                                                                                    success: function(response) {
                                                                                        if (response.success) {
                                                                                            Swal.fire(
                                                                                                '{{ __('Approved!') }}',
                                                                                                response.message,
                                                                                                'success'
                                                                                            ).then(() => {
                                                                                                location.reload();
                                                                                            });

                                                                                        } else {
                                                                                            Swal.fire(
                                                                                                '{{ __('Error!') }}',
                                                                                                response.message,
                                                                                                'error'
                                                                                            );
                                                                                        }
                                                                                    },
                                                                                    error: function() {
                                                                                        Swal.fire(
                                                                                            '{{ __('Error!') }}',
                                                                                            '{{ __('There was a problem approving the transaction.') }}',
                                                                                            'error'
                                                                                        );
                                                                                    }
                                                                                });
                                                                            };
                                                                        });
                                                                    });

                                                                    $('#cancel_{{ $item->id }}').on('click', function() {
                                                                        const button = $(this);
                                                                        const userId = button.data('user-id');
                                                                        const returnDate = button.data('return-date');
                                                                        const quantity = button.data('quantity');
                                                                        const supplierId = button.data('supplier-id');
                                                                        const invoiceNumber = button.data('invoice-number');
                                                                        const itemId = button.data('item-id');
                                                                        Swal.fire({
                                                                            title: "{{ __('are you sure?') }}",
                                                                            text: "{{ __('you want to cancel this transaction?') }}",
                                                                            icon: "warning",
                                                                            input: 'textarea',
                                                                            showCancelButton: true,
                                                                            confirmButtonColor: "#3085d6",
                                                                            cancelButtonColor: "#d33",
                                                                            confirmButtonText: "{{ __('save') }}",
                                                                            cancelButtonText: "{{ __('cancel') }}",
                                                                            preConfirm: (description) => {
                                                                                if (!description) {
                                                                                    Swal.showValidationMessage(
                                                                                        '{{ __('Please enter a description.') }}'
                                                                                    );
                                                                                }
                                                                                return description; // Return the description
                                                                            }
                                                                        }).then((result) => {
                                                                            if (result.isConfirmed) {
                                                                                const description = result.value; // Get the description value

                                                                                $.ajax({
                                                                                    url: "{{ route('transaksi.masuk.cancel', $item->id) }}", // Use the route defined earlier
                                                                                    type: 'POST',
                                                                                    data: {
                                                                                        _token: '{{ csrf_token() }}', // Include CSRF token for security
                                                                                        user_id: userId,
                                                                                        date_retur: returnDate,
                                                                                        quantity: quantity,
                                                                                        description: description,
                                                                                        supplier_id: supplierId,
                                                                                        invoice_number: invoiceNumber,
                                                                                        item_id: itemId
                                                                                    },
                                                                                    success: function(response) {
                                                                                        if (response.success) {
                                                                                            Swal.fire(
                                                                                                '{{ __('success!') }}',
                                                                                                response.message,
                                                                                                'success'
                                                                                            ).then(() => {
                                                                                                location.reload();
                                                                                            });

                                                                                        } else {
                                                                                            Swal.fire(
                                                                                                '{{ __('Error!') }}',
                                                                                                response.message,
                                                                                                'error'
                                                                                            );
                                                                                        }
                                                                                    },
                                                                                    error: function() {
                                                                                        Swal.fire(
                                                                                            '{{ __('Error!') }}',
                                                                                            '{{ __('There was a problem approving the transaction.') }}',
                                                                                            'error'
                                                                                        );
                                                                                    }
                                                                                });
                                                                            }
                                                                        })
                                                                    });
                                                                </script>
                                                            </td>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
                                        <th class="border-bottom-0" width="4%">{{ __('no') }}</th>
                                        <th class="border-bottom-0">{{ __('date of entry') }}</th>
                                        <th class="border-bottom-0">{{ __('incoming item code') }}</th>
                                        <th class="border-bottom-0">{{ __('item code') }}</th>
                                        <th class="border-bottom-0">{{ __('supplier') }}</th>
                                        <th class="border-bottom-0">{{ __('item') }}</th>
                                        <th class="border-bottom-0">{{ __('brand') }}</th>
                                        <th class="border-bottom-0">{{ __('incoming amount') }}</th>
                                        <th class="border-bottom-0">{{ __('status') }}</th>
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

        function pilih() {}

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
            const date_received = $("input[name='tanggal_masuk'").val();
            const supplier_id = $("select[name='supplier'").val();
            const invoice_number = $("input[name='kode'").val();
            const quantity = $("input[name='jumlah'").val();

            if (!item_id || !date_received || !quantity || !supplier_id) {
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
            Form.append('date_received', date_received);
            Form.append('quantity', quantity);
            Form.append('supplier_id', supplier_id);
            Form.append('invoice_number', invoice_number);
            $.ajax({
                url: `{{ route('transaksi.masuk.save') }}`,
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
                    $("select[name='supplier'").val(null);
                    $("input[name='nama_barang']").val(null);
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

        function ubah() {
            const id = $("input[name='id']").val();
            const item_id = $("input[name='id_barang']").val();
            const user_id = `{{ Auth::user()->id }}`;
            const date_received = $("input[name='tanggal_masuk'").val();
            const supplier_id = $("select[name='supplier'").val();
            const invoice_number = $("input[name='kode'").val();
            const quantity = $("input[name='jumlah'").val();
            $.ajax({
                url: `{{ route('transaksi.masuk.update') }}`,
                type: "put",
                data: {
                    id,
                    item_id,
                    user_id,
                    date_received,
                    supplier_id,
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
                    $("select[name='supplier'").val("-- {{ __('choose a supplier') }} --");
                    $("input[name='nama_barang']").val(null);
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
            $('#TambahData').on('show.bs.modal', function() {
                var today = new Date().toISOString().split('T')[0];
                $('#tanggal_masuk').val(today);
            });

            $('#supplier').select2({
                theme: 'bootstrap4',
                placeholder: "-- Pilih Pemasok--",
                allowClear: true,
                minimumInputLength: 0 // Set this to enable search after 1 character
            });

            $('#inputer').select2({
                theme: 'bootstrap4',
                allowClear: true,
                minimumInputLength: 0 // Set this to enable search after 1 character
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

            $('#data-approve').DataTable({
                lengthChange: true,
                autoWidth: false,
                responsive:true,
                language: languageSettings,
                // language: {
                //     decimal: "",
                //     searchPlaceholder: "Cari Data",
                //     emptyTable: "Tabel kosong",
                //     info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                //     infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                //     infoFiltered: "(filtered from MAX total entries)",
                //     infoPostFix: "",
                //     thousands: ",",
                //     lengthMenu: "Tampilkan _MENU_ data",
                //     loadingRecords: "Loading...",
                //     processing: "",
                //     search: "Cari:",
                //     zeroRecords: "Data tidak ditemukan",
                //     paginate: {
                //         first: "<<",
                //         last: ">>",
                //         next: ">",
                //         previous: "<",
                //     },
                //     aria: {
                //         orderable: "Order by this column",
                //         orderableReverse: "Reverse order this column",
                //     },
                // },
            });

            const tabel = $('#data-tabel').DataTable({
                lengthChange: true,
                processing: true,
                serverSide: true,
                responsive: true,
                language: languageSettings,
                ajax: {
                    url: `{{ route('transaksi.masuk.list') }}`,
                    data: function(d) {
                        d.start_date = $("input[name='start_date']").val();
                        d.end_date = $("input[name='end_date']").val();
                        d.inputer = $("#inputer").val();
                        d.status = $("#status").val();
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
                    },
                    {
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
                // console.log();
            });

            $("#modal-button").on("click", function() {
                id = new Date().getTime();
                $("input[name='kode']").val("BRGTRX-" + id);
                $("input[name='id']").val(null);
                $("input[name='id_barang']").val(null);
                $("select[name='supplier'").val("-- {{ __('choose a supplier') }} --");
                $("input[name='nama_barang']").val(null);
                $("input[name='tanggal_masuk']").val(null);
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
                url: "{{ route('transaksi.masuk.detail') }}",
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
                    $("select[name='supplier'").val(data.supplier_id).trigger('change');;
                    $("input[name='nama_barang']").val(data.nama_barang);
                    $("input[name='tanggal_masuk']").val(data.date_received);
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
                title: "{{ __('you are sure') }} ?",
                text: "{{ __('this data will be deleted') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('yes, delete') }}",
                cancelButtonText: "{{ __('no, cancel') }}!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('transaksi.masuk.delete') }}",
                        type: "delete",
                        data: {
                            id: id,
                            "_token": "{{ csrf_token() }}"
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
