<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($category) }}
        </h2>
    </x-slot>

    @if(count($category_shows) > 0)
        <div class="row px-5 pt-5">
            @foreach($category_shows as $show)
                <div class="col-3 pb-3">
                    <form action="/show/{{ $show->tv_id }}" class="form-group d-flex justify-content-center" method="POST">
                        @csrf
                        <button class="btn btn-secondary">Added</button>
                    </form>
                    <div class="d-flex justify-content-center text-center mb-2">
                        <div>
                            <a href="/show/{{ $show->tv_id }}/season/{{ $show->getLastWatchedSeason() }}" class="text-black w-50 text-break">
                                <img src={{ $show->getTvShowPoster() }} width=200px height=auto/>
                                <div class="progress mx-auto align-center bg-secondary -mt-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ \App\Models\ShowPercentage::where('tvshow_id', $show->id)->first()->getPercentage() }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </a>
                            <div class="mt-2">
                                <form action='/show/{{ $show->tv_id }}/season/{{ preg_split('/[se]/', $show->getNextEpisode())[1] }}/episode-unique/{{ preg_split('/[se]/', $show->getNextEpisode())[2] }}' method="POST">
                                    @csrf
                                    {{ $show->getNextEpisode() }}
                                    <button type="submit"><i class="bi bi-eye"></i></button>
                                    <span class="text-muted" style="font-size: 12px">+ {{ $show->getNumberOfEpisodes() - $show->getNumberOfWatchedEpisodes() }} episodes</span>
                                </form>
                            </div>
                            <a href="/show/{{ $show->tv_id }}/season/{{ $show->getLastWatchedSeason() }}" class="text-muted">{{ $show->getTvShowName() }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
