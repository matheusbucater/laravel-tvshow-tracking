<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if($shows->count() !== 0)
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
