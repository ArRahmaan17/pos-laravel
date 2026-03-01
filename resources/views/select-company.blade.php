@extends('template.single')
@section('title', 'Login')
@section('content')
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="col-12">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center align-items-middle fs-1 fw-bold">
                            SELECT YOUR COMPANY 
                        </div>
                        <div class="d-flex row company-container justify-content-center gap-2 gap-md-5 mt-5">
                            <div class="card col-12 col-sm-5 col-md-4 col-lg-3" aria-hidden="true">
                                <img draggable="false" class="card-img-top placeholder-glow">
                                <div class="card-body">
                                    <h5 class="card-title placeholder-glow">
                                        <span class="placeholder col-6"></span>
                                    </h5>
                                    <p class="card-text placeholder-glow">
                                        <span class="placeholder col-7"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-6"></span>
                                        <span class="placeholder col-8"></span>
                                    </p>
                                    <a class="btn btn-primary disabled placeholder col-6" aria-disabled="true"></a>
                                </div>
                            </div>
                            <div class="card col-12 col-sm-5 col-md-4 col-lg-3" aria-hidden="true">
                                <img draggable="false" class="card-img-top placeholder-glow">
                                <div class="card-body">
                                    <h5 class="card-title placeholder-glow">
                                        <span class="placeholder col-6"></span>
                                    </h5>
                                    <p class="card-text placeholder-glow">
                                        <span class="placeholder col-7"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-6"></span>
                                        <span class="placeholder col-8"></span>
                                    </p>
                                    <a class="btn btn-primary disabled placeholder col-6" aria-disabled="true"></a>
                                </div>
                            </div>
                            <div class="card col-12 col-sm-5 col-md-4 col-lg-3" aria-hidden="true">
                                <img draggable="false" class="card-img-top placeholder-glow">
                                <div class="card-body">
                                    <h5 class="card-title placeholder-glow">
                                        <span class="placeholder col-6"></span>
                                    </h5>
                                    <p class="card-text placeholder-glow">
                                        <span class="placeholder col-7"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-6"></span>
                                        <span class="placeholder col-8"></span>
                                    </p>
                                    <a class="btn btn-primary disabled placeholder col-6" aria-disabled="true"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   @endsection
@push('resources-js')
    <script>
        $(function() {
            $.ajax({
                type: "GET",
                url: `{{ route('list-company') }}`,
                dataType: "json",
                success: function(response) {
                    const cardContainer = document.querySelector('.company-container');
                    cardContainer.innerHTML = ``;
                    response.data.forEach(item => {
                        const card = `
                               <div class="card mb-4 col-12 col-sm-5 col-md-4 col-lg-3">
                                    <img
                                        src="{{ url('/') }}/${item.picture}"
                                        class="card-img-top w-full"
                                        style="max-height:150px; object-fit: cover;"
                                        alt="${item.name}"
                                    />
                                    <div class="card-body">
                                        <h5 class="card-title fs-3">${item.name}</h5>
                                        <p class="card-text text-truncate" title="${item.type.name}"><strong>Business Type:</strong> ${item.type.name}</p>
                                        <p class="card-text">
                                            <strong>Location:</strong> ${item.address.city}, ${item.address.province}
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <form action="{{ route('select-company') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="${item.id}">
                                            <button class="btn btn-outline-success btn-icon d-block d-sm-inline-block"><i class='mt-1 bx  bxs-finger-up'></i></button>
                                        </form>
                                    </div>
                                </div>`;
                        cardContainer.innerHTML += card;
                    });
                }
            });
        });
    </script>
@endpush
