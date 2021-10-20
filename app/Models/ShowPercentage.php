<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowPercentage extends Model
{
    use HasFactory;

    protected $table = 'shows_percentages';
    protected $attributes = [
        'percentage' => 0
    ];


    public function storePercentage() {
        $tvshow = TvShow::find($this->tvshow_id);
        $this->percentage = $tvshow->getWatchedPercentage();
        $this->save();
    }

    public function getPercentage() {
        return $this->percentage;
    }

//    public function storeSeasonPercentage($season_number) {
//        $tvshow = TvShow::find($this->tvshow_id);
//        $season_percentage = $tvshow->getSeasonPercentage();
//        $this->season_percentage = "s${season_number}p$season_percentage";
//        $this->save();
//    }
}
