@extends('layouts.master')

@section('content')
    <section class="my-12">
        <div class="mb-8">
            <h3 class="text-3xl font-semibold">{{ __('Single Product') }}</h3>
        </div>

        <div class="w-full max-w-96">
            <x-product-card :product="$product" />
        </div>
    </section>
@endsection
