<div class="card" style="height: 200px">
    <h3 class="font-normal text-xl py-4 -ml-5 mb-3 border-l-4 border-blue-light pl-4">
        <a href="{{ $project->path() }}">{{ $project->title }}</a>
    </h3>

    <div class="text-gray mb-2">{{ Str::limit($project->description) }}</div>

    <footer>
        <form action="{{ $project->path() }}" method="POST" class="text-right">
            @method('DELETE')
            @csrf

            <button type="submit" class="text-xs">Delete</button>
        </form>
    </footer>
</div>