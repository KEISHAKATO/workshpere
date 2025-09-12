<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Post a Job</h2></x-slot>

    <div class="max-w-3xl mx-auto p-4">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form method="POST" action="{{ route('employer.job_posts.store') }}" class="space-y-4">
                    @csrf
                    @include('employer.job_posts.partials.form-fields', ['job' => null])

                    <div class="pt-2">
                        <button class="btn btn-primary">Create</button>
                        <a href="{{ route('employer.job_posts.index') }}" class="btn ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
