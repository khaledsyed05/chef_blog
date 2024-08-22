<table class="table">
    <thead>
        <tr>
            <th></th>
            <th>Id</th>
            <th>Name</th>
            <th></th>
        </tr>
        <tbode>
            @forelse ($categories as $category)
                <tr>
                    <td>
                        @if ($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" height="50">
                        @endif
                    </td>
                    <td>{{ $category->id }}</td>
                    <td><a href="{{ route('dashboard.categories.show', $category->id) }}"> {{ $category->name }}</a></td>
                    <td class="d-flex">
                        <a href="{{ route('dashboard.categories.edit', $category->id) }}"
                            class="btn btn-outline-warning">
                            Edit
                        </a>
                        <form action="{{ route('dashboard.categories.destroy', $category->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger" type="submit">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        No Categories Deifned.
                    </td>
                </tr>
            @endforelse
        </tbode>
    </thead>
</table>