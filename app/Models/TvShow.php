<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class TvShow extends Model
{
    use HasFactory;

    protected $table = 'tvshows';
    protected $fillable = [
        'tv_id'
    ];

    function episodes() {
        return $this->hasMany(Episode::class, 'tvshow_id');
    }

    function getTvShow() {
        $api_key = $_ENV['TMDB_API_KEY'];
        $tv_id = $this->tv_id;
        $response = HTTP::get("https://api.themoviedb.org/3/tv/$tv_id?api_key=$api_key");
        $show = json_decode($response->body());
        return $show;
    }

    function getSeasonsNumbers(): array
    {
        $show = $this->getTvShow();
        $seasons = $show->seasons;
        $seasons_numbers = [];
//        foreach ($seasons as $season) {
//            array_push($seasons_numbers, $season->season_number);
//        }
        $season_range = [$seasons[0]->season_number, end($seasons)->season_number];
//        dd($seasons[0]->season_number, end($seasons)->season_number);
        return $season_range;
    }

    function getSeason($season_number) {
        $api_key = $_ENV['TMDB_API_KEY'];
        $tv_id = $this->tv_id;
        $response = HTTP::get("https://api.themoviedb.org/3/tv/$tv_id/season/$season_number?api_key=$api_key");
        $season = json_decode($response->body());
        return $season;
    }

    function getEpisodeAirDate($season_number, $episode_number) {
        $api_key = $_ENV['TMDB_API_KEY'];
        $tv_id = $this->tv_id;
        $response = HTTP::get("https://api.themoviedb.org/3/tv/$tv_id/season/$season_number/episode/$episode_number?api_key=$api_key");
        $episode = json_decode($response->body());
        return $episode->air_date;
    }

    function getTvShowName() {
        $show = $this->getTvShow();
        return $show->name;
    }
    function getTvShowPoster() {
        $show = $this->getTvShow();
        return "https://image.tmdb.org/t/p/original$show->poster_path";
    }

    function getNumberOfEpisodes() {
        $show = $this->getTvShow();
        return $show->number_of_episodes;
    }

    function getWatchedPercentage() {
        return $this->episodes->count() * 100 / $this->getNumberOfEpisodes();
    }

    function getSeasonEpisodes($season_number) {
        return $this->getSeason($season_number)->episodes;
    }

    function getLastSeason() {
        $episodes = [];
        if ($this->episodes->count() !== 0) {
            foreach ($this->episodes as $episode) {
                array_push($episodes, $episode->episode_number);
            }
            rsort($episodes);
            return preg_split('/[se]/', $episodes[0])[1];
        } else {
            return 1;
        }
    }

    function getSeasonNumberOfEpisodes($season_number) {
        $show = $this->getTvShow();
        return $show->seasons[$season_number]->episode_count;
    }

    function getWatchedSeasonEpisodes($season_number) {
        $all_season_episodes = [];
        foreach($this->getSeasonEpisodes($season_number) as $season_episode) {
            array_push($all_season_episodes, $season_episode->episode_number);
        }
        $watched_episodes = $this->episodes->filter( function ($value, $key) use ($all_season_episodes, $season_number) {
           if (preg_split('/[se]/', $value->episode_number)[1] == $season_number) {
               return $value;
           }
        });

        return $watched_episodes;
    }

    function getSeasonPercentage($season_number) {
        return count($this->getWatchedSeasonEpisodes($season_number)) * 100 / count($this->getSeasonEpisodes($season_number));
    }

}
