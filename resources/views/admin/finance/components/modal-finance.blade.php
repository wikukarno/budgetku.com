<div class="modal fade" id="financeModal" tabindex="-1" aria-labelledby="financeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="financeModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-finance" method="POST">
                    @csrf
                    <input type="hidden" name="id_finance" id="id_finance">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="name">Nama Kategori</label>
                                    <select name="category_finances_id" id="category_finances_id" class="form-select"
                                        required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name_category_finances }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="name">Nama Barang / Item</label>
                                    <input type="text" name="name_item" id="name_item" class="form-control"
                                        placeholder="Makanan" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-4">
                                <div class="form-group">
                                    <label for="name">Harga Barang / Item</label>
                                    <input type="text" name="price" id="price" class="form-control"
                                        placeholder="Rp.100.000" required>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="form-group">
                                    <label for="purchase_date">Tanggal Bayar</label>
                                    <input type="date" name="purchase_date" id="purchase_date" class="form-control"
                                        placeholder="Rp.100.000" required>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="form-group">
                                    <label for="name">Metode Pembayaran</label>
                                    <input type="text" name="purchase_by" id="purchase_by" class="form-control"
                                        placeholder="Kes, gopay, transfer, dll" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-end mt-5">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="btnSaveKeuangan" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>