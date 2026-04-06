@extends('template.parent')
@section('title', 'Report')
@section('css')
<style>
    ::placeholder {
        color: blue;
    }
</style>
@endsection
@section('content')
<div class="col-12 card p-2 sticky-top">
    <div class="my-3">
        <button type="button" data-end="{{ now() }}" data-start="{{ now() }}" class="btn btn-sm rounded-pill btn-primary">Today</button>
        <button type="button" data-end="{{ now() }}" data-start="{{ now()->sub(7, 'days') }}" class="btn btn-sm rounded-pill btn-outline-primary">A
            Week</button>
        <button type="button" data-end="{{ now() }}" data-start="{{ now()->sub(30, 'days') }}"
            class="btn btn-sm rounded-pill btn-outline-primary">A Month</button>
        <input type="text" id="time_limit" name="time_limit" placeholder="Custom" class="datepicker btn btn-sm rounded-pill btn-outline-primary" />
        <a href="{{ route('man.report.debug-print') }}" target="_blank" class="btn btn-sm rounded-pill btn-outline-dark">
            <i class='bx bx-test-tube'></i> Debug Print
        </a>
    </div>
</div>
<div class="d-flex flex-wrap gap-1 mt-2 align-items-stretch align-content-stretch">
    @foreach ($reportTemplates as $template)
    <div class="flex-grow-1 col-12 col-md-5 col-lg-3 flex-fill">
        <div class="card h-100">
            <form action="{{ route('man.report.generate-report') }}" method="POST" target="_blank">
                @csrf
                <div class="card-header pt-3">
                    <h5>{{ $template['name'] }}</h5>
                </div>
                <div class="card-body py-1">
                    <div class="text-xs">{{ $template['description'] }}</div>
                    <input type="hidden" name="template" value="{{ $template['template'] }}" />
                    <input type="hidden" name="start" value="{{ now()->format('Y-m-d') }}" />
                    <input type="hidden" name="end" value="{{ now()->format('Y-m-d') }}" />
                    @if ($template['cashier'])
                    <select class="form-control form-control-sm select2-multiple" name="cashier[]" multiple="multiple">
                        @foreach ($cashiers->userByRole as $cashier)
                        <option value="{{ $cashier->user->id }}">{{ $cashier->user->name }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
                <div class="card-footer align-self-end">
                    <button type="submit" class="btn btn-info btn-sm" name="report" value="pdf"><i class='bx bxs-file-export'></i> pdf</button>
                    <button type="submit" class="btn btn-outline-dark btn-sm" name="report" value="thermal"><i class='bx bxs-printer'></i> thermal</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" name="report" value="xlxs"><i class='bx bxs-file-export'></i>
                        xlxs</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
@push('resource-js')
<script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<link rel='stylesheet' type="text/css" href="{{ asset('assets/css/daterangepicker.css') }}">
<script>
    window.dateTimePickerRange = undefined;
    $(function() {
        window.dateTimePickerRange = $('.datepicker').daterangepicker({
            showDropdowns: false,
            opens: 'down',
            locale: {
                format: 'YYYY/MM/DD'
            },
            maxDate: moment(),
        });
        $('.btn.rounded-pill').click(function() {
            $('.btn.rounded-pill').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            if ($(this).data('end') != undefined) {
                $('[name=start]').val(moment($(this).data('start')).format('YYYY-MM-DD'));
                $('[name=end]').val(moment($(this).data('end')).format('YYYY-MM-DD'));
            } else {
                $('#time_limit').on('apply.daterangepicker', function(e, picker) {
                    $('[name=start]').val(picker.startDate.format('YYYY-MM-DD'));
                    $('[name=end]').val(picker.endDate.format('YYYY-MM-DD'));
                })
            }
        });
        $('.select2-multiple').select2({
            placeholder: "Select One",
        });
    });
</script>
@endpush
