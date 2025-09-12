<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Edit Job</h2></x-slot>

    <div class="max-w-3xl mx-auto p-4">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form method="POST" action="{{ route('employer.job_posts.update', $job) }}" class="space-y-4">
                    @csrf @method('PUT')

                    @include('employer.job_posts.partials.form-fields', ['job' => $job])

                    <div class="pt-2 flex gap-2">
                        <button class="btn btn-primary">Save</button>
                        <a href="{{ route('employer.job_posts.index') }}" class="btn">Back</a>
                    </div>
                </form>

                <form method="POST" action="{{ route('employer.job_posts.destroy', $job) }}" class="mt-6">
                    @csrf @method('DELETE')
                    <button class="btn btn-error" onclick="return confirm('Delete this job?')">Delete</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
