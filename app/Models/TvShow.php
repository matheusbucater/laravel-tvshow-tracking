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

    function getLastWatchedEpisode() {
        $aux_episodes = [];
        $episodes = [];
        if ($this->episodes->count() !== 0) {
            foreach ($this->episodes as $episode) {
                array_push($aux_episodes, $episode->episode_number);
            }
            sort($aux_episodes);
            $season = end($aux_episodes)[1];
            foreach ($this->episodes as $episode) {
                $episode_number = preg_split('/[se]/', $episode->episode_number)[2];
                array_push($episodes, "s${season}e$episode_number");
            }
            return end($episodes);
        } else {
            return "s1e1";
        }
    }

    function getLastWatchedSeason() {
        if ($this->episodes->count() !== 0) {
            $season = preg_split('/[se]/', $this->getLastWatchedEpisode())[1];
            $season_percentage = SeasonPercentage::where([
                ['tvshow_id', '=', $this->id],
                ['season_number', '=', $season]
            ])->first()->getPercentage();
            if ($season_percentage >= 100) {
                if (ShowPercentage::where('tvshow_id', $this->id)->first()->getPercentage() >= 100) {
                    return $season;
                } else {
                    return $season + 1;
                }
            } else {
                return $season;
            }
        } else {
            return 1;
        }
    }

    function getNextEpisode(): string
    {
        $last_watched_season = preg_split('/[se]/', $this->getLastWatchedEpisode())[1];
        $last_watched_episode = preg_split('/[se]/', $this->getLastWatchedEpisode())[2];
        $season_percentage = SeasonPercentage::where([
            ['tvshow_id', '=', $this->id],
            ['season_number', '=', $last_watched_season]
        ])->first()->getPercentage();
        if ($season_percentage >= 100) {
            return "s" . ($last_watched_season + 1) . "e1";
        } elseif ($season_percentage > 0) {
            return "s" . $last_watched_season . "e" . ($last_watched_episode + 1);
        } else {
            return "s1e1";
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
