@extends('template.parent')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
@endpush
@section('content')
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
@endpush
