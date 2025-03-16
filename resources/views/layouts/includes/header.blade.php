<header class="fixed inset-x-0 top-0 bg-gray-50 w-full h-16 md:h-20">
    <div class="max-w-6xl h-full mx-auto flex flex-col justify-center items-center">
        <div class="grid grid-cols-7 gap-4 w-full">

            <div class="col-span-2 flex flex-col justify-center">
                <x-logo class="text-2xl font-light text-gray-600">
                    Mini<span class="font-semibold text-gray-950">Cart</span>
                </x-logo>
            </div>

            <div class="col-span-3">
                <x-search />
            </div>

            <div class="col-span-2 flex flex-col justify-center items-end">
                <x-cart />
            </div>

        </div>
    </div>
</header>

{{-- Header Spacer --}}
<div class="h-16 md:h-20"></div>
