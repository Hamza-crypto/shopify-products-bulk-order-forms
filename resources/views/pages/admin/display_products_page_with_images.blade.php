<h1 class="h3 mb-3">Upload Shopify Products File</h1>
<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">

            <div class="card-body">
                <form action="{{ route('admin.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3 error-placeholder">
                        <label class="form-label">Choose CSV File</label>
                        <div>
                            <input type="file" class="validation-file" name="file" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Enter Website URL</label>
                        <div>
                            <input type="text" name="domain" class="form-control" placeholder="abc.com">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Upload</button>
                </form>

            </div>
        </div>
    </div>
</div>
