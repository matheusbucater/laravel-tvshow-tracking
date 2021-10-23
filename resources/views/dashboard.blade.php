<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if($shows->count() !== 0)
        @if(count($watch_next_shows) > 0)
            <div class="pl-5 pt-5">
                <div class="pl-5 pb-2 h5">Watch next <span class="text-muted">({{ count($watch_next_shows) }})</span></div>
            </div>
            <div class="row px-5 pt-3">
                @foreach($watch_next_shows as $show)
                    <div class="col-3">
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
                                    </form>
                                </div>
                                <a href="/show/{{ $show->tv_id }}/season/{{ $show->getLastWatchedSeason() }}" class="text-muted">{{ $show->getTvShowName() }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
{{--                TODO--}}
{{--                PAGINA ESPECIFICA PARA CADA CATEGORIA (WATCH NEXT, NOT STARTED YET, ENDED)--}}
{{--                LIMITE DE SERIES DE CADA CATEGORIA MOSTRADAS NA DASHBOARD--}}
{{--                TODO--}}
{{--                QUANTOS EPISODES FALTAM ASSISTIR--}}

            </div>
        @endif
        @if(count($not_started_shows) > 0)
            <div class="pl-5 pt-5">
                <div class="pl-5 pb-2 h5">Not started yet<span class="text-muted">({{ count($not_started_shows) }})</span></div>
            </div>
            <div class="row px-5 pt-3">
                @foreach($not_started_shows as $show)
                    <div class="col-3">
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
                                    </form>
                                </div>
                                <a href="/show/{{ $show->tv_id }}/season/{{ $show->getLastWatchedSeason() }}" class="text-muted">{{ $show->getTvShowName() }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        @if(count($ended_shows) > 0)
                <div class="pl-5 pt-5">
                    <div class="pl-5 pb-2 h5">Ended <span class="text-muted">({{ count($ended_shows) }})</span></div>
                </div>
            <div class="row px-5 pt-3">
                @foreach($ended_shows as $show)
                    <div class="col-3">
                        <form action="/show/{{ $show->tv_id }}" class="form-group d-flex justify-content-center" method="POST">
                            @csrf
                            <button class="btn btn-secondary">Added</button>
                        </form>
                        <div class="d-flex justify-content-center text-center mb-2">
                            <a href="/show/{{ $show->tv_id }}/season/{{ $show->getLastWatchedSeason() }}" class="text-black w-50 text-break">
                                <img src={{ $show->getTvShowPoster() }} width=200px height=auto/>
                                <div class="progress mx-auto align-center bg-secondary -mt-1" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                {{ $show->getTvShowName() }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class=" pt-5 pb-4 align-middle text-center inline-block min-w-full sm:px-6 lg:px-8">
                                <h3>You don't have any added tv shows.</h3>
                                <h4 class="text-muted pb-3">Try adding one.</h4>
                                <div class="d-flex input-group justify-content-center">
                                    <form action="{{ route('search') }}" class="form-inline" method="GET">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="title" placeholder="Search">
                                            <div class="input-group-append">
                                                <button class="input-group-text" type="submit"><i class="bi bi-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
