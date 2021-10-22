<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\SeasonPercentage;
use App\Models\ShowPercentage;
use App\Models\TvShow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class EpisodeController extends Controller
{
    public function details ($id, Auth $auth_user, $number) {
        $api_key = $_ENV['TMDB_API_KEY'];
        $show = json_decode(HTTP::get("https://api.themoviedb.org/3/tv/$id?api_key=$api_key")->body());
        $response= HTTP::get("https://api.themoviedb.org/3/tv/$id/season/$number?api_key=$api_key");
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
                ['season_number', '=', $number]
            ])->first();
            if($season_percentage) {
                $percentage = $season_percentage->getPercentage();
            } else {
                $percentage = 0;
            }
        } else {
            $user_episodes = null;
            $percentage = null;
        }
        return view('episodes', ['season' => $season, 'show' => $show, 'tvshow' => $tvshow, 'user_episodes' => $user_episodes, 'percentage' => $percentage]);
    }

    public function findOrCreateTvShow (Auth $auth_user, $tv_id) {
        $user = User::find($auth_user::id());
        if ($user->tvshows()->contains('tv_id', $tv_id) === false) {
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
        $tvshow = TvShow::where([
            ['user_id', '=', $user->id],
            ['tv_id', '=', $tv_id],
        ])->first();

        return $tvshow;
    }

    public function handleEpisodes (Auth $auth_user, $tv_id, $season_number, $episode_number) {
        $tvshow = $this->findOrCreateTvShow($auth_user, $tv_id);
        $episode_number_string = "s${season_number}e$episode_number";
        if ($tvshow->episodes->contains('episode_number', $episode_number_string)) {
            $tvshow->episodes->where('episode_number', $episode_number_string)->first()->delete();
        } else {
            for ($e=1; $e<=$episode_number; $e++) {
                $episode_number_string = "s${season_number}e$e";
                if ($tvshow->episodes->contains('episode_number', $episode_number_string) === false) {
                    $episode = new Episode;
                    $episode->episode_number = $episode_number_string;
                    $episode->tvshow_id = $tvshow->id;
                    $episode->save();
                }
            }
        }
        $show_percentage = ShowPercentage::where('tvshow_id', $tvshow->id)->first();
        $show_percentage->storePercentage();

        $season_percentage = SeasonPercentage::where([
           ['tvshow_id', '=', $tvshow->id],
           ['season_number', '=', $season_number]
        ])->first();
        $season_percentage->storePercentage();
        return redirect()->back();
    }

    public function massChange(Request $request, Auth $auth_user, $tv_id, $season_number) {
        $tvshow = $this->findOrCreateTvShow($auth_user, $tv_id);
        $episodes = [];
        foreach ($tvshow->getSeasonEpisodes($season_number) as $episode) {
            array_push($episodes, $episode->episode_number);
        }
        switch ($request['action']) {
            case 'finish':
                foreach ($episodes as $episode) {
                    $episode_number = "s${season_number}e$episode";
                    if ($tvshow->getEpisodeAirDate($season_number, $episode) > date("Y-m-d") or $tvshow->episodes->contains('episode_number', $episode_number)) {
                    }
                    $episode = new Episode;
                    $episode->episode_number = $episode_number;
                    $episode->tvshow_id = $tvshow->id;
                    $episode->save();
                }
                break;
            case 'unfinish':
                foreach ($episodes as $episode) {
                    $episode_number = "s${season_number}e$episode";
                    if ($tvshow->episodes->contains('episode_number', $episode_number)) {
                        $tvshow->episodes->where('episode_number', $episode_number)->first()->delete();
                    }
                }
                break;
        }
        $show_percentage = ShowPercentage::where('tvshow_id', $tvshow->id)->first();
        $show_percentage->storePercentage();

        $season_percentage = SeasonPercentage::where([
            ['tvshow_id', '=', $tvshow->id],
            ['season_number', '=', $season_number]
        ])->first();
        $season_percentage->storePercentage();

        return redirect()->back();
    }
}
