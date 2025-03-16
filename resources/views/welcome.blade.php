@extends('layouts.master')

@section('content')
    <main class="my-12">
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Laudantium, distinctio quae. Ipsum enim, ipsam veniam
            debitis officia nam quo iste qui voluptas incidunt atque, officiis animi! Repellendus praesentium est natus.</p>
        <div x-data="{ count: 0 }">
            <button class="border px-2 mt-2" @click="count++">Click <span x-text="count"></span></button>
        </div>
    </main>
@endsection
