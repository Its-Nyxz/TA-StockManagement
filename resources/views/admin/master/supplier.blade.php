@extends('layouts.app')
@section('title', __('supplier'))
@section('content')
    <x-head-datatable />
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100">
                    <div class="card-header row">
                        <div class="d-flex justify-content-end align-items-center w-100">
                            @if (Auth::user()->role->name != 'staff')
                                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#TambahData"
                                    id="modal-button"><i class="fas fa-plus"></i> {{ __('Add suppliers') }}</button>
                            @endif
                        </div>
                    </div>


                    <!-- Modal -->
                    <div class="modal fade" id="TambahData" tabindex="-1" aria-labelledby="TambahDataModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="TambahDataModalLabel">{{ __('adding supplier data') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true" onclick="clear()">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="name">{{ __('name') }}</label>
                                        <input type="text" class="form-control" id="name" autocomplete="off">
                                        <input type="hidden" name="id" id="id">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="phone_number">{{ __('phone number') }}</label>
                                        <input type="text" class="form-control" id="phone_number" autocomplete="off">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="address">{{ __('address') }}</label>
                                        <textarea class="form-control" id="address"></textarea>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="email">{{ __('email') }}</label>
                                        <input type="email" class="form-control" id="email" autocomplete="off">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="website">{{ __('website') }}</label>
                                        <textarea class="form-control" id="website"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                        id="kembali">Kembali</button>
                                    <button type="button" class="btn btn-success" id="simpan">Simpan</button>
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
                                        <th class="border-bottom-0" width="4%">No</th>
                                        <th class="border-bottom-0">{{ __('name') }}</th>
                                        <th class="border-bottom-0">{{ __('phone number') }}</th>
                                        <th class="border-bottom-0">{{ __('address') }}</th>
                                        <th class="border-bottom-0">{{ __('email') }}</th>
                                        <th class="border-bottom-0">{{ __('website') }}</th>
                                        @if (Auth::user()->role->name != 'staff')
                                            <th class="border-bottom-0" width="1%">{{ __('action') }}</th>
                                        @endif
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
        function isi() {
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

            $('#data-tabel').DataTable({
                lengthChange: true,
                autoWidth: false,
                processing: true,
                responsive: true,
                serverSide: true,
                language:languageSettings,
                ajax: `{{ route('supplier.list') }}`,
                columns: [{
                        "data": null,
                        "sortable": false,
                        "className": "text-center",
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number',
                    },
                    {
                        data: 'address',
                        name: 'address',
                    },
                    {
                        data: 'email',
                        name: 'email',
                        render: function(data) {
                            if (data == null) {
                                return "<span class='font-weight-bold'>-</span>";
                            }
                            return data;
                        }
                    },
                    {
                        data: 'website',
                        name: 'website',
                        render: function(data) {
                            if (data == null) {
                                return "<span class='font-weight-bold'>-</span>";
                            }
                            return data;
                        }
                    },
                    @if (Auth::user()->role->name != 'staff')
                        {
                            data: 'tindakan',
                            name: 'tindakan'
                        }
                    @endif
                ]
            }).buttons().container();
        }

        function simpan() {
            let name = $("#name").val();
            let phone_number = $("#phone_number").val();
            let address = $("#address").val();

            if (name.length == 0) {
                return Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Nama tidak boleh kosong!",
                    showConfirmButton: false,
                    timer: 1500
                });
            }

            if (phone_number.length == 0) {
                return Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Nomor Telepon tidak boleh kosong!",
                    showConfirmButton: false,
                    timer: 1500
                });
            }

            if (address.length == 0) {
                return Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Alamat tidak boleh kosong!",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            $.ajax({
                url: `{{ route('supplier.save') }}`,
                type: "post",
                data: {
                    name: $("#name").val(),
                    phone_number: $("#phone_number").val(),
                    address: $("#address").val(),
                    email: $('#email').val(),
                    website: $('#website').val(),
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
                    $('#kembali').click();
                    $("#name").val(null);
                    $("#phone_number").val(null);
                    $("#address").val(null);
                    $("#email").val(null);
                    $("#website").val(null);
                    $('#data-tabel').DataTable().ajax.reload();
                },
                error: function(err) {
                    console.log(err);
                },

            });
        }


        function ubah() {
            $.ajax({
                url: `{{ route('supplier.update') }}`,
                type: "put",
                data: {
                    id: $("#id").val(),
                    name: $("#name").val(),
                    phone_number: $("#phone_number").val(),
                    address: $("#address").val(),
                    email: $("#email").val(),
                    website: $("#website").val(),
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
                    $('#kembali').click();
                    $("#name").val(null);
                    $("#phone_number").val(null);
                    $("#address").val(null);
                    $("#email").val(null);
                    $("#website").val(null);
                    $('#data-tabel').DataTable().ajax.reload();
                    $('#simpan').text("{{ __('save') }}");
                },
                error: function(err) {
                    console.log(err.responJson.text);
                },

            });
        }

        $(document).ready(function() {
            isi();

            $('#simpan').on('click', function() {
                if ($(this).text() === "{{ __('update') }}") {
                    ubah();
                } else {
                    simpan();
                }
            });

            $("#modal-button").on("click", function() {
                $("#TambahDataModalLabel").text("{{ __('adding supplier data') }}");
                $("#name").val(null);
                $("#phone_number").val(null);
                $("#address").val(null);
                $("#email").val(null);
                $("#website").val(null);
                $("#simpan").text("{{ __('save') }}");
            });


        });



        $(document).on("click", ".ubah", function() {
            let id = $(this).attr('id');
            $("#modal-button").click();
            $("#TambahDataModalLabel").text("{{ __('changing supplier data') }}");
            $("#simpan").text("{{ __('update') }}");
            $.ajax({
                url: "{{ route('supplier.detail') }}",
                type: "post",
                data: {
                    id: id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function({
                    data
                }) {
                    $("#id").val(data.id);
                    $("#name").val(data.name);
                    $("#phone_number").val(data.phone_number);
                    $("#address").val(data.address);
                    $("#website").val(data.website);
                    $("#email").val(data.email);
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
                        url: "{{ route('supplier.delete') }}",
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
