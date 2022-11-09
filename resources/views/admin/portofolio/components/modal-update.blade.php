<!-- Modal -->
<div class="modal fade" id="updatePortofolioModal" tabindex="-1" aria-labelledby="updatePortofolioModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="updatePortofolioModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <input type="text" id="portofolio">
                    <form id="form-update-portofolio" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="title" class="form-label">Nama Portofolio</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Nama Portofolio">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="kategori" class="form-label">Nama Portofolio</label>
                                    <select class="form-select" name="kategori" aria-label="Default select example">
                                        <option selected>Open this select menu</option>
                                        <option value="Web Design">Web Design</option>
                                        <option value="Web Design / Code">Web Design / Code</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="url" class="form-label">Url</label>
                                    <input type="url" class="form-control" id="url" name="url"
                                        placeholder="Masukan Url">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="url" class="form-label">Thumbnail</label>
                                    <input type="file" class="form-control" id="thumbnail" name="thumbnail"
                                        accept="image/*" onchange="validateSize()">
                                </div>
                            </div>
                        </div>
                        <div class="d-block d-md-flex d-grid gap-2 mt-5">
                            <a href="{{ route('portofolio.index') }}" class="btn btn-danger col-12 col-md-6">Batal</a>
                            <button type="submit" id="btnUpdatePortofolio"
                                class="btn btn-primary col-12 col-md-6">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>