<div class="modal fade" id="categoryFinanceModal" tabindex="-1" aria-labelledby="categoryFinanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="categoryFinanceModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-tambah-kategori-finance" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id_category_finance" id="id_category_finance">
                    <div class="form-group">
                        <label for="name">Nama Kategori</label>
                        <input type="text" name="name_category_finances" id="name_category_finances"
                            class="form-control" placeholder="Makanan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSaveKategoriKeuangan" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>