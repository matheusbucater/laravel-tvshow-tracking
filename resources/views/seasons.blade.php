<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 ">
            {{ $show->name }}
        </h2>
    </x-slot>

    <div class="pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('components.errors')
        </div>
    </div>
    @if(!empty($show->seasons))
        <div class="row px-5 py-4">
            @foreach($show->seasons as $season)
                <div class="col-3">
                    <div class="d-flex justify-content-center text-center mb-5">
                        <a href="/show/{{ $show->id }}/season/{{ $season->season_number }}" class="text-black w-50 text-break">
                            <img src="https://image.tmdb.org/t/p/original{{ $season->poster_path }}" width=200px height=auto/>
                            @isset($tvshow)
                                <div class="progress mx-auto align-center bg-secondary -mt-1" style="height: 8px;">
                                    <div class="progress-bar {{ $percentages->where('season_number', $season->season_number)->first()->percentage >= 100 ? 'bg-info' : 'bg-success' }}" role="progressbar" style="width: {{ $percentages->where('season_number', $season->season_number)->first()->percentage }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            @endisset
                            {{ $season->name }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{--TODO--}}
        Not Found
    @endif
</x-app-layout>
