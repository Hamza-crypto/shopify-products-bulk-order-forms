<h1 class="h3 mb-3">Download Images</h1>
<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <small>Upload new products file here so that system can download all the images in advance.</small>
            </div>
            <div class="card-body">

                <form action="{{ route('admin.download') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3 error-placeholder">
                        <label class="form-label">Choose CSV File</label>
                        <div>
                            <input type="file" class="validation-file" name="file" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Upload</button>
                </form>

                <form action="/progress" method="GET">
                    <button type="submit" class="btn btn-success mt-3">See Progress</button>
                </form>
            </div>
        </div>
    </div>
</div>
