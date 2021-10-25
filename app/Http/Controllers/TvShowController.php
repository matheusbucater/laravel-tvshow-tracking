<?php

namespace App\Http\Controllers;

use App\Models\SeasonPercentage;
use App\Models\ShowPercentage;
use App\Models\TvShow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class TvShowController extends Controller
{

    public function dashboard(Auth $auth_user) {
        $user = User::find($auth_user::id());
        $shows = $user->tvshows;
        $ended_shows = [];
        $watch_next_shows = [];
        $not_started_shows = [];
        foreach ($shows as $show) {
            $show_percentage = ShowPercentage::where('tvshow_id', $show->id)->first()->getPercentage();
            if($show_percentage >= 100) {
                array_push($ended_shows, $show);
            } elseif ($show_percentage > 0) {
                array_push($watch_next_shows, $show);
            } else {
                array_push($not_started_shows, $show);
            }
        }
        return view('dashboard', ['shows' => $shows, 'watch_next_shows' => $watch_next_shows, 'ended_shows' => $ended_shows, 'not_started_shows' => $not_started_shows]);
    }

    public function categories(Auth $auth_user, $show_category) {
        $user = User::find($auth_user::id());
        $shows = $user->tvshows;
        $ended_shows = [];
        $watch_next_shows = [];
        $not_started_shows = [];
        foreach ($shows as $show) {
            $show_percentage = ShowPercentage::where('tvshow_id', $show->id)->first()->getPercentage();
            if($show_percentage >= 100) {
                array_push($ended_shows, $show);
            } elseif ($show_percentage > 0) {
                array_push($watch_next_shows, $show);
            } else {
                array_push($not_started_shows, $show);
            }
        }
        switch ($show_category) {
            case 'watch-next':
                return view('categories', ['category' => 'Watch next', 'category_shows' => $watch_next_shows]);
                break;
            case 'not-started-yet':
                return view('categories', ['category' => 'Not started yet', 'category_shows' => $not_started_shows]);
                break;
            case 'ended':
                return view('categories', ['category' => 'Ended', 'category_shows' => $ended_shows]);
                break;
            default:
                return redirect('/dashboard');
        }
    }

    public function search (Request $request, Auth $auth_user) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }
        $input = $request->input('title');
        $response = HTTP::get('https://api.themoviedb.org/3/search/tv', [
            'api_key' => $_ENV['TMDB_API_KEY'],
            'query' => $input,
        ]);
        $shows = json_decode($response->body());
        $user = User::find($auth_user::id());
        $user_shows = $user->tvshows();
        return view('shows', ['shows' => $shows, 'search_request' => $input, 'user' => $user, 'user_shows' => $user_shows]);
    }

    public function seasons ($id, Auth $auth_user) {
        $api_key = $_ENV['TMDB_API_KEY'];
        $response = HTTP::get("https://api.themoviedb.org/3/tv/$id?api_key=$api_key");
        $show = json_decode($response->body());
        $user = User::find($auth_user::id());
        $tvshow = TvShow::where([
            ['user_id', '=', $user->id],
            ['tv_id', '=', $id]
        ])->first();
        if(!empty($tvshow)) {
            $percentages = SeasonPercentage::where('tvshow_id', $tvshow->id)->get();
        } else {
            $percentages = null;
        }
        return view('seasons', ['show' => $show, 'tvshow' => $tvshow, 'percentages' => $percentages]);
    }

    public function searchSeason (Request $request, $id, Auth $auth_user) {
        $input = $request->input('season_number');
        $api_key = $_ENV['TMDB_API_KEY'];
        $show = json_decode(HTTP::get("https://api.themoviedb.org/3/tv/$id?api_key=$api_key")->body());
        $response= HTTP::get("https://api.themoviedb.org/3/tv/$id/season/$input?api_key=$api_key");
        $season = json_decode($response->body());
        $user = User::find($auth_user::id());
        $tvshow = TvShow::where([
            ['user_id', '=', $user->id],
            ['tv_id', '=', $id]
        ])->first();
        if(!empty($tvshow)) {
            $user_episodes = $tvshow->episodes;
            $season_percentage = SeasonPercentage::where([
                ['tvshow_id', '=', $tvshow->id],
                ['season_number', '=', $input]
            ])->first();
            $percentage = $season_percentage->getPercentage();
        } else {
            $user_episodes = null;
            $percentage = null;
        }
        return view('episodes', ['season' => $season, 'show' => $show, 'user_episodes' => $user_episodes, 'tvshow' => $tvshow, 'percentage' => $percentage]);
    }

    public function addTvShow (Auth $auth_user, $tv_id) {
        $user = User::find($auth_user::id());
        if ($user->tvshows->contains('tv_id', $tv_id)) {
            $user->tvshows->where('tv_id', $tv_id)->first()->delete();
        } else {
            $tvshow = new TvShow;
            $tvshow->tv_id = $tv_id;
            $tvshow->user_id = $user->id;
            $tvshow->save();

            $show_percentage = new ShowPercentage;
            $show_percentage->tvshow_id = $tvshow->id;
            $show_percentage->save();

            $season_percentage = new SeasonPercentage;
            $season_percentage->tvshow_id = $tvshow->id;
            $season_percentage->storeSeasons();
        }
        return redirect()->back();
    }
}
