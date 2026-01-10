<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-foreground">
                {{ __('Welcome back, ') }} {{ Auth::user()->name }}!
            </h1>
        </div>
    </div>
</x-app-layout>
