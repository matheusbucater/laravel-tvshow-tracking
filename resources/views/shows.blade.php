<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Matching results for '{{ $search_request }}'
        </h2>
    </x-slot>

    <div class="pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('components.errors')
        </div>
    </div>
    @if(!empty($shows->results))
        <div class="row px-5 py-4">
            @foreach($shows->results as $show)
                @if($show->poster_path)
                    <div class="col-3">
                                <form action="/show/{{ $show->id }}" class="form-group d-flex justify-content-center" method="POST">
                                @csrf
                                @if($user_shows->contains('tv_id', $show->id))
                                    <button class="btn btn-secondary">Added</button>
                                @else
                                    <button class="btn btn-dark">Add</button>
                                @endif
                            </form>
                            <div class="d-flex justify-content-center text-center mb-5">
                            <a href="/show/{{ $show->id }}" class="text-black w-50 text-break">
                                <img src="https://image.tmdb.org/t/p/original{{ $show->poster_path }}" width=200px height=auto/>
                                @if($user_shows->contains('tv_id', $show->id))
                                    <div class="progress mx-auto align-center bg-secondary -mt-1" style="height: 8px;">
                                        <div class="progress-bar {{ \App\Models\ShowPercentage::where('tvshow_id', $user_shows->where('tv_id', $show->id)->first()->id)->first()->getPercentage() >= 100 ? 'bg-info' : 'bg-success' }}" role="progressbar" style="width: {{ \App\Models\ShowPercentage::where('tvshow_id', $user_shows->where('tv_id', $show->id)->first()->id)->first()->getPercentage() }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endif
                                {{ $show->name }}
                            </a>
                            </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        {{--TODO--}}
        Not Found
    @endif
</x-app-layout>
