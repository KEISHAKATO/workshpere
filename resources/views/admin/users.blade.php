<x-app-layout>
    <div class="max-w-5xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-4">Users</h1>
        <table class="w-full border">
            <tr class="bg-gray-100 text-left">
                <th class="p-2">ID</th><th class="p-2">Name</th><th class="p-2">Email</th><th class="p-2">Role</th>
            </tr>
            @foreach($users as $u)
                <tr class="border-t">
                    <td class="p-2">{{ $u->id }}</td>
                    <td class="p-2">{{ $u->name }}</td>
                    <td class="p-2">{{ $u->email }}</td>
                    <td class="p-2">{{ $u->role }}</td>
                </tr>
            @endforeach
        </table>
        <div class="mt-3">{{ $users->links() }}</div>
    </div>
</x-app-layout>
