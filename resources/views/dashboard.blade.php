<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if($shows)
        <div class="row px-5 py-4">
            @foreach($shows as $show)
                <div class="col-3">
                    <form action="/show/{{ $show->tv_id }}" class="form-group d-flex justify-content-center" method="POST">
                        @csrf
                        <button class="btn btn-secondary">Added</button>
                    </form>
                    <div class="d-flex justify-content-center text-center mb-5">
                        <a href="/show/{{ $show->tv_id }}/season/{{ $show->getLastSeason() }}" class="text-black w-50 text-break">
                            <img src={{ $show->getTvShowPoster() }} width=200px height=auto/>
                            <div class="progress mx-auto align-center bg-secondary -mt-1" style="height: 8px;">
                                <div class="progress-bar {{ \App\Models\ShowPercentage::where('tvshow_id', $show->id)->first()->getPercentage() >= 100 ? 'bg-info' : 'bg-success' }}" role="progressbar" style="width: {{ \App\Models\ShowPercentage::where('tvshow_id', $show->id)->first()->getPercentage() }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            {{ $show->getTvShowName() }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    <div class="d-flex justify-content-center pt-4">
        {{ $shows->links() }}
    </div>
    @else
        {{--TODO--}}
        Not Found
    @endif
</x-app-layout>
