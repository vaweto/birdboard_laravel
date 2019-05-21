@extends ('layouts.app')

    @section('content')
        <header class="flex items-center mb-3 py-4">
            <div class="flex justify-between items-end w-full">
                <h2 class="text-muted text-base font-light">My Projects</h2>
                <a class="button" href="/projects/create">Create a project</a>
            </div>
        </header>

        <main class="lg:flex Lf:flex-wrap -mx-3">
            @forelse($projects as $project)
                <div class="lg:w-1/3 px-3 pb-6">
                    @include('projects.card')
                </div>
            @empty
                <div> no projects yet.</div>
            @endforelse
        </main>

    @endsection

