@extends ('layouts.app')

@section('content')
    <form method="POST" action="/projects">
        @csrf
        <h1>Create a Project</h1>
        <div class="field">
            <label class="label" for="title">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" placeholder="Text input">
            </div>
        </div>

        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">Description</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <div class="control">
                        <textarea name="description" class="textarea" placeholder="Description"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="field is-grouped">
            <div class="control">
                <button class="button is-link">Submit</button>
            </div>
            <div class="control">
                <a href="/projects" class="button is-text">Cancel</a>
            </div>
        </div>
    </form>

@endsection
