@extends('layouts.app')

@section('title', 'Upload Shopify Products File')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />

@endsection



@section('content')

    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Images Download Report</h5>
                </div>


                <div class="card-body">
                    @foreach ($files as $file)
                        <h1>{{ $file->filename }}</h1>
                        <div class="progress mb-3">
                            @php
                                $percentage =
                                    $file->total_rows > 0 ? ($file->processed_rows / $file->total_rows) * 100 : 0;
                            @endphp
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                aria-valuemax="100">
                                ({{ round($percentage, 2) }}%)
                            </div>


                        </div>
                        <p>{{ $file->processed_rows }}/{{ $file->total_rows }} ({{ round($percentage, 2) }}%)</p>
                        <hr>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
@endsection
