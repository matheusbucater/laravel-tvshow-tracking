<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a class="text-info bold" href="/show/{{ $show->id }}">{{ $show->name }}</a> - {{ $season->name }}
        </h2>

        <form class="form-inline" action="{{ route('season', $show->id) }}" method="get">
            <select name="season_number" class="form-control pr-5">
                </option>
            @foreach($show->seasons as $show_season)
                    <option value="{{ $show_season->season_number }}" {{ ($show_season->id === $season->id) ? 'selected' : ''}}>
                        {{ $show_season->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary ml-3">Go</button>
        </form>
        @isset($tvshow)
            <div class="progress mx-auto align-center bg-secondary mt-3" style="height: 8px;">
                <div class="progress-bar {{ $percentage  >= 100 ? 'bg-info' : 'bg-success' }}" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        @endisset
    </x-slot>

<div class="py-5">
    <div class="w-75 mx-auto ">
        @include('components.errors')
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-8 lg:-mx-10">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-1 lg:px-2">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">No</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Release Date
                                    </th>
                                    <th scope="col" class=" px-6 py-3 text-right">
                                        <form action='/show/{{ $show->id }}/season/{{ $season->season_number }}' method="POST">
                                            @csrf
                                            <button type="submit" name="action" value="finish"><i class="bi bi-eye-fill"></i></button>
                                            /
                                            <button type="submit" name="action" value="unfinish"><i class="bi bi-eye"></i></button>
                                        </form>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($season->episodes as $episode)
                                    @if($episode->air_date <= date("Y-m-d"))
                                        <tr class="{!! $episode->episode_number % 2 === 0  ? 'bg-gray-100' : 'bg-white'!!}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $episode->episode_number }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $episode->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $episode->air_date }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form action='/show/{{ $show->id }}/season/{{ $season->season_number }}/episode/{{ $episode->episode_number }}' method="POST">
                                                    @csrf
                                                    @if($user_episodes !== null)
                                                        <button type="submit" onclick=" {{ !$user_episodes->contains('episode_number', ("s" . $season->season_number . "e$episode->episode_number")) ? 'return confirm("All previous unwatched episodes will be finished.")' : '' }}"><i class="bi {{ $user_episodes->contains('episode_number', ("s" . $season->season_number . "e$episode->episode_number")) ? 'bi-eye-fill' : 'bi-eye' }} text-muted"></i></button>
                                                    @else
                                                        <button type="submit"><i class="bi bi-eye"></i></button>
                                                    @endif
                                                </form>
                                            </td>
                                        </tr>
                                    @else
                                        <tr class="{!! $episode->episode_number % 2 === 0  ? 'bg-gray-100' : 'bg-white'!!}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-400">{{ $episode->episode_number }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-400">{{ $episode->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                {{ $episode->air_date }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <span class="sr-only">Watched</span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>
