@extends('layouts.app')
@section('title', __('goods'))
@section('content')
    <x-head-datatable />
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100">
                    <div class="card-header row">
                        <div class="d-flex justify-content-end align-items-center w-100">
                            @can('super')
                                <button type="button" class="btn btn-primary m-1 mt-1" id="upload-modal-button"
                                    data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-file-import"></i>
                                </button>
                            @endcan
                            @can('super&admin')
                                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#TambahData"
                                    id="modal-button"><i class="fas fa-plus"></i> {{ __('Add data') }}</button>
                            @endcan
                        </div>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="TambahData" aria-labelledby="TambahDataModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="TambahDataModalLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="kode" class="form-label">{{ __('code of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="kode" readonly class="form-control">
                                                <input type="hidden" name="id" />
                                            </div>
                                            <div class="form-group">
                                                <label for="nama" class="form-label">{{ __('name of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="nama" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="jenisbarang" class="form-label">{{ __('types of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select name="jenisbarang" id="jenisbarang" class="form-control">
                                                        <option value="">-- {{ __('select category') }} --</option>
                                                        @foreach ($jenisbarang as $jb)
                                                            <option value="{{ $jb->id }}">{{ $jb->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-primary" type="button"
                                                            id="add-jenis" data-toggle="modal" data-target="#modalJenis">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="satuan" class="form-label">{{ __('unit of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select name="satuan" id="satuan" class="form-control">
                                                        <option value="">-- {{ __('select unit') }} --</option>
                                                        @foreach ($satuan as $s)
                                                            <option value="{{ $s->id }}">{{ $s->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-primary" type="button"
                                                            id="add-satuan" data-toggle="modal" data-target="#modalSatuan">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="conversion-section">
                                                <div class="form-group">
                                                    <label for="konversi_unit"
                                                        class="form-label">{{ __('Tambah Konversi Satuan Barang') }}</label>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('Dari Satuan') }}</th>
                                                                <th>{{ __('Ke Satuan') }}</th>
                                                                <th>{{ __('Jumlah Konversi') }}</th>
                                                                <th>{{ __('Aksi') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="conversion-rows">
                                                            <!-- Rows for dynamic conversion units -->
                                                        </tbody>
                                                    </table>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        id="add-conversion-row">
                                                        {{ __('Tambah Konversi') }}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="merk" class="form-label">{{ __('brand of goods') }} <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select name="merk" id="merk" class="form-control">
                                                        <option value="">-- {{ __('select brand') }} --</option>
                                                        @foreach ($merk as $m)
                                                            <option value="{{ $m->id }}">{{ $m->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-primary" type="button"
                                                            id="add-merk" data-toggle="modal" data-target="#modalMerk">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="supplier" class="form-label">{{ __('supplier of goods') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select name="supplier" id="supplier" class="form-control">
                                                        <option selected value="">--{{ __('select supplier') }} --
                                                        </option>
                                                        @foreach ($supplier as $sp)
                                                            <option value="{{ $sp->id }}">{{ $sp->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-primary" type="button"
                                                            id="add-supplier" data-toggle="modal"
                                                            data-target="#modalSupplier">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="stock_limit">Batas Bawah Stok<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="stock_limit" class="form-control"
                                                    value="{{ $item->stock_limit ?? 0 }}">
                                            </div>
                                            <div class="form-group item-count" id="item-count">
                                                <label for="harga" class="form-label">{{ __('Stok Awal') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" value="0" name="jumlah"
                                                    class="form-control">
                                            </div>
                                            <!-- <div class="form-group">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label for="harga" class="form-label">{{ __('price of goods') }} <span class="text-danger">*</span></label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="text"  id="harga" name="harga" class="form-control" placeholder="RP. 0">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div> -->
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="title" class="form-label">{{ __('photo') }}</label>
                                                <img src="{{ asset('default.png') }}" width="80%" alt="profile-user"
                                                    id="outputImg" class="text-center">
                                                <input class="form-control mt-5" id="GetFile" name="photo"
                                                    type="file" accept=".png,.jpeg,.jpg,.svg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                        id="kembali">{{ __('back') }}</button>
                                    <button type="button" class="btn btn-success" id="simpan"
                                        data-action="simpan">{{ __('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal AddJenis -->
                    <div class="modal fade" id="modalJenis" tabindex="-1" role="dialog"
                        aria-labelledby="modalJenisLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalJenisLabel">{{ __('Tambahkan Jenis') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-add-jenis">
                                        <div class="form-group">
                                            <label for="jenis-name">{{ __('Nama') }}</label>
                                            <input type="text" id="jenis-name" class="form-control"
                                                placeholder="{{ __('Masukan Nama Jenis') }}">
                                        </div>
                                        <button type="submit"
                                            class="btn btn-primary btn-block">{{ __('save') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal AddSatuan -->
                    <div class="modal fade" id="modalSatuan" tabindex="-1" role="dialog"
                        aria-labelledby="modalSatuanLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalSatuanLabel">{{ __('Tambahkan Satuan') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-add-satuan">
                                        <div class="form-group">
                                            <label for="satuan-name">{{ __('Nama') }}</label>
                                            <input type="text" id="satuan-name" class="form-control"
                                                placeholder="{{ __('Masukan Nama Satuan') }}">
                                        </div>
                                        <button type="submit"
                                            class="btn btn-primary btn-block">{{ __('save') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal AddMerk -->
                    <div class="modal fade" id="modalMerk" tabindex="-1" role="dialog"
                        aria-labelledby="modalMerkLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalMerkLabel">{{ __('Tambahkan Merk') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-add-merk">
                                        <div class="form-group">
                                            <label for="merk-name">{{ __('Nama') }}</label>
                                            <input type="text" id="merk-name" class="form-control"
                                                placeholder="{{ __('Masukan Nama Merk') }}">
                                        </div>
                                        <button type="submit"
                                            class="btn btn-primary btn-block">{{ __('save') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal AddPemasok -->
                    <div class="modal fade" id="modalSupplier" tabindex="-1" role="dialog"
                        aria-labelledby="modalSupplierLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalSupplierLabel">{{ __('Tambahkan Pemasok') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="form-add-supplier">
                                        <div class="form-group">
                                            <label for="supplier-name">{{ __('Nama') }}</label>
                                            <input type="text" id="supplier-name" class="form-control"
                                                placeholder="{{ __('Masukan Nama Pemasok') }}">
                                            <label for="supplier-name">{{ __('Alamat') }}</label>
                                            <textarea name="" id="supplier-addres" class="form-control" cols="30" rows="2"
                                                placeholder="{{ __('Masukan Alamat Pemasok') }}"></textarea>
                                            <label for="supplier-nohp">{{ __('No Hp') }}</label>
                                            <input type="number" id="supplier-nohp" class="form-control"
                                                placeholder="{{ __('Masukan No HP Pemasok') }}">
                                        </div>
                                        <button type="submit"
                                            class="btn btn-primary btn-block">{{ __('save') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Import-->
                    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadModalLabel">Import Barang</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('barang.import') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="file">{{ __('Choose File') }}</label>
                                            <input type="file" name="file" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-2">{{ __('Import') }}</button>
                                    </form>
                                    <hr>
                                    <a href="{{ route('barang.template') }}"
                                        class="btn btn-success mt-2">{{ __('Download Template') }}</a>
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
                                        <th class="border-bottom-0">{{ __('photo') }}</th>
                                        <th class="border-bottom-0">{{ __('code') }}</th>
                                        <th class="border-bottom-0">{{ __('variant name') }}</th>
                                        <th class="border-bottom-0">{{ __('type') }}</th>
                                        <th class="border-bottom-0">{{ __('unit') }}</th>
                                        <th class="border-bottom-0">{{ __('brand') }}</th>
                                        <th class="border-bottom-0">{{ __('supplier') }}</th>
                                        <th class="border-bottom-0">{{ __('initial stock') }}</th>
                                        <th class="border-bottom-0">{{ __('minim') }}</th>
                                        <!-- <th class="border-bottom-0">{{ __('price') }}</th> -->
                                        {{-- @if (Auth::user()->role->name != 'staff') --}}
                                        <th class="border-bottom-0">{{ __('Konversi') }}</th>
                                        @can('super&admin')
                                            <th class="border-bottom-0" width="1%">{{ __('action') }}</th>
                                        @endcan
                                        {{-- @endif --}}
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


        function harga() {
            this.value = formatIDR(this.value.replace(/[^0-9.]/g, ''));
        }


        function formatIDR(angka) {
            // Ubah angka menjadi string dan hapus simbol yang tidak diperlukan
            var strAngka = angka.toString().replace(/[^0-9]/g, '');

            // Jika tidak ada angka yang tersisa, kembalikan string kosong
            if (!strAngka) return '';

            // Pisahkan angka menjadi bagian yang sesuai dengan ribuan
            var parts = strAngka.split('.');
            var intPart = parts[0];
            var decPart = parts.length > 1 ? '.' + parts[1] : '';

            // Tambahkan pemisah ribuan
            intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Tambahkan simbol IDR
            return 'RP.' + intPart + decPart;
        }

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
                processing: true,
                responsive: true,
                serverSide: true,
                language: languageSettings,
                ajax: `{{ route('barang.list') }}`,
                columns: [{
                        "data": null,
                        "sortable": false,
                        "className": "text-center",
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
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'stock_limit',
                        name: 'stock_limit'
                    },
                    // {
                    //     data:'price',
                    //     name:'price'
                    // },
                    {
                        data: 'conversions',
                        name: 'conversions',
                        render: function(data) {
                            // Data backend sudah dirender sebagai HTML, tidak perlu memproses ulang
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
            const name = $("input[name='nama']").val();
            const code = $("input[name='kode']").val();
            const stock_limit = $("input[name='stock_limit']").val();
            const image = $("#GetFile")[0].files;
            const category_id = $("select[name='jenisbarang']").val();
            const unit_id = $("select[name='satuan']").val();
            const brand_id = $("select[name='merk']").val();
            const supplier_id = $("select[name='supplier']").val();
            // const price = $("input[name='harga']").val();
            // return console.log({name,code,category_id,unit_id,brand_id,price,quantity});
            const Form = new FormData();
            Form.append('image', image[0]);
            Form.append('code', code);
            Form.append('name', name);
            Form.append('stock_limit', stock_limit);
            Form.append('category_id', category_id);
            Form.append('unit_id', unit_id);
            Form.append('brand_id', brand_id);
            Form.append('supplier_id', supplier_id);
            // Handle conversions
            const fromUnits = $("select[name='from_unit[]']").map(function() {
                return $(this).val();
            }).get();

            const toUnits = $("select[name='to_unit[]']").map(function() {
                return $(this).val();
            }).get();

            const factors = $("input[name='conversion_factor[]']").map(function() {
                return $(this).val();
            }).get();

            fromUnits.forEach((fromUnit, index) => {
                Form.append(`conversions[${index}][from_unit_id]`, fromUnit);
                Form.append(`conversions[${index}][to_unit_id]`, toUnits[index]);
                Form.append(`conversions[${index}][conversion_factor]`, factors[index]);
            });

            // console.log([...Form.entries()]);
            // Form.append('price', price);
            if (name.length == 0 || category_id.length == 0 || unit_id.length == 0 || brand_id.length == 0 || supplier_id
                .length == 0 || stock_limit.length == 0) {
                return Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Bertanda * Tidak Boleh Kosong !",
                    showConfirmButton: false,
                    imer: 1500
                });
            }
            // if(price.length == 0){
            //     return Swal.fire({
            //         position: "center",
            //         icon: "warning",
            //         title: "harga tidak boleh kosong !",
            //         showConfirmButton: false,
            //         imer: 1500
            //     });
            // }
            $.ajax({
                url: `{{ route('barang.save') }}`,
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
                    $("input[name='nama']").val(null);
                    $("input[name='kode']").val(null);
                    $("input[name='stock_limit']").val(null);
                    $("#GetFile")[0].files = null;
                    $("select[name='jenisbarang']").val(null);
                    $("select[name='satuan']").val(null);
                    $("select[name='merk']").val(null);
                    $("select[name='supplier']").val(null);
                    $("input[name='jumlah']").val(0);
                    // $("input[name='harga']").val(null);
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
            const name = $("input[name='nama']").val();
            const stock_limit = $("input[name='stock_limit']").val();
            const code = $("input[name='kode']").val();
            const image = $("#GetFile")[0].files;
            const category_id = $("select[name='jenisbarang']").val();
            const unit_id = $("select[name='satuan']").val();
            const brand_id = $("select[name='merk']").val();
            const supplier_id = $("select[name='supplier']").val();
            // const price = $("input[name='harga']").val();
            const quantity = $("input[name='jumlah']").val();
            // return console.log({name,code,category_id,unit_id,brand_id,price,quantity});
            const Form = new FormData();
            Form.append('id', $("input[name='id']").val());
            Form.append('image', image[0]);
            Form.append('code', code);
            Form.append('name', name);
            Form.append('category_id', category_id);
            Form.append('unit_id', unit_id);
            Form.append('brand_id', brand_id);
            Form.append('supplier_id', supplier_id);
            Form.append('quantity', quantity);
            Form.append('stock_limit', stock_limit);

            // Tambahkan data konversi
            $("select[name='from_unit[]']").each(function(index, element) {
                Form.append(`from_unit[${index}]`, $(element).val());
            });

            $("select[name='to_unit[]']").each(function(index, element) {
                Form.append(`to_unit[${index}]`, $(element).val());
            });

            $("input[name='conversion_factor[]']").each(function(index, element) {
                Form.append(`conversion_factor[${index}]`, $(element).val());
            });

            // Form.append('price', price);
            $.ajax({
                url: `{{ route('barang.update') }}`,
                type: "post",
                contentType: false,
                processData: false,
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
                    $("input[name='id']").val(null);
                    $("input[name='nama']").val(null);
                    $("input[name='stock_limit']").val(null);
                    $("input[name='kode']").val(null);
                    $("#GetFile").val(null);
                    $("select[name='jenisbarang']").val(null);
                    $("select[name='satuan']").val(null);
                    $("select[name='merk']").val(null);
                    $("select[name='supplier']").val(null);
                    $("input[name='jumlah']").val(0);
                    // $("input[name='harga']").val(null);
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
            $("#harga").on("input", harga);
            isi();

            $('#jenisbarang, #satuan, #merk, #supplier').select2({
                theme: 'bootstrap4',
                allowClear: true,
                minimumInputLength: 0 // Set this to enable search after 1 character
            });

            $('#simpan').on('click', function() {
                var action = $(this).data('action');
                if (action === 'ubah') {
                    ubah();
                } else {
                    simpan();
                }
            });

            // Tambah Jenis Barang
            $('#form-add-jenis').on('submit', function(e) {
                e.preventDefault();
                let name = $('#jenis-name').val();

                $.ajax({
                    url: `{{ route('barang.jenis.store') }}`, // Route menuju controller
                    type: 'POST',
                    data: {
                        name: name,
                        _token: $('meta[name="csrf-token"]').attr('content') // Token CSRF Laravel
                    },
                    success: function(response) {
                        if (response.success) {
                            // Tambahkan data ke dropdown
                            const newOption = new Option(response.data.name, response.data.id,
                                true, true);
                            $('#jenisbarang').append(newOption).val(response.data.id).trigger(
                                'change');
                            $('#modalJenis').modal('hide');
                            $('#jenis-name').val(''); // Bersihkan input

                            // Tampilkan notifikasi SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Berhasil') }}',
                                text: '{{ __('Berhasil menambahkan Jenis Barang baru!') }}',
                                timer: 3000, // Durasi dalam milidetik
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON.message,
                            timer: 3000, // Durasi dalam milidetik
                            showConfirmButton: false
                        });
                    }
                });
            });
            // Tambah Satuan Barang
            $('#form-add-satuan').on('submit', function(e) {
                e.preventDefault();
                let name = $('#satuan-name').val();

                $.ajax({
                    url: `{{ route('barang.satuan.store') }}`, // Route menuju controller
                    type: 'POST',
                    data: {
                        name: name,
                        _token: $('meta[name="csrf-token"]').attr('content') // Token CSRF Laravel
                    },
                    success: function(response) {
                        if (response.success) {
                            // Tambahkan data ke dropdown
                            const newOption = new Option(response.data.name, response.data.id,
                                true, true);
                            $('#satuan').append(newOption).val(response.data.id).trigger(
                                'change');
                            $('#modalSatuan').modal('hide');
                            $('#satuan-name').val(''); // Bersihkan input

                            // Tampilkan notifikasi SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Berhasil') }}',
                                text: '{{ __('Berhasil menambahkan Satuan Barang baru!') }}',
                                timer: 3000, // Durasi dalam milidetik
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON.message,
                            timer: 3000, // Durasi dalam milidetik
                            showConfirmButton: false
                        });
                    }
                });
            });
            // Tambah Merk Barang
            $('#form-add-merk').on('submit', function(e) {
                e.preventDefault();
                let name = $('#merk-name').val();

                $.ajax({
                    url: `{{ route('barang.merk.store') }}`, // Route menuju controller
                    type: 'POST',
                    data: {
                        name: name,
                        _token: $('meta[name="csrf-token"]').attr('content') // Token CSRF Laravel
                    },
                    success: function(response) {
                        if (response.success) {
                            // Tambahkan data ke dropdown
                            const newOption = new Option(response.data.name, response.data.id,
                                true, true);
                            $('#merk').append(newOption).val(response.data.id).trigger(
                                'change');
                            $('#modalMerk').modal('hide');
                            $('#merk-name').val(''); // Bersihkan input

                            // Tampilkan notifikasi SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Berhasil') }}',
                                text: '{{ __('Berhasil menambahkan Merk Barang baru!') }}',
                                timer: 3000, // Durasi dalam milidetik
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON.message,
                            timer: 3000, // Durasi dalam milidetik
                            showConfirmButton: false
                        });
                    }
                });
            });
            // Tambah Supplier Barang
            $('#form-add-supplier').on('submit', function(e) {
                e.preventDefault();
                let name = $('#supplier-name').val();
                let address = $('#supplier-addres').val();
                let phone_number = $('#supplier-nohp').val();

                $.ajax({
                    url: `{{ route('suppliers.store') }}`, // Route menuju controller
                    type: 'POST',
                    data: {
                        name: name,
                        address: address,
                        phone_number: phone_number,
                        _token: $('meta[name="csrf-token"]').attr('content') // Token CSRF Laravel
                    },

                    success: function(response) {
                        if (response.success) {
                            // Tambahkan data ke dropdown
                            const newOption = new Option(response.data.name, response.data.id,
                                true, true);
                            $('#supplier').append(newOption).val(response.data.id).trigger(
                                'change');
                            $('#modalSupplier').modal('hide');
                            $('#supplier-name').val(''); // Bersihkan input
                            $('#supplier-addres').val(''); // Bersihkan input
                            $('#supplier-nohp').val(''); // Bersihkan input

                            // Tampilkan notifikasi SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Berhasil') }}',
                                text: '{{ __('Berhasil menambahkan Pemasok Barang baru!') }}',
                                timer: 3000, // Durasi dalam milidetik
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error') }}',
                            text: xhr.responseJSON.message,
                            timer: 3000, // Durasi dalam milidetik
                            showConfirmButton: false
                        });
                    }
                });
            });
            // Awalnya sembunyikan #conversion-section
            $("#conversion-section").hide();

            // Pantau perubahan pada select#satuan
            $("#satuan").on("change", function() {
                const selectedUnit = $(this).val(); // Ambil nilai yang dipilih
                if (selectedUnit) {
                    // Jika ada nilai yang dipilih, tampilkan conversion-section
                    $("#conversion-section").fadeIn();
                } else {
                    // Jika tidak ada nilai yang dipilih, sembunyikan conversion-section
                    $("#conversion-section").fadeOut();
                }
            });

            $("#modal-button").on("click", function() {
                $("#TambahDataModalLabel").text("{{ __('Add goods') }}");
                $("#item-count").hide();
                $("input[name='nama']").val(null);
                $("input[name='id']").val(null);
                $("input[name='kode']").val(null);
                $("input[name='stock_limit']").val(null);
                $("#GetFile").val(null);
                $("#outputImg").attr("src", "{{ asset('default.png') }}");
                $("select[name='jenisbarang'], select[name='satuan'], select[name='merk'], select[name='supplier']")
                    .val(null).trigger('change'); // Reset Select2
                $("input[name='jumlah']").val(0);
                // $("input[name='harga']").val(null);
                $("#simpan").data('action', 'simpan');
                // id = new Date().getTime();
                // Dapatkan timestamp saat ini
                const now = new Date();

                // Konversi ke timestamp Indonesia (WIB, UTC+7)
                const indonesiaOffset = 7 * 60 * 60 * 1000; // Offset WIB dalam milidetik
                const idTime = new Date(now.getTime() + indonesiaOffset);

                // Format `idTime` sebagai timestamp atau string sesuai kebutuhan
                const id = Math.floor(idTime.getTime() / 1000); // Dalam detik
                $("input[name='kode']").val("BRG-" + id);
            });

            $("#upload-modal-button").on("click", function() {
                $('#uploadModal').modal('show');
            });
            // Event listener untuk mendeteksi perubahan satuan barang
            $("select[name='satuan']").on("change", function() {
                const selectedUnit = $(this).val(); // Ambil nilai satuan barang yang dipilih
                $("select[name='from_unit[]']").val(selectedUnit).trigger(
                    'change'); // Update semua from_unit
                $("select[name='to_unit[]']").val(selectedUnit).trigger('change'); // Update semua to_unit
            });

            // Tambah baris konversi baru
            $("#add-conversion-row").on("click", function() {
                const selectedUnit = $("select[name='satuan']").val() ||
                    ''; // Ambil satuan barang yang dipilih

                const newRow = `
                        <tr>
                        <td>
                            <select name="from_unit[]" class="form-control from-unit" disabled>
                                @foreach ($satuan as $s)
                                    <option value="{{ $s->id }}" ${selectedUnit == {{ $s->id }} ? 'selected' : ''}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="to_unit[]" class="form-control to-unit">
                                @foreach ($satuan as $s)
                                    <option value="{{ $s->id }}" ${selectedUnit == {{ $s->id }} ? 'selected' : ''}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="conversion_factor[]" class="form-control" placeholder="Jumlah">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-row">Hapus</button>
                        </td>
                    </tr>
                    `;
                $("#conversion-rows").append(newRow);
                $('.from-unit, .to-unit').select2({
                    theme: 'bootstrap4',
                    allowClear: true
                });
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
        });



        $(document).on("click", ".ubah", function() {
            let id = $(this).attr('id');
            $("#modal-button").click();
            $("#item-count").show();
            $("#simpan").data('action', 'ubah');
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
                    $("input[name='id']").val(data.id);
                    $("input[name='nama']").val(data.name);
                    $("input[name='stock_limit']").val(data.stock_limit);
                    $("input[name='kode']").val(data.code);
                    $("select[name='jenisbarang']").val(data.category_id).trigger(
                        'change'); // Set nilai dan trigger
                    $("select[name='satuan']").val(data.unit_id).trigger(
                        'change'); // Set nilai dan trigger
                    $("select[name='merk']").val(data.brand_id).trigger(
                        'change'); // Set nilai dan trigger
                    $("select[name='supplier']").val(data.supplier_id).trigger(
                        'change'); // Set nilai dan trigger
                    $("input[name='jumlah']").val(data.quantity);
                    let imageUrl = data.image ? `{{ asset('storage/barang') }}/${data.image}` :
                        `{{ asset('default.png') }}`;
                    $("#outputImg").attr("src", imageUrl); // Set gambar pada elemen img
                    // $("input[name='harga']").val(data.price);
                    // Clear conversion rows and populate with existing data
                    $('#conversion-rows').empty();
                    if (data.conversions && Array.isArray(data.conversions)) {
                        data.conversions.forEach(function(conv) {
                            $('#conversion-rows').append(`
                                <tr>
                                    <td>
                                        <select name="from_unit[]" class="form-control form-unit" disabled>
                                            @foreach ($satuan as $s)
                                                <option value="{{ $s->id }}" ${conv.from_unit_id == {{ $s->id }} ? 'selected' : ''}>
                                                    {{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="to_unit[]" class="form-control to-unit">
                                            @foreach ($satuan as $s)
                                                <option value="{{ $s->id }}" ${conv.to_unit_id == {{ $s->id }} ? 'selected' : ''}>
                                                    {{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="conversion_factor[]" value="${conv.conversion_factor}" class="form-control">
                                    </td>
                                    <td>
                                        <input type="hidden" name="conversion_ids[]" value="${conv.id}">
                                        <button type="button" class="btn btn-sm btn-danger remove-row">{{ __('Hapus') }}</button>
                                    </td>
                                </tr>
                            `);
                        });
                        // Inisialisasi Select2 untuk elemen "to_unit"
                        $('.to-unit').select2({
                            theme: 'bootstrap4',
                            allowClear: true
                        });
                    } else {
                        console.warn("No conversions available");
                    }
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
                title: "{{ __('You are Sure') }} ?",
                text: "{{ __('This data will be deleted') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, Delete') }}",
                cancelButtonText: "{{ __('No, Cancel') }}!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('barang.delete') }}",
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
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('error') || $errors->has('file'))
            Swal.fire({
                icon: 'error',
                title: '{{ session('error') ? 'Gagal' : 'File Tidak Cocok' }}',
                text: '{{ session('error') ?? $errors->first('file') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endsection
