<div class="card flex flex-col mt-3" >
    <h3 class="font-normal text-xl py-4 -ml-5 mb-3 border-l-4 border-blue-light pl-4">
        Invite a user
    </h3>


    <form method="POST" action="{{$project->path() .'/invitations'}}" class="text-right">
        @csrf
        <div class="mb-3">
            <input type="email" name="email" class="border border-grey-light rounded w-full py-2 px-2" placeholder="email"/>
        </div>

        <button type="submit" class="text-xs button">Invite</button>
    </form>
    @if($errors->invitations->any())
        <div class="field mt-6">
            @foreach($errors->invitations->all() as $error)
                <li class="text-sm text-red">{{$error}}</li>
            @endforeach
        </div>
    @endif
</div>