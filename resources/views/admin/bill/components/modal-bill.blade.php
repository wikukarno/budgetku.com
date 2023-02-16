<div class="modal fade" id="salaryModal" tabindex="-1" aria-labelledby="salaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="salaryModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-salary" method="POST">
                    @csrf
                    <input type="hidden" name="id_salary" id="id_salary">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="salary">Uang Masuk</label>
                                    <input type="text" name="salary" id="salary" class="form-control"
                                        placeholder="Rp.15.000.000" required>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="date">Tanggal</label>
                                    <input type="date" name="date" id="date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="form-group">
                                    <label for="description">Keterangan</label>
                                    <input type="text" name="description" id="description" class="form-control"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-end mt-5">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="btnSaveSalary" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>