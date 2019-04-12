@extends ('layouts.app')

@section('content')
    <header class="flex items-center mb-3 py-4">
        <div class="flex justify-between items-end w-full">
            <p class="text-grey text-sm font-normal">
                <a href="/projects" class="text-grey text-sm font-normal no-underline"> My projects </a> /  {{$project->title}}
            </p>
            <a class="button" href="/projects/create">Create a project</a>
        </div>
    </header>
    <main>
        <div class="lg:flex -mx-3 mb-6">
            <div class="lg:w-3/4 px-3">
                <div class="mb-8">
                    <h2 class="text-grey text-lg font-normal mb-3">Tasks</h2>
                    @foreach($project->tasks as $task)
                        <div class="card mb-3">
                            <form method="POST" action="{{$task->path()}}">
                                @method('PATCH')
                                @csrf
                                <div class="flex">
                                    <input class="w-full {{ $task->completed ? 'text-grey' : '' }}" name="body" type="text" value="{{$task->body}}">
                                    <input name="completed" type="checkbox" onchange="this.form.submit()" {{$task->completed ? 'checked' : ''}}>
                                </div>
                            </form>
                        </div>
                    @endforeach
                    <div class="card mb-3">
                        <form action="{{ $project->path() . '/tasks' }}" method="POST">
                            @csrf
                            <input placeholder=" begin adding tasks" class="w-full" name="body"/>
                        </form>
                    </div>
                </div>

                <div>
                    <h2 class="text-grey text-lg font-normal mb-3">General Notes</h2>
                    <form method="POST" action="{{$project->path()}}">
                        @method('PATCH')
                        @csrf
                        <textarea
                                name="notes"
                                class="card w-full"
                                style="min-height: 200px"
                                placeholder="General notes here"
                        >{{$project->notes}}</textarea>
                        <button type="submit" class="button mt-1">Save</button>
                    </form>

                </div>
            </div>
            <div class="lg:w-1/4 px-3">
                @include('projects.card')
            </div>
        </div>
    </main>

@endsection

